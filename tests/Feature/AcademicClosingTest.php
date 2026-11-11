<?php

namespace Tests\Feature;

use App\Models\Monograph;
use App\Models\AcademicCommittee;
use App\Models\MonographReview;
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
}
