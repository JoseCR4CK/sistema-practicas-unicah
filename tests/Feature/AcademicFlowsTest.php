<?php

namespace Tests\Feature;

use App\Models\AcademicDocument;
use App\Models\DocumentVersion;
use App\Models\PracticeRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\Visit;
use App\Models\Teacher;
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
}
