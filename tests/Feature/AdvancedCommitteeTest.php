<?php

namespace Tests\Feature;

use App\Models\AcademicCareer;
use App\Models\AcademicCommittee;
use App\Models\CommitteeMember;
use App\Models\Defense;
use App\Models\Monograph;
use App\Models\Practice;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use App\Services\CommitteeAssignment;
use App\Services\CommitteeWorkload;
use App\Services\DefenseScheduling;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdvancedCommitteeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        foreach (glob(database_path('migrations/*.php')) as $file) {
            (require $file)->up();
        }
        $this->seed(DatabaseSeeder::class);
    }

    private function manager(): User
    {
        return Role::where('name', 'COORDINACION')->firstOrFail()->users()->firstOrFail();
    }

    private function teacher(string $faculty): Teacher
    {
        $area = AcademicCareer::where('name', $faculty)->firstOrFail();
        $user = User::factory()->create(['status' => 'ACTIVE', 'role_id' => Role::where('name', 'DOCENTE_SEGUIMIENTO')->value('id')]);
        $teacher = Teacher::create(['user_id' => $user->id, 'access_approved_at' => now()]);
        $teacher->faculties()->attach($area->id, ['is_primary' => true]);

        return $teacher;
    }

    private function fields(): array
    {
        return ['revision' => 0, 'status' => 'Activa', 'reason' => 'Asignación interdisciplinaria registrada', 'members' => collect(['Derecho', 'Psicología', 'Generales'])->map(function ($faculty, $index) {
            $teacher = $this->teacher($faculty);

            return ['user_id' => $teacher->user_id, 'function' => AcademicCommittee::FUNCTIONS[$index], 'academic_career_id' => $teacher->faculties()->first()->id];
        })->all()];
    }

    private function assign(?Practice $practice = null, ?array $fields = null): AcademicCommittee
    {
        return app(CommitteeAssignment::class)->save($practice ?? Practice::firstOrFail(), $this->manager(), $fields ?? $this->fields());
    }

    private function lifecycle(AcademicCommittee $committee, array $fields)
    {
        return $this->actingAs($this->manager())->post(route('coordinacion.terna.gestion', $committee), array_replace(['revision' => $committee->revision, 'round' => $committee->defense->scheduling_round, 'reason' => 'Resultado académico verificado'], $fields));
    }

    public function test_interdisciplinary_assignment_from_first_day_and_server_validation(): void
    {
        $practice = Practice::firstOrFail();
        $practice->update(['stage' => 'Solicitud', 'start_date' => today(), 'academic_period' => 'III', 'academic_year' => 2025]);
        $practice->student->update(['academic_career_id' => AcademicCareer::where('name', 'Derecho')->value('id')]);
        $fields = $this->fields();
        $this->actingAs($this->manager());
        $this->post(route('coordinacion.ternas.store'), $fields + ['practice_id' => $practice->id])->assertRedirect();
        $committee = $practice->fresh()->committee;
        $this->assertSame(3, $committee->members()->count());
        $this->assertEqualsCanonicalizing(AcademicCommittee::FUNCTIONS, $committee->members->pluck('function')->all());
        $this->assertSame(3, $committee->members->pluck('academic_career_id')->unique()->count());
        $this->assertSame($committee->members->firstWhere('function', AcademicCommittee::COORDINATOR)->teacher_id, $practice->fresh()->teacher_id);
        $this->assertSame('Solicitud', $practice->fresh()->stage);
        $this->get(route('coordinacion.ternas', ['faculty' => $practice->student->academic_career_id, 'period' => 'III', 'year' => 2025, 'status' => 'active']))->assertOk()->assertViewHas('practicas', fn ($rows) => $rows->count() === 1);
        $this->get(route('coordinacion.ternas', ['faculty' => AcademicCareer::where('name', 'Psicología')->value('id'), 'period' => 'III', 'year' => 2025]))->assertOk()->assertViewHas('practicas', fn ($rows) => $rows->isEmpty());
        $this->get(route('coordinacion.terna.show', $committee))->assertOk()->assertSee('Coordinador de terna')->assertSee('committee-assignment.js');
        $fields['revision'] = $committee->revision;
        $fields['members'][1]['user_id'] = $fields['members'][0]['user_id'];
        $this->put(route('coordinacion.terna.update', $committee), $fields)->assertSessionHasErrors();
        $this->assertSame(3, $committee->members()->count());
        $this->assertDatabaseMissing('roles', ['name' => 'COORDINADOR_TERNA']);
    }

    public function test_ineligible_and_manipulated_faculty_ids_cannot_be_assigned(): void
    {
        $practice = Practice::firstOrFail();
        $fields = $this->fields();
        $teacher = Teacher::where('user_id', $fields['members'][1]['user_id'])->firstOrFail();
        $this->actingAs($this->manager());
        $teacher->update(['access_approved_at' => null]);
        $this->post(route('coordinacion.ternas.store'), $fields + ['practice_id' => $practice->id])->assertSessionHasErrors('members.1.user_id');
        $teacher->update(['access_approved_at' => now()]);
        $teacher->user->update(['status' => 'INACTIVE']);
        $this->post(route('coordinacion.ternas.store'), $fields + ['practice_id' => $practice->id])->assertSessionHasErrors('members.1.user_id');
        $teacher->user->update(['status' => 'ACTIVE']);
        $fields['members'][1]['academic_career_id'] = $fields['members'][0]['academic_career_id'];
        $this->post(route('coordinacion.ternas.store'), $fields + ['practice_id' => $practice->id])->assertSessionHasErrors('members.1.user_id');
        $fields['members'][1]['user_id'] = 999999;
        $this->post(route('coordinacion.ternas.store'), $fields + ['practice_id' => $practice->id])->assertSessionHasErrors('members.1.user_id');
        $this->assertSame(0, CommitteeMember::count());
    }

    public function test_substitution_preserves_history_updates_loads_and_only_unresolved_review_responsibles(): void
    {
        $monograph = Monograph::firstOrFail();
        $committee = $this->assign($monograph->practice);
        $review = $monograph->currentReviews()->get('tecnica')->fresh();
        $clock = $review->due_at->toDateTimeString();
        $old = $committee->members->firstWhere('function', AcademicCommittee::TECHNICAL);
        $this->assertSame($old->user_id, $review->reviewer_id);
        $replacement = $this->teacher('Psicología');
        $fields = ['revision' => $committee->revision, 'status' => 'Activa', 'members' => $committee->members->map(fn ($m) => $m->only(['user_id', 'function', 'academic_career_id']))->all()];
        foreach ($fields['members'] as &$member) {
            if ($member['function'] === AcademicCommittee::TECHNICAL) {
                $member['user_id'] = $replacement->user_id;
            }
        }
        unset($member);
        $this->actingAs($this->manager())->put(route('coordinacion.terna.update', $committee), $fields)->assertSessionHasErrors('reason');
        $fields['reason'] = 'Cambio de responsable técnico';
        $this->put(route('coordinacion.terna.update', $committee), $fields)->assertRedirect();
        $committee->refresh();
        $loads = app(CommitteeWorkload::class)->active();
        $this->assertArrayNotHasKey($old->user_id, $loads);
        $this->assertSame(1, $loads[$replacement->user_id]['total']);
        $this->assertSame(4, $committee->membershipHistory()->count());
        $this->assertNotNull($old->fresh()->ended_at);
        $this->assertSame($replacement->user_id, $review->fresh()->reviewer_id);
        $this->assertSame($clock, $review->fresh()->due_at->toDateTimeString());
        $event = $committee->events()->where('action', 'Modificación de integrantes o funciones')->firstOrFail();
        $this->assertSame($this->manager()->id, $event->actor_id);
        $this->assertNotNull($event->occurred_at);
        $this->assertSame($old->user_id, collect($event->before['members'])->firstWhere('function', AcademicCommittee::TECHNICAL)['user_id']);
        $this->assertSame($replacement->user_id, collect($event->after['members'])->firstWhere('function', AcademicCommittee::TECHNICAL)['user_id']);
        $this->actingAs($old->user)->post(route('docente.monografia.review', $monograph), ['review_id' => $review->id, 'status' => 'Aprobada'])->assertForbidden();
        $this->actingAs($replacement->user)->post(route('docente.monografia.review', $monograph), ['review_id' => $review->id, 'status' => 'Aprobada'])->assertRedirect();
        $this->actingAs($this->manager())->get(route('coordinacion.ternas.carga'))->assertOk()->assertSee($old->user->name)->assertSee('Historial');
    }

    public function test_origin_period_load_formal_close_and_failed_defense_retry_preserve_same_committee(): void
    {
        $practice = Practice::firstOrFail();
        $practice->update(['academic_period' => 'III', 'academic_year' => 2025]);
        $committee = $this->assign($practice);
        $user = $committee->members->first()->user_id;
        $defense = Defense::create(['academic_committee_id' => $committee->id, 'duration_minutes' => 60, 'status' => 'Realizada', 'performed_at' => now(), 'observation' => 'Observación de la primera ronda']);
        $committee->refresh();
        $this->assertSame(1, app(CommitteeWorkload::class)->active()[$user]['periods']['III · 2025']);
        $practice->update(['academic_period' => 'I', 'academic_year' => 2026]);
        $this->lifecycle($committee, ['action' => 'close', 'final_records_verified' => 1])->assertStatus(422);
        $this->lifecycle($committee, ['action' => 'result', 'result' => 'Reprobada'])->assertRedirect();
        $this->assertSame(1, app(CommitteeWorkload::class)->active()[$user]['total']);
        $this->assertFalse($committee->fresh()->isClosed());
        $this->lifecycle($committee, ['action' => 'retry'])->assertRedirect();
        $this->assertSame(2, $defense->fresh()->scheduling_round);
        $this->assertSame($committee->id, $defense->academic_committee_id);
        $this->assertSame(3, $committee->members()->count());
        $event = $committee->events()->where('action', 'Nueva ronda con la misma terna')->firstOrFail();
        $this->assertSame('Reprobada', $event->before['defense']['result']);
        $this->assertSame('Observación de la primera ronda', $event->before['defense']['observation']);
        $this->assertNull($defense->fresh()->observation);
        $defense->refresh()->update(['status' => 'Realizada', 'performed_at' => now()]);
        $committee->refresh();
        $this->lifecycle($committee, ['action' => 'result', 'result' => 'Aprobada'])->assertRedirect();
        $this->lifecycle($committee, ['action' => 'close', 'final_records_verified' => 0])->assertStatus(422);
        $this->lifecycle($committee, ['action' => 'close', 'final_records_verified' => 1])->assertRedirect();
        $this->assertTrue($committee->fresh()->isClosed());
        $this->assertArrayNotHasKey($user, app(CommitteeWorkload::class)->active());
        $this->assertSame(3, $committee->members()->count());
        $this->assertSame('ACTIVE', $practice->fresh()->status);
        $this->get(route('coordinacion.terna.show', $committee))->assertOk()->assertSee('Cerrada')->assertSee('Historial');
        $next = $practice->replicate();
        $next->save();
        $new = $this->assign($next);
        $this->assertNotEquals($committee->id, $new->id);
        $this->assertSame($practice->student_id, $new->practice->student_id);
    }

    public function test_global_workload_and_mutations_are_restricted_to_academic_managers(): void
    {
        $committee = $this->assign();
        foreach ([$committee->practice->student->user, $committee->members->first()->user] as $user) {
            $this->actingAs($user)->get(route('coordinacion.ternas.carga'))->assertForbidden();
            $this->put(route('coordinacion.terna.update', $committee), [])->assertForbidden();
            $this->post(route('coordinacion.terna.gestion', $committee), [])->assertForbidden();
        }
        $other = $this->teacher('Derecho')->user;
        $this->actingAs($other)->get(route('docente.terna.show', $committee))->assertForbidden();
        $this->actingAs($this->manager())->get(route('coordinacion.terna.show', 999999))->assertNotFound();
    }

    public function test_totals_group_by_origin_across_periods_and_role_swaps_keep_load_without_limit(): void
    {
        $first = Practice::firstOrFail();
        $first->update(['academic_period' => 'III', 'academic_year' => 2025]);
        $fields = $this->fields();
        $committee = $this->assign($first, $fields);
        $next = $first->replicate();
        $next->academic_period = 'I';
        $next->academic_year = 2026;
        $next->save();
        $other = $this->assign($next, $fields);
        $user = $fields['members'][0]['user_id'];
        $loads = app(CommitteeWorkload::class)->active();
        $this->assertSame(2, $loads[$user]['total']);
        $this->assertSame(['III · 2025' => 1, 'I · 2026' => 1], $loads[$user]['periods']);
        $fields['revision'] = $committee->revision;
        [$fields['members'][0]['user_id'],$fields['members'][1]['user_id']] = [$fields['members'][1]['user_id'], $fields['members'][0]['user_id']];
        [$fields['members'][0]['academic_career_id'],$fields['members'][1]['academic_career_id']] = [$fields['members'][1]['academic_career_id'], $fields['members'][0]['academic_career_id']];
        $this->actingAs($this->manager())->put(route('coordinacion.terna.update', $committee), $fields)->assertRedirect();
        $this->assertSame(2, app(CommitteeWorkload::class)->active()[$user]['total']);
        $this->assertSame(5, $committee->membershipHistory()->count());
        $this->assertSame(Teacher::where('user_id', $fields['members'][0]['user_id'])->value('id'), $first->fresh()->teacher_id);
        $this->put(route('coordinacion.terna.update', $committee), ['revision' => $committee->fresh()->revision, 'status' => 'Pendiente de asignación', 'reason' => 'Intento de retirada'])->assertSessionHasErrors('members');
        $this->assertSame(3, $committee->members()->count());
    }

    public function test_new_round_preserves_old_options_and_requires_fresh_participant_agreement(): void
    {
        $committee = $this->assign();
        $workflow = app(DefenseScheduling::class);
        $defense = $workflow->create($committee, 60);
        $old = $workflow->propose($defense, $this->manager(), '2026-12-01T10:00');
        $defense->refresh()->update(['status' => 'Realizada', 'performed_at' => now()]);
        $committee->refresh();
        $this->lifecycle($committee, ['action' => 'result', 'result' => 'Reprobada'])->assertRedirect();
        $this->lifecycle($committee, ['action' => 'retry'])->assertRedirect();
        $defense->refresh();
        $new = $workflow->propose($defense, $this->manager(), '2026-12-01T10:00');
        $this->assertNotEquals($old->id, $new->id);
        $this->assertSame(2, $defense->options()->count());
        $this->actingAs($committee->members->first()->user)->post(route('academico.defensa.respond', [$defense, $old]), ['available' => 1])->assertStatus(409);
        $this->actingAs($this->manager())->post(route('coordinacion.defensa.confirm', [$defense, $new]))->assertStatus(409);
        foreach ($defense->participants() as $participant) {
            $this->actingAs($participant)->post(route('academico.defensa.respond',[$defense, $new]),['available' => 1])->assertRedirect();
        }
        $this->actingAs($this->manager())->post(route('coordinacion.defensa.confirm',[$defense, $new]))->assertRedirect();
        $this->assertSame('Programada',$defense->fresh()->status);
    }
}
