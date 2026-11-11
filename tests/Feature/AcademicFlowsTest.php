<?php

namespace Tests\Feature;

use App\Models\AcademicDocument;
use App\Models\DocumentVersion;
use App\Models\PracticeRequest;
use App\Models\Practice;
use App\Models\Student;
use App\Models\Role;
use App\Models\User;
use App\Models\Visit;
use App\Models\Teacher;
use App\Models\Followup;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcademicFlowsTest extends TestCase
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

    public function test_request_access_and_review_persist_without_changing_the_practice_stage(): void
    {
        $application = PracticeRequest::firstOrFail();
        $show = route('coordinacion.solicitud', $application);
        $review = route('coordinacion.solicitud.review', $application);
        $this->get($show)->assertRedirect(route('login'));
        foreach (['ESTUDIANTE', 'DOCENTE_SEGUIMIENTO', 'JEFE_INMEDIATO'] as $role) {
            $this->actingAs($this->userFor($role))->get($show)->assertForbidden();
            $this->post($review, ['status' => 'Aprobada'])->assertForbidden();
        }
        $this->actingAs($this->userFor('COORDINACION'));
        $stage = $application->practice->stage;
        $this->get(route('coordinacion.solicitudes'))->assertOk()->assertSee($application->student->user->name);
        $this->get($show)->assertOk();
        $this->get(route('coordinacion.solicitud', 999999))->assertNotFound();
        $this->post($review, ['status' => 'Aprobada'])->assertRedirect($show);
        $this->assertSame('Aprobada', $application->fresh()->status);
        $this->assertSame($stage, $application->practice->fresh()->stage);
        $this->post($review, ['status' => 'Aprobada'])->assertStatus(409);
        $this->get($show)->assertSee('Aprobada');
        $this->get(route('coordinacion.solicitudes'))->assertSee('Aprobada');
    }

    public function test_rejection_records_the_reason_and_restarts_the_unspecified_deadline_once(): void
    {
        $this->actingAs($this->userFor('COORDINACION'));
        $application = PracticeRequest::firstOrFail();
        $url = route('coordinacion.solicitud.review', $application);
        $this->post($url, ['status' => 'Rechazada'])->assertSessionHasErrors('observation');
        $this->post($url, ['status' => 'Rechazada', 'observation' => 'Revisar información'])
            ->assertRedirect(route('coordinacion.solicitud', $application));
        $this->assertSame('Revisar información', $application->fresh()->observation);
        $this->assertNotNull($application->fresh()->deadline_restarted_at);
        $this->post($url, ['status' => 'Rechazada', 'observation' => 'Otra razón'])->assertStatus(409);
        $this->assertSame('Revisar información', $application->fresh()->observation);
    }

    public function test_document_review_versions_badge_and_student_ownership(): void
    {
        $document = AcademicDocument::with('currentVersion', 'practice.student.user')->firstOrFail();
        $version = $document->currentVersion;
        $owner = $document->practice->student->user;
        $review = route('coordinacion.documento.review', $document);
        $this->get(route('coordinacion.documentos'))->assertRedirect(route('login'));
        foreach (['ESTUDIANTE', 'DOCENTE_SEGUIMIENTO', 'JEFE_INMEDIATO'] as $role) {
            $this->actingAs($this->userFor($role))->post($review, ['version_id' => $version->id, 'status' => 'Aprobado'])->assertForbidden();
        }
        $this->actingAs($this->userFor('COORDINACION'));
        $total = AcademicDocument::count();
        $response = $this->get(route('coordinacion.documentos'))->assertOk();
        $this->assertMatchesRegularExpression('/id="sidebarPendingDocuments"\s*>\s*'.$total.'\s*</', $response->getContent());
        $this->get(route('coordinacion.documento', $document))->assertOk()->assertSee($owner->name);
        $this->get(route('coordinacion.documento', 999999))->assertNotFound();
        $this->post($review, ['version_id' => $version->id, 'status' => DocumentVersion::CORRECTION, 'observation' => 'Corregir metadatos'])->assertRedirect();
        $this->assertSame('Corregir metadatos', $version->fresh()->observation);
        $this->actingAs($owner);
        $this->get(route('estudiante.documentos'))->assertOk()->assertSee('Requiere corrección');
        $this->post(route('estudiante.documentos.store'), [
            'practice_id' => $document->practice_id, 'type' => $document->type, 'submission_note' => 'Segunda versión',
        ])->assertRedirect(route('estudiante.documento', $document));
        $this->assertSame(2, $document->versions()->count());
        $this->assertSame('Corregir metadatos', $version->fresh()->observation);
        $newVersion = $document->fresh()->currentVersion;
        $this->assertSame(2, $newVersion->version);
        $other = AcademicDocument::whereHas('practice', fn ($query) => $query->where('student_id', '!=', $document->practice->student_id))->firstOrFail();
        $this->get(route('estudiante.documento', $other))->assertForbidden();
        $this->post(route('estudiante.documentos.store'), ['practice_id' => $other->practice_id, 'type' => $other->type])->assertNotFound();
        $this->actingAs($this->userFor('COORDINACION'));
        $this->post($review, ['version_id' => $version->id, 'status' => 'Aprobado'])->assertStatus(409);
        $this->post($review, ['version_id' => $newVersion->id, 'status' => 'Aprobado'])->assertRedirect();
        $this->assertSame('Aprobado', $newVersion->fresh()->status);
        $response = $this->get(route('coordinacion.documentos'))->assertOk()->assertSee('Aprobado');
        $this->assertMatchesRegularExpression('/id="sidebarPendingDocuments"\s*>\s*'.($total - 1).'\s*</', $response->getContent());
        $this->get(route('coordinacion.documento', $document))->assertOk()->assertSee('Aprobado');
        $this->assertSame('Requiere corrección', $version->fresh()->status);
    }

    public function test_visit_day_44_45_46_permissions_and_completion_shared_with_coordination(): void
    {
        $visit = Visit::with('practice.teacher.user')->firstOrFail();
        $visit->practice->update(['start_date' => '2026-09-01']);
        $teacher = $visit->practice->teacher->user;
        $this->actingAs($teacher);
        $url = route('docente.visita', $visit);
        foreach (['2026-10-14' => 'Pendiente', '2026-10-15' => 'Habilitada', '2026-10-16' => 'Habilitada'] as $date => $status) {
            $this->travelTo(Carbon::parse($date));
            $this->get($url)->assertOk()->assertSee($status);
            $this->assertSame($status, $visit->fresh()->effectiveStatus());
        }
        $this->post(route('docente.visita.update', $visit), ['status' => 'Realizada', 'date' => '2026-10-14'])
            ->assertSessionHasErrors('date');
        $this->post(route('docente.visita.update', $visit), ['status' => 'Realizada', 'date' => '2026-10-16', 'observation' => 'Visita completada'])
            ->assertRedirect($url);
        $this->assertSame('Realizada', $visit->fresh()->status);
        $this->assertSame($teacher->id, $visit->fresh()->completed_by);
        $this->post(route('docente.visita.update', $visit), ['status' => 'Realizada', 'date' => '2026-10-16'])->assertStatus(409);
        $this->actingAs($this->userFor('COORDINACION'))->get(route('coordinacion.visita', $visit))
            ->assertOk()->assertSee('Realizada')->assertSee('Visita completada')
            ->assertSee(route('coordinacion.estudiante', $visit->practice->student_id));
        $this->get(route('coordinacion.visita', 999999))->assertNotFound();

        $otherUser = User::factory()->create(['role_id' => Role::where('name', 'DOCENTE_SEGUIMIENTO')->firstOrFail()->id, 'status' => 'ACTIVE']);
        Teacher::create(['user_id' => $otherUser->id, 'employee_code' => 'TEST-TEACHER']);
        $this->actingAs($otherUser)->get($url)->assertForbidden();
        $this->get(route('docente.visitas'))->assertOk()->assertDontSee($visit->practice->student->user->name);
        $this->get(route('docente.practicante', $visit->practice->student_id))->assertNotFound();
        $this->post(route('docente.visita.update', $visit), ['status' => 'Realizada', 'date' => '2026-10-16'])->assertForbidden();
        foreach (['ESTUDIANTE', 'JEFE_INMEDIATO', 'COORDINACION'] as $role) {
            $this->actingAs($this->userFor($role))->post(route('docente.visita.update', $visit), ['status' => 'Realizada', 'date' => '2026-10-16'])->assertForbidden();
        }
        $this->travelBack();
    }

    public function test_followups_persist_edits_and_status_and_reject_foreign_or_stale_changes(): void
    {
        $followup = Followup::with('practice', 'teacher.user')->firstOrFail();
        $teacher = $followup->teacher->user;
        $this->actingAs($teacher);
        $fields = [
            'practice_id' => $followup->practice_id, 'submission_key' => (string) Str::uuid(),
            'date' => '2026-11-10', 'type' => 'Tipo flexible', 'title' => 'Seguimiento real',
            'description' => 'Descripción persistente', 'status' => 'Pendiente', 'observation' => 'Observación de prueba',
        ];
        $response = $this->post(route('docente.seguimientos.store'), $fields)->assertRedirect();
        $created = Followup::where('submission_key', $fields['submission_key'])->firstOrFail();
        $this->post(route('docente.seguimientos.store'), $fields)->assertRedirect($response->headers->get('Location'));
        $this->assertSame(1, Followup::where('submission_key', $fields['submission_key'])->count());
        $this->get(route('docente.seguimiento', $created))->assertOk()->assertSee('Descripción persistente');
        $edited = array_replace($fields, ['title' => 'Título editado', 'revision' => 0]);
        $this->put(route('docente.seguimiento.update', $created), $edited)->assertRedirect();
        $this->assertSame('Título editado', $created->fresh()->title);
        $this->put(route('docente.seguimiento.update', $created), $edited)->assertStatus(409);
        $this->patch(route('docente.seguimiento.status', $created), ['status' => 'Realizado', 'revision' => 1])->assertRedirect();
        $this->assertSame('Realizado', $created->fresh()->status);
        foreach (['docente.dashboard', 'docente.practicantes', 'docente.alertas', 'docente.seguimientos'] as $route) {
            $this->get(route($route))->assertOk();
        }
        $this->actingAs($this->userFor('COORDINACION'));
        $this->get(route('coordinacion.seguimiento', $created))->assertOk()->assertSee('Realizado')->assertSee('Observación de prueba');
        $this->get(route('coordinacion.seguimiento', 999999))->assertNotFound();
        $otherUser = User::factory()->create(['role_id' => $teacher->role_id, 'status' => 'ACTIVE']);
        Teacher::create(['user_id' => $otherUser->id, 'employee_code' => 'OTHER-TEACHER']);
        $this->actingAs($otherUser)->get(route('docente.seguimiento', $created))->assertForbidden();
        $this->get(route('docente.seguimientos'))->assertOk()->assertDontSee('Título editado');
        $this->put(route('docente.seguimiento.update', $created), $edited)->assertForbidden();
        $this->post(route('docente.seguimientos.store'), array_replace($fields, ['submission_key' => (string) Str::uuid()]))->assertNotFound();
        foreach (['ESTUDIANTE', 'JEFE_INMEDIATO', 'COORDINACION'] as $role) {
            $this->actingAs($this->userFor($role))->patch(route('docente.seguimiento.status', $created), ['status' => 'Realizado', 'revision' => 2])->assertForbidden();
        }
        $this->actingAs($teacher)->delete(route('docente.seguimiento.destroy', $created), ['revision' => 2])->assertRedirect();
        $this->assertSoftDeleted('followups', ['id' => $created->id]);
    }

    public function test_student_requests_are_scoped_and_can_be_resubmitted_after_rejection(): void
    {
        $application = PracticeRequest::firstOrFail();
        $owner = $application->student->user;
        $other = PracticeRequest::where('student_id', '!=', $application->student_id)->firstOrFail();
        $this->actingAs($owner)->get(route('estudiante.solicitudes'))->assertOk()->assertSee($owner->name)->assertDontSee($other->student->user->name);
        $this->get(route('estudiante.solicitud', $other))->assertForbidden();
        $fields = ['practice_id' => $application->practice_id, 'student_observation' => 'Envío corregido'];
        $this->post(route('estudiante.solicitudes.store'), $fields)->assertRedirect(route('estudiante.solicitud', $application));
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.solicitud.review', $application), ['status' => 'Rechazada', 'observation' => 'Revisar'])->assertRedirect();
        $this->actingAs($owner)->post(route('estudiante.solicitudes.store'), $fields)->assertRedirect();
        $this->assertSame(2, $application->student->applications()->count());
        $this->assertSame('Rechazada', $application->fresh()->status);
        $this->assertSame('Pendiente', $application->student->applications()->latest('id')->first()->status);
        $this->get(route('estudiante.dashboard'))->assertOk()->assertSee('Pendiente');
        $this->get(route('estudiante.practica'))->assertOk()->assertSee('Pendiente')->assertSee($application->practice->company->official_name);
        $this->actingAs($this->userFor('COORDINACION'))->get(route('coordinacion.dashboard'))->assertOk()
            ->assertSee(route('coordinacion.solicitud', $application->student->applications()->latest('id')->first()));
    }

    public function test_flow_demo_seed_preserves_edited_and_deleted_records(): void
    {
        $followup = Followup::firstOrFail();
        $followup->update(['title' => 'Título cambiado por el docente', 'status' => 'Realizado']);
        $application = PracticeRequest::firstOrFail();
        $application->update(['status' => 'Aprobada']);
        $visit = Visit::firstOrFail();
        $visit->update(['status' => 'Programada', 'scheduled_date' => '2026-12-01']);
        $version = AcademicDocument::firstOrFail()->currentVersion;
        $version->update(['status' => 'Requiere corrección', 'observation' => 'Conservar observación']);
        $counts = [Followup::withTrashed()->count(), Visit::count(), PracticeRequest::count(), AcademicDocument::count(), DocumentVersion::count()];
        $this->seed(\Database\Seeders\AcademicFlowSeeder::class);
        $this->assertSame($counts, [Followup::withTrashed()->count(), Visit::count(), PracticeRequest::count(), AcademicDocument::count(), DocumentVersion::count()]);
        $this->assertSame('Título cambiado por el docente', $followup->fresh()->title);
        $this->assertSame('Aprobada', $application->fresh()->status);
        $this->assertSame('Programada', $visit->fresh()->status);
        $this->assertSame('Conservar observación', $version->fresh()->observation);
        $followup->delete();
        $this->seed(\Database\Seeders\AcademicFlowSeeder::class);
        $this->assertSame($counts[0], Followup::withTrashed()->count());
        $this->assertSoftDeleted('followups', ['id' => $followup->id]);
    }

    public function test_andrea_sees_the_same_preferred_practice_without_global_request_filters(): void
    {
        $andrea = Student::where('account_number', '0801-2022-00458')->firstOrFail();
        $practice = $andrea->preferredPractice();
        $andrea->practices()->create([
            'academic_period' => 'I Período', 'academic_year' => 2027,
            'stage' => 'Finalización', 'status' => 'COMPLETED', 'progress' => 100,
        ]);
        $this->actingAs($andrea->user);
        $requests = $this->get(route('estudiante.solicitudes'))->assertOk()
            ->assertViewHas('preferredPracticeId', $practice->id)
            ->assertDontSee('Sin práctica registrada')
            ->assertDontSee('id="flowSearch"', false)
            ->assertDontSee('id="flowCareer"', false)
            ->assertSee('Consulta y gestiona las solicitudes relacionadas con tu proceso');
        $this->assertMatchesRegularExpression('/<option value="'.$practice->id.'"\s+selected>/', $requests->getContent());
        $documents = $this->get(route('estudiante.documentos'))->assertOk()
            ->assertViewHas('preferredPracticeId', $practice->id)
            ->assertDontSee('Los archivos adjuntos se incorporarán en una etapa posterior.');
        $this->assertMatchesRegularExpression('/<option value="'.$practice->id.'"\s+selected>/', $documents->getContent());
        $this->get(route('estudiante.visita'))->assertOk()
            ->assertViewHas('studentPractice', fn ($selected) => $selected->is($practice));
        $this->actingAs($this->userFor('COORDINACION'))->get(route('coordinacion.solicitudes'))
            ->assertOk()->assertSee('id="flowSearch"', false)->assertSee('id="flowCareer"', false);
    }

    public function test_student_visit_eligibility_without_a_visit_does_not_create_records(): void
    {
        $andrea = Student::where('account_number', '0801-2022-00458')->firstOrFail();
        $practice = $andrea->preferredPractice();
        $practice->visits()->firstOrFail()->delete();
        $count = Visit::count();
        $this->actingAs($andrea->user);
        foreach (['2026-10-14' => false, '2026-10-15' => true, '2026-10-16' => true] as $date => $eligible) {
            $this->travelTo(Carbon::parse($date));
            $response = $this->get(route('estudiante.visita'))->assertOk();
            if ($eligible) {
                $response->assertSee('Tu práctica ya se encuentra habilitada para la visita de seguimiento. Aún no hay una visita registrada.');
            } else {
                $response->assertSee('Tu práctica aún no se encuentra habilitada para la visita de seguimiento.');
            }
            $this->assertSame($count, Visit::count());
        }
        $this->travelBack();
    }

    public function test_student_cannot_open_another_students_visit_and_visit_identity_uses_shared_styles(): void
    {
        $visit = Visit::with('practice.student.user', 'practice.teacher.user')->firstOrFail();
        $otherStudent = Student::whereKeyNot($visit->practice->student_id)->firstOrFail();
        $this->actingAs($otherStudent->user)->get(route('estudiante.visita.show', $visit))->assertForbidden();
        $this->actingAs($visit->practice->student->user)->get(route('estudiante.visita.show', $visit))->assertOk();
        foreach ([
            [$this->userFor('COORDINACION'), 'coordinacion.visitas'],
            [$visit->practice->teacher->user, 'docente.visitas'],
        ] as [$user, $route]) {
            $response = $this->actingAs($user)->get(route($route))->assertOk();
            $this->assertMatchesRegularExpression(
                '/class="coord-visit-student">\s*<strong>'.preg_quote(e($visit->practice->student->user->name), '/').'<\/strong>\s*<small>'.preg_quote($visit->practice->student->account_number, '/').'<\/small>/',
                $response->getContent(),
            );
        }
    }

    public function test_demo_visits_are_only_created_for_active_practices_and_followup_counter_is_explicit(): void
    {
        $sofia = Student::where('account_number', '0801-2020-00118')->firstOrFail();
        $practice = $sofia->preferredPractice();
        $this->assertSame('COMPLETED', $practice->status);
        $this->assertSame('Finalización', $practice->stage);
        $this->assertSame(100, $practice->progress);
        $this->assertFalse($practice->visits()->exists());
        $teacher = $practice->teacher->user;
        $this->actingAs($teacher)->get(route('docente.seguimientos'))->assertOk()
            ->assertSee('Con seguimiento')
            ->assertSee('Seguimiento académico')
            ->assertSee('Registro de acompañamiento del proceso de práctica profesional.')
            ->assertDontSee('Seguimiento de demostración')
            ->assertDontSee('Registro de desarrollo para demostrar el flujo académico.');
    }
}
