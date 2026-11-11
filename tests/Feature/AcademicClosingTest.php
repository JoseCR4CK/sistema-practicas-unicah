<?php

namespace Tests\Feature;

use App\Models\Monograph;
use App\Models\AcademicCommittee;
use App\Models\Defense;
use App\Models\Practice;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicClosingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        foreach (glob(database_path('migrations/*.php')) as $file) {
            (require $file)->up();
        }
        $this->seed(DatabaseSeeder::class);
    }

    private function userFor(string $role): User
    {
        return Role::where('name', $role)->firstOrFail()->users()->firstOrFail();
    }

    private function otherTeacher(): Teacher
    {
        $user = User::factory()->create([
            'role_id' => Role::where('name', 'DOCENTE_SEGUIMIENTO')->firstOrFail()->id,
            'status' => 'ACTIVE',
        ]);
        return Teacher::create(['user_id' => $user->id]);
    }

    private function submit(Practice $practice): Monograph
    {
        $this->actingAs($practice->student->user)->post(route('estudiante.monografia.submit'), [
            'practice_id' => $practice->id,
            'title' => 'Informe académico',
            'submission_key' => (string) Str::uuid(),
        ])->assertRedirect();

        return Monograph::where('practice_id', $practice->id)->firstOrFail();
    }

    public function test_monograph_ownership_deadline_and_versions_preserve_other_review_clocks(): void
    {
        $this->travelTo(Carbon::parse('2026-11-01 09:00:00'));
        $practice = Student::where('account_number', '0801-2022-00458')->firstOrFail()->preferredPractice();
        $this->assertSame('2026-11-23', $practice->monographDeadline()->format('Y-m-d'));
        $monograph = $this->submit($practice);
        $first = $monograph->currentVersion;
        $technical = $monograph->currentReviews()->get('tecnica');
        $this->assertSame('2026-11-11 09:00:00', $technical->due_at->format('Y-m-d H:i:s'));
        $style = $monograph->currentReviews()->get('estilo');
        $coordinator = $monograph->currentReviews()->get('coordinador');
        $this->assertNull($style->started_at);
        $this->assertNull($coordinator->started_at);
        $this->actingAs($practice->teacher->user)->post(route('docente.monografia.review', $monograph), [
            'review_id' => $technical->id, 'status' => 'Requiere corrección', 'observation' => 'Precisar resultados',
        ])->assertRedirect();
        $this->travelTo(Carbon::parse('2026-11-03 12:00:00'));
        $monograph = $this->submit($practice);
        $this->assertSame(2, $monograph->versions()->count());
        $this->assertSame('Requiere corrección', $first->fresh()->status);
        $newTechnical = $monograph->currentReviews()->get('tecnica');
        $this->assertSame('2026-11-13 12:00:00', $newTechnical->due_at->format('Y-m-d H:i:s'));
        $this->assertSame($style->id, $monograph->currentReviews()->get('estilo')->id);
        $this->assertNull($style->fresh()->due_at);
        $this->assertNull($coordinator->fresh()->due_at);
        $this->actingAs($practice->teacher->user)->post(route('docente.monografia.review', $monograph), [
            'review_id' => $newTechnical->id, 'status' => 'Aprobada',
        ])->assertRedirect();
        $this->assertSame('2026-11-06 12:00:00', $style->fresh()->due_at->format('Y-m-d H:i:s'));
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.monografia.review', $monograph), [
            'review_id' => $style->id, 'status' => 'Requiere corrección', 'observation' => 'Revisar redacción',
        ])->assertRedirect();
        $technicalClock = $newTechnical->fresh()->due_at->toDateTimeString();
        $this->travelTo(Carbon::parse('2026-11-04 10:00:00'));
        $monograph = $this->submit($practice);
        $newStyle = $monograph->currentReviews()->get('estilo');
        $this->assertSame('2026-11-07 10:00:00', $newStyle->due_at->toDateTimeString());
        $this->assertSame($technicalClock, $newTechnical->fresh()->due_at->toDateTimeString());
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.monografia.review', $monograph), [
            'review_id' => $newStyle->id, 'status' => 'Aprobada',
        ])->assertRedirect();
        $this->assertSame('2026-11-05 10:00:00', $coordinator->fresh()->due_at->toDateTimeString());
        $otherStudent = Student::whereKeyNot($practice->student_id)->firstOrFail();
        $this->actingAs($otherStudent->user)->get(route('estudiante.monografia.show', $monograph))->assertForbidden();
        $this->actingAs($this->otherTeacher()->user)->get(route('docente.monografia.show', $monograph))->assertForbidden();
        $this->actingAs($practice->student->user)->get(route('estudiante.monografia.show', $monograph))->assertOk()->assertSee('Informe académico');
        $this->assertSame('Práctica', $practice->fresh()->stage);
        $practice->update(['end_date' => null]);
        $this->assertNull($practice->fresh()->monographDeadline());
        $this->travelBack();
    }

    public function test_committee_requires_three_distinct_members_and_at_most_one_coordinator(): void
    {
        $practice = Practice::firstOrFail();
        $members = [$practice->teacher, $this->otherTeacher(), $this->otherTeacher()];
        $fields = [
            'practice_id' => $practice->id, 'revision' => 0, 'status' => 'Confirmada',
            'members' => collect($members)->map(fn ($teacher, $index) => [
                'user_id' => $teacher->user_id, 'function' => $index === 0 ? 'Coordinador' : 'Miembro',
            ])->all(),
        ];
        $this->actingAs($this->userFor('COORDINACION'));
        $this->post(route('coordinacion.ternas.store'), array_replace($fields, ['members' => array_slice($fields['members'], 0, 2)]))
            ->assertSessionHasErrors('members');
        $duplicate = $fields;
        $duplicate['members'][2]['user_id'] = $duplicate['members'][1]['user_id'];
        $this->post(route('coordinacion.ternas.store'), $duplicate)->assertSessionHasErrors('members.1.user_id');
        $multipleCoordinators = $fields;
        $multipleCoordinators['members'][1]['function'] = 'Coordinador';
        $this->post(route('coordinacion.ternas.store'), $multipleCoordinators)->assertSessionHasErrors('members');
        $this->post(route('coordinacion.ternas.store'), $fields)->assertRedirect();
        $committee = AcademicCommittee::where('practice_id', $practice->id)->firstOrFail();
        $this->assertSame(3, $committee->members()->count());
        $this->assertSame('Confirmada', $committee->status);
        $this->assertSame($members[0]->user_id, $committee->coordinator()->id);
        $this->assertDatabaseMissing('roles', ['name' => 'COORDINADOR_TERNA']);
        $this->actingAs($practice->student->user)->get(route('estudiante.terna.show', $committee))->assertOk();
        $this->put(route('coordinacion.terna.update', $committee), $fields)->assertForbidden();
        $otherStudent = Student::whereKeyNot($practice->student_id)->firstOrFail();
        $this->actingAs($otherStudent->user)->get(route('estudiante.terna.show', $committee))->assertForbidden();
        $this->actingAs($members[1]->user)->get(route('docente.terna.show', $committee))->assertOk();
        $this->get(route('academico.terna.show', $committee))->assertOk();
        $members[1]->user->update(['status' => 'INACTIVE']);
        $this->get(route('academico.terna.show', $committee))->assertForbidden();
        $members[1]->user->update(['status' => 'ACTIVE']);
        $this->actingAs($this->otherTeacher()->user)->get(route('docente.terna.show', $committee))->assertForbidden();
        $this->actingAs($this->userFor('COORDINACION'))->put(route('coordinacion.terna.update', $committee), $fields)->assertStatus(409);
    }

    public function test_coordinator_is_contextual_and_only_the_correct_review_clock_restarts(): void
    {
        $practice = Student::where('account_number', '0801-2022-00458')->firstOrFail()->preferredPractice();
        $coordinator = $this->otherTeacher();
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.ternas.store'), [
            'practice_id' => $practice->id, 'revision' => 0, 'status' => 'Asignada',
            'members' => [['user_id' => $coordinator->user_id, 'function' => 'Coordinador']],
        ])->assertRedirect();
        $monograph = $this->submit($practice);
        foreach (['tecnica', 'estilo'] as $stage) {
            $review = $monograph->fresh()->currentReviews()->get($stage);
            $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.monografia.review', $monograph), [
                'review_id' => $review->id, 'status' => 'Aprobada',
            ])->assertRedirect();
        }
        $reviews = $monograph->fresh()->currentReviews();
        $originalClock = $reviews->get('estilo')->due_at->toDateTimeString();
        $finalReview = $reviews->get('coordinador');
        $this->actingAs($coordinator->user)->post(route('docente.monografia.review', $monograph), [
            'review_id' => $finalReview->id, 'status' => 'Requiere corrección', 'observation' => 'Completar conclusiones',
        ])->assertRedirect();
        $this->travelTo(now()->addDays(2));
        $monograph = $this->submit($practice);
        $latest = $monograph->currentReviews()->get('coordinador');
        $this->assertSame(now()->addDay()->toDateTimeString(), $latest->due_at->toDateTimeString());
        $this->assertSame($originalClock, $monograph->currentReviews()->get('estilo')->due_at->toDateTimeString());
        $this->actingAs($coordinator->user)->post(route('docente.monografia.review', $monograph), [
            'review_id' => $latest->id, 'status' => 'Aprobada',
        ])->assertRedirect();
        $this->assertSame('Aprobada', $monograph->fresh()->statusLabel());
        $this->assertSame(2, $monograph->versions()->count());
        $this->travelBack();
    }

    private function confirmedCommittee(): AcademicCommittee
    {
        $practice = Student::where('account_number', '0801-2022-00458')->firstOrFail()->preferredPractice();
        $members = [$practice->teacher, $this->otherTeacher(), $this->otherTeacher()];
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.ternas.store'), [
            'practice_id' => $practice->id, 'revision' => 0, 'status' => 'Confirmada',
            'members' => collect($members)->map(fn ($teacher, $index) => [
                'user_id' => $teacher->user_id, 'function' => $index === 0 ? 'Coordinador' : 'Miembro',
            ])->all(),
        ])->assertRedirect();

        return AcademicCommittee::where('practice_id', $practice->id)->with(['members.user', 'practice.student.user'])->firstOrFail();
    }

    private function createDefense(AcademicCommittee $committee): Defense
    {
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.defensas.store'), [
            'academic_committee_id' => $committee->id, 'duration_minutes' => 60,
        ])->assertRedirect();

        return Defense::where('academic_committee_id', $committee->id)->firstOrFail();
    }

    public function test_defense_requires_every_participants_availability_and_never_closes_the_practice(): void
    {
        $committee = $this->confirmedCommittee();
        $defense = $this->createDefense($committee);
        foreach ([60, 90, 120] as $duration) {
            $this->put(route('coordinacion.defensa.duration', $defense), ['duration_minutes' => $duration])->assertRedirect();
            $this->assertSame($duration, $defense->fresh()->duration_minutes);
        }
        $this->put(route('coordinacion.defensa.duration', $defense), ['duration_minutes' => 45])->assertSessionHasErrors('duration_minutes');
        $this->put(route('coordinacion.defensa.duration', $defense), ['duration_minutes' => 60])->assertRedirect();
        $this->post(route('coordinacion.defensa.options', $defense), ['starts_at' => '2026-12-01T12:00'])->assertRedirect();
        $option = $defense->options()->firstOrFail();
        $confirm = route('coordinacion.defensa.confirm', [$defense, $option]);
        $this->post($confirm)->assertStatus(409);
        $participants = $defense->fresh()->participants();
        foreach ($participants as $participant) {
            $this->actingAs($participant)->post(route('academico.defensa.respond', [$defense, $option]), ['available' => 1])->assertRedirect();
        }
        $this->assertSame(4, $option->availabilities()->where('available', true)->count());
        $this->actingAs($committee->practice->student->user)->get(route('estudiante.defensa.show', $defense))->assertOk();
        $this->post($confirm)->assertForbidden();
        $otherStudent = Student::whereKeyNot($committee->practice->student_id)->firstOrFail();
        $this->actingAs($otherStudent->user)->get(route('estudiante.defensa.show', $defense))->assertForbidden();
        $this->post(route('academico.defensa.respond', [$defense, $option]), ['available' => 1])->assertForbidden();
        $this->actingAs($this->otherTeacher()->user)->get(route('docente.defensa.show', $defense))->assertForbidden();
        $this->actingAs($committee->members->first()->user)->get(route('docente.defensa.show', $defense))->assertOk();
        $this->actingAs($this->userFor('COORDINACION'))->post($confirm)->assertRedirect();
        $this->assertSame('Programada', $defense->fresh()->status);
        $this->assertSame($option->id, $defense->fresh()->selected_option_id);
        $this->assertSame('Práctica', $committee->practice->fresh()->stage);
        $this->assertSame('ACTIVE', $committee->practice->fresh()->status);
        $this->put(route('coordinacion.terna.update', $committee), [
            'revision' => $committee->revision, 'status' => 'Confirmada',
            'members' => $committee->members->map(fn ($member) => ['user_id' => $member->user_id, 'function' => $member->function])->all(),
        ])->assertStatus(409);
        $this->travelTo(Carbon::parse('2026-12-01 14:00'));
        $this->post(route('coordinacion.defensa.performed', $defense), ['observation' => 'Actividad realizada'])->assertRedirect();
        $this->assertSame('Realizada', $defense->fresh()->status);
        $this->assertSame('ACTIVE', $committee->practice->fresh()->status);
        $this->travelBack();
    }

    public function test_only_known_class_hours_block_scheduling_and_the_last_academic_block_is_respected(): void
    {
        $committee = $this->confirmedCommittee();
        $defense = $this->createDefense($committee);
        $teacher = $committee->members->first()->user;
        $this->actingAs($teacher)->post(route('academico.defensa.classes', $defense), [
            'starts_at' => '2026-12-01T15:00', 'ends_at' => '2026-12-01T16:00', 'course_name' => 'Asignatura registrada',
        ])->assertRedirect();
        $this->assertDatabaseCount('defense_class_blocks', 1);
        $this->actingAs($this->userFor('COORDINACION'));
        $this->post(route('coordinacion.defensa.options', $defense), ['starts_at' => '2026-12-01T15:30'])->assertSessionHasErrors('starts_at');
        $this->post(route('coordinacion.defensa.options', $defense), ['starts_at' => '2026-12-01T12:00'])->assertRedirect();
        $option = $defense->options()->firstOrFail();
        foreach ($defense->participants() as $participant) {
            $this->actingAs($participant)->post(route('academico.defensa.respond', [$defense, $option]), ['available' => 1])->assertRedirect();
        }
        $this->actingAs($teacher)->post(route('academico.defensa.classes', $defense), [
            'starts_at' => '2026-12-01T12:30', 'ends_at' => '2026-12-01T13:30',
        ])->assertRedirect();
        $this->post(route('academico.defensa.respond', [$defense, $option]), ['available' => 1])->assertSessionHasErrors('available');
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.defensa.confirm', [$defense, $option]))->assertSessionHasErrors('option');
        $this->get(route('coordinacion.defensa.show', $defense))->assertOk()->assertSee('La opción coincide con horas de clase registradas.');
        $this->post(route('coordinacion.defensa.options', $defense), ['starts_at' => '2026-12-01T16:00'])->assertRedirect();
        $this->post(route('coordinacion.defensa.options', $defense), ['starts_at' => '2026-12-01T20:50'])->assertRedirect();
        $this->post(route('coordinacion.defensa.options', $defense), ['starts_at' => '2026-12-01T21:00'])->assertSessionHasErrors('starts_at');
        $this->assertSame('En coordinación', $defense->fresh()->status);
    }

    public function test_changed_committee_invalidates_old_options_without_changing_existing_practice_states(): void
    {
        $committee = $this->confirmedCommittee();
        $defense = $this->createDefense($committee);
        $this->post(route('coordinacion.defensa.options', $defense), ['starts_at' => '2026-12-01T09:00'])->assertRedirect();
        $option = $defense->options()->firstOrFail();
        $members = $committee->members->map(fn ($member) => ['user_id' => $member->user_id, 'function' => $member->function])->all();
        $replacement = $this->otherTeacher();
        $members[2]['user_id'] = $replacement->user_id;
        $this->put(route('coordinacion.terna.update', $committee), [
            'revision' => $committee->revision, 'status' => 'Confirmada', 'members' => $members,
        ])->assertRedirect();
        $this->actingAs($replacement->user)->post(route('academico.defensa.respond', [$defense, $option]), ['available' => 1])->assertStatus(409);
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.defensa.confirm', [$defense, $option]))->assertStatus(409);
        $this->assertSame('En coordinación', $defense->fresh()->status);
        $this->assertSame('Práctica', $committee->practice->fresh()->stage);
    }

    public function test_closing_views_and_summary_use_persistent_records_and_missing_ids_return_404(): void
    {
        $committee = $this->confirmedCommittee();
        $defense = $this->createDefense($committee);
        $monograph = $this->submit($committee->practice);
        $this->actingAs($this->userFor('COORDINACION'));
        foreach (['coordinacion.monografias', 'coordinacion.ternas', 'coordinacion.defensas'] as $route) {
            $this->get(route($route))->assertOk()->assertSee($committee->practice->student->user->name);
        }
        foreach (['coordinacion.monografia.show', 'coordinacion.terna.show', 'coordinacion.defensa.show'] as $route) {
            $this->get(route($route, 999999))->assertNotFound();
        }
        $this->get(route('coordinacion.estudiante', $committee->practice->student_id))->assertOk()
            ->assertSee(route('coordinacion.monografia.show', $monograph))
            ->assertSee(route('coordinacion.terna.show', $committee))
            ->assertSee(route('coordinacion.defensa.show', $defense));
        $this->get(route('coordinacion.practica', $committee->practice))->assertOk()->assertSee('En revisión');
        $this->actingAs($committee->practice->student->user);
        $this->get(route('estudiante.monografia.show', $monograph))->assertOk()->assertSee('Informe académico');
        $this->get(route('estudiante.terna.show', $committee))->assertOk()->assertSee('Confirmada');
        $this->get(route('estudiante.defensa.show', $defense))->assertOk()->assertSee('Pendiente');
        $this->get(route('estudiante.practica'))->assertOk()->assertSee('23/11/2026');
        $this->actingAs($committee->practice->teacher->user)
            ->get(route('docente.practicante', $committee->practice->student_id))
            ->assertOk()->assertSee('En revisión');
    }

    public function test_closing_seed_is_idempotent_preserves_assignments_and_keeps_incoherent_processes_empty(): void
    {
        $monograph = Monograph::firstOrFail();
        $committee = AcademicCommittee::where('practice_id', $monograph->practice_id)->firstOrFail();
        $committee->members()->create(['user_id' => $monograph->practice->teacher->user_id, 'function' => 'Miembro']);
        $committee->update(['status' => 'Asignada', 'revision' => 1]);
        $monograph->currentVersion->update(['title' => 'Título actualizado']);
        $this->seed(\Database\Seeders\AcademicClosingSeeder::class);
        $this->assertSame(1, Monograph::count());
        $this->assertSame('Título actualizado', $monograph->fresh()->currentVersion->title);
        $this->assertSame(1, $committee->members()->count());
        $this->assertSame('Asignada', $committee->fresh()->status);
        $this->assertSame(0, Defense::count());
        $this->assertDatabaseCount('teachers', 1);
        $sofia = Student::where('account_number', '0801-2020-00118')->firstOrFail();
        $this->assertFalse($sofia->preferredPractice()->monograph()->exists());
    }
}
