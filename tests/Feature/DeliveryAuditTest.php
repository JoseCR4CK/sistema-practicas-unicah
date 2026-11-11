<?php

namespace Tests\Feature;

use App\Models\AcademicCommittee;
use App\Models\AcademicDocument;
use App\Models\Company;
use App\Models\Defense;
use App\Models\DocumentVersion;
use App\Models\Followup;
use App\Models\Monograph;
use App\Models\Practice;
use App\Models\PracticeRequest;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\Visit;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DeliveryAuditTest extends TestCase
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

    private function user(string $role): User
    {
        return User::whereHas('role', fn ($query) => $query->where('name', $role))->firstOrFail();
    }

    public function test_dni_login_logout_inactive_and_unsupported_roles(): void
    {
        foreach (['ROOT' => 'coordinacion.dashboard', 'COORDINACION' => 'coordinacion.dashboard', 'ESTUDIANTE' => 'estudiante.dashboard', 'DOCENTE_SEGUIMIENTO' => 'docente.dashboard', 'JEFE_INMEDIATO' => 'jefe.dashboard'] as $role => $destination) {
            $user = $this->user($role);
            $this->post(route('login.attempt'), ['dni' => $user->dni, 'password' => 'Demo1234!'])->assertRedirect(route($destination));
            $this->assertAuthenticatedAs($user);
            $this->post(route('logout'))->assertRedirect(route('login'));
            $this->assertGuest();
        }
        $user = $this->user('ESTUDIANTE');
        $user->update(['status' => 'INACTIVE']);
        $this->post(route('login.attempt'), ['dni' => $user->dni, 'password' => 'Demo1234!'])->assertSessionHasErrors();
        $this->assertGuest();
        $unsupported = User::factory()->create(['role_id' => Role::where('name', 'PERSONAL_CAMPUS')->firstOrFail()->id, 'dni' => '9000000000001', 'status' => 'ACTIVE', 'password' => 'Demo1234!']);
        $this->post(route('login.attempt'), ['dni' => $unsupported->dni, 'password' => 'Demo1234!'])->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_csrf_is_required_outside_the_automated_test_bypass(): void
    {
        $this->app->instance('env', 'local');
        $student = $this->user('ESTUDIANTE');
        $fields = ['dni' => $student->dni, 'password' => 'Demo1234!'];
        $this->post(route('login.attempt'), $fields)->assertStatus(419);
        $this->assertGuest();
        $this->withSession(['_token' => 'token-verificacion-csrf'])
            ->post(route('login.attempt'), $fields + ['_token' => 'token-verificacion-csrf'])
            ->assertRedirect(route('estudiante.dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    public function test_login_attempts_are_limited_and_guest_cannot_open_academic_pages(): void
    {
        foreach (['coordinacion.dashboard', 'docente.dashboard', 'estudiante.practica', 'docente.carga-academica', 'academico.ternas', 'academico.defensas'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post(route('login.attempt'), ['dni' => 'inexistente', 'password' => 'incorrecta'])->assertSessionHasErrors('dni');
        }
        $this->post(route('login.attempt'), ['dni' => 'inexistente', 'password' => 'incorrecta'])->assertStatus(429);
        $this->assertGuest();
    }

    public function test_student_company_teacher_and_dashboards_use_the_same_persistent_relations(): void
    {
        $student = Student::firstOrFail();
        $practice = $student->preferredPractice();
        $practice->company->update(['official_name' => 'Empresa actualizada desde coordinación']);
        $practice->teacher->user->update(['name' => 'Docente actualizado']);
        $this->actingAs($student->user);
        foreach (['estudiante.dashboard', 'estudiante.empresa', 'estudiante.practica'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('Empresa actualizada desde coordinación')->assertDontSee('Grupo Tecnológico S.A.');
        }
        foreach (['estudiante.dashboard', 'estudiante.docente'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('Docente actualizado');
        }
        $other = Student::whereKeyNot($student->id)->firstOrFail();
        $this->actingAs($other->user)->get(route('estudiante.empresa'))->assertOk()->assertDontSee('Empresa actualizada desde coordinación');
        $practice->update(['company_id' => null, 'company_responsible_id' => null, 'teacher_id' => null]);
        $this->actingAs($student->user)->get(route('estudiante.empresa'))->assertOk()->assertSee('Sin empresa asignada');
        $this->get(route('estudiante.docente'))->assertOk()->assertSee('Sin docente asignado');
        $this->actingAs($this->user('COORDINACION'))->get(route('coordinacion.dashboard'))->assertOk()
            ->assertViewHas('practicasActivas', Practice::where('status', 'ACTIVE')->count());
        $visits = Visit::with('practice')->get()->filter(fn ($visit) => $visit->practice->teacher_id !== null && in_array($visit->effectiveStatus(), ['Habilitada', 'Programada'], true))->count();
        $this->get(route('coordinacion.docentes'))->assertOk()->assertViewHas('visitasPorAtender', $visits);
    }

    public function test_boss_reads_only_own_responsibility_without_academic_management(): void
    {
        $boss = $this->user('JEFE_INMEDIATO');
        $practice = Practice::whereHas('companyResponsible', fn ($query) => $query->where('user_id', $boss->id))->firstOrFail();
        $other = Practice::whereHas('companyResponsible', fn ($query) => $query->where('user_id', '!=', $boss->id))->firstOrFail();
        $this->actingAs($boss)->get(route('jefe.dashboard'))->assertOk()->assertSee($practice->student->user->name)->assertDontSee($other->student->user->name);
        foreach (['coordinacion.solicitudes', 'coordinacion.documentos', 'docente.seguimientos', 'docente.carga-academica', 'academico.ternas', 'academico.defensas'] as $route) {
            $this->get(route($route))->assertStatus(in_array($route, ['academico.ternas', 'academico.defensas'], true) ? 200 : 403);
        }
        $this->get(route('academico.terna.show', AcademicCommittee::firstOrFail()))->assertForbidden();
        $this->post(route('coordinacion.solicitud.review', PracticeRequest::firstOrFail()), ['status' => 'Aprobada'])->assertForbidden();
        $this->post(route('coordinacion.documento.review', AcademicDocument::firstOrFail()), ['status' => 'Aprobado'])->assertForbidden();
    }

    public function test_parent_child_ids_and_repeated_actions_cannot_modify_other_records(): void
    {
        $this->actingAs($this->user('COORDINACION'));
        $company = Company::whereHas('responsibles')->firstOrFail();
        $foreignResponsible = \App\Models\CompanyResponsible::where('company_id', '!=', $company->id)->firstOrFail();
        $this->put(route('coordinacion.empresas.responsables.update', [$company, $foreignResponsible]), [])->assertNotFound();
        $document = AcademicDocument::firstOrFail();
        $foreignVersion = DocumentVersion::where('academic_document_id', '!=', $document->id)->firstOrFail();
        $this->post(route('coordinacion.documento.review', $document), ['version_id' => $foreignVersion->id, 'status' => 'Aprobado'])->assertStatus(409);
        $this->assertSame(DocumentVersion::PENDING, $foreignVersion->fresh()->status);
        $request = PracticeRequest::firstOrFail();
        $this->post(route('coordinacion.solicitud.review', $request), ['status' => 'Aprobada'])->assertRedirect();
        $this->post(route('coordinacion.solicitud.review', $request), ['status' => 'Aprobada'])->assertStatus(409);
        $followup = Followup::firstOrFail();
        $otherPractice = Practice::where('teacher_id', $followup->teacher_id)->whereKeyNot($followup->practice_id)->firstOrFail();
        $this->actingAs($followup->teacher->user)->post(route('docente.seguimientos.store'), [
            'practice_id' => $otherPractice->id, 'submission_key' => $followup->submission_key,
            'date' => today()->format('Y-m-d'), 'type' => 'Seguimiento', 'title' => 'Actividad', 'description' => 'Descripción', 'status' => 'Pendiente',
        ])->assertStatus(409);
        $this->assertSame($followup->practice_id, $followup->fresh()->practice_id);
        foreach (['docente.visita', 'docente.seguimiento', 'docente.monografia.show', 'docente.terna.show', 'docente.defensa.show'] as $route) {
            $this->get(route($route, 999999999))->assertNotFound();
        }
    }

    public function test_seeders_are_idempotent_and_cleanup_preserves_manual_text_and_non_development_accounts(): void
    {
        $request = PracticeRequest::firstOrFail();
        $request->refresh()->update(['student_observation' => 'Solicitud de demostración para desarrollo.']);
        $version = DocumentVersion::firstOrFail();
        $version->update(['submission_note' => 'Observación editada por estudiante']);
        $monograph = Monograph::firstOrFail();
        $monograph->currentVersion->update(['title' => 'Título actualizado por estudiante']);
        $role = Role::where('name', 'ESTUDIANTE')->firstOrFail();
        $role->update(['display_name' => 'Nombre conservado']);
        $before = [PracticeRequest::count(), DocumentVersion::count(), Visit::count(), Followup::withTrashed()->count(), Monograph::count(), AcademicCommittee::count()];
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);
        $this->assertSame($before, [PracticeRequest::count(), DocumentVersion::count(), Visit::count(), Followup::withTrashed()->count(), Monograph::count(), AcademicCommittee::count()]);
        $this->assertSame('Solicitud de práctica profesional.', $request->fresh()->student_observation);
        $this->assertSame('Observación editada por estudiante', $version->fresh()->submission_note);
        $this->assertSame('Título actualizado por estudiante', $monograph->fresh()->currentVersion->title);
        $this->assertSame('Nombre conservado', $role->fresh()->display_name);
        $request->refresh()->update(['student_observation' => 'SOLICITUD DE DEMOSTRACIÓN PARA DESARROLLO.']);
        $this->seed(\Database\Seeders\DevelopmentTextCleanupSeeder::class);
        $this->assertSame('SOLICITUD DE DEMOSTRACIÓN PARA DESARROLLO.', $request->fresh()->student_observation);
        $request->student->user->update(['email' => 'alumno@example.org']);
        $request->refresh()->update(['student_observation' => 'Solicitud de demostración para desarrollo.']);
        $this->seed(\Database\Seeders\DevelopmentTextCleanupSeeder::class);
        $this->assertSame('Solicitud de demostración para desarrollo.', $request->fresh()->student_observation);
    }
}
