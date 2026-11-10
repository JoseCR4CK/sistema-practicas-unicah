<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Practice;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoordinationBackendTest extends TestCase
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

    public function test_only_authorized_roles_can_access_coordination(): void
    {
        $url = route('coordinacion.estudiantes');

        $this->get($url)->assertRedirect(route('login'));

        foreach (['ESTUDIANTE', 'DOCENTE_SEGUIMIENTO', 'JEFE_INMEDIATO'] as $role) {
            $this->actingAs($this->userWithRole($role))->get($url)->assertForbidden();
        }

        foreach (['COORDINACION', 'ROOT'] as $role) {
            $this->actingAs($this->userWithRole($role))->get($url)->assertOk();
        }
    }

    public function test_student_list_record_and_report_share_the_active_practice(): void
    {
        $this->actingAs($this->userWithRole('COORDINACION'));
        $student = Student::where('account_number', '0801-2022-00458')->firstOrFail();
        $student->user->update(['name' => 'Nombre actualizado en base']);
        $active = $student->practices()->firstOrFail();
        $active->update(['stage' => 'Etapa actual de prueba', 'progress' => 61]);
        $historical = $student->practices()->create([
            'academic_period' => 'I Período', 'academic_year' => 2027,
            'status' => 'COMPLETED', 'stage' => 'Etapa histórica de prueba',
        ]);

        $this->assertTrue($student->latestPractice->is($historical));
        $this->assertTrue($student->latestActivePractice->is($active));

        foreach ([
            route('coordinacion.estudiantes'),
            route('coordinacion.estudiante', $student),
            route('coordinacion.estudiantes.informe', ['tipo' => 'activas', 'periodo' => 'III-2026']),
        ] as $url) {
            $this->get($url)->assertOk()
                ->assertSee('Nombre actualizado en base')
                ->assertSee('Grupo Tecnológico S.A.')
                ->assertSee('Etapa actual de prueba')
                ->assertSee('61%');
        }

        $this->get(route('coordinacion.estudiantes', ['etapa' => 'Etapa actual de prueba']))
            ->assertOk()->assertSee('Nombre actualizado en base')->assertDontSee('María López');

        $this->get(route('coordinacion.estudiante', $student))->assertOk()
            ->assertSee('Ing. Roberto Martínez')->assertSee('Msc. Carlos Martínez')
            ->assertSee('01/09/2026')->assertSee('30/11/2026');

        $active->update(['status' => 'COMPLETED']);
        $this->get(route('coordinacion.estudiante', $student))->assertOk()
            ->assertSee('Etapa histórica de prueba')->assertDontSee('Etapa actual de prueba');
    }

    public function test_student_record_handles_missing_practice_and_assignments(): void
    {
        $this->actingAs($this->userWithRole('COORDINACION'));
        $student = User::factory()->create()->student()->create([
            'account_number' => 'CUENTA-PRUEBA', 'career' => 'Carrera de prueba',
        ]);

        $this->get(route('coordinacion.estudiante', $student))->assertOk()->assertSee('Sin práctica');

        $student->practices()->create([
            'academic_period' => 'I Período', 'academic_year' => 2026,
            'status' => Practice::ACTIVE, 'stage' => 'Solicitud',
        ]);

        $this->get(route('coordinacion.estudiante', $student))->assertOk()
            ->assertSee('Sin empresa asignada')->assertSee('Sin responsable asignado')->assertSee('Sin docente asignado');
        $this->get(route('coordinacion.estudiante', 999999))->assertNotFound();
    }

    public function test_demo_seed_can_run_again_without_overwriting_edits(): void
    {
        $student = Student::where('account_number', '0801-2022-00714')->firstOrFail();
        $student->latestActivePractice->update(['progress' => 51]);
        $student->latestActivePractice->company->update(['official_name' => 'Empresa editada', 'rtn' => 'RTN-DE-PRUEBA']);
        $student->latestActivePractice->companyResponsible->update(['email' => 'responsable-editado@example.test']);
        $otherCompany = Company::where('rtn', '00000000000003')->firstOrFail();
        $otherCompany->update(['official_name' => 'Otra empresa editada', 'rtn' => 'OTRO-RTN-DE-PRUEBA']);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('students', 5);
        $this->assertDatabaseCount('practices', 5);
        $this->assertDatabaseCount('companies', 4);
        $this->assertDatabaseCount('company_responsibles', 2);
        $this->assertSame(51, $student->fresh()->latestActivePractice->progress);
        $this->assertSame('Empresa editada', $student->fresh()->latestActivePractice->company->official_name);
        $this->assertSame('responsable-editado@example.test', $student->fresh()->latestActivePractice->companyResponsible->email);
        $this->assertSame('OTRO-RTN-DE-PRUEBA', $otherCompany->fresh()->rtn);
    }

    public function test_company_lists_the_practitioners_and_responsible_from_the_same_practices(): void
    {
        $this->actingAs($this->userWithRole('COORDINACION'));
        $company = Company::where('commercial_name', 'GT Solutions')->firstOrFail();
        $responsible = $company->responsibles()->firstOrFail();

        $this->assertSame(2, $responsible->practices()->count());
        $this->assertSame(2, $company->practices()->with('student')->get()->pluck('student.career')->unique()->count());

        $this->get(route('coordinacion.empresas'))->assertOk()->assertSee($company->official_name)
            ->assertSee($company->commercial_name);
        $this->get(route('coordinacion.empresa', $company))->assertOk()->assertSee($responsible->full_name)
            ->assertSee('Andrea Martínez')->assertSee('Luis Hernández')->assertSee('Msc. Carlos Martínez');
    }

    public function test_coordination_can_create_and_edit_companies_and_responsibles_safely(): void
    {
        $this->actingAs($this->userWithRole('COORDINACION'));
        $companyFields = [
            'official_name' => 'Empresa <b>demo</b>', 'commercial_name' => 'Comercial de prueba',
            'sector' => 'Servicios', 'status' => 'ACTIVE',
        ];
        $this->post(route('coordinacion.empresas.store'), $companyFields)->assertRedirect(route('coordinacion.empresas'));
        $company = Company::where('official_name', $companyFields['official_name'])->firstOrFail();

        foreach ([route('coordinacion.empresas'), route('coordinacion.empresa', $company)] as $url) {
            $this->get($url)->assertOk()->assertSee($company->official_name)->assertDontSee($company->official_name, false);
        }

        $this->post(route('coordinacion.empresas.store'), $companyFields)->assertSessionHasErrors('official_name');
        $this->put(route('coordinacion.empresas.update', $company), array_replace($companyFields, ['commercial_name' => 'Nombre actualizado']))
            ->assertSessionHasNoErrors()->assertRedirect(route('coordinacion.empresas'));
        $this->assertSame('Nombre actualizado', $company->fresh()->commercial_name);

        $responsibleFields = ['full_name' => 'Responsable de prueba', 'position' => 'Contacto', 'status' => 'ACTIVE'];
        $this->post(route('coordinacion.empresas.responsables.store', $company), $responsibleFields)
            ->assertRedirect(route('coordinacion.empresa', $company));
        $responsible = $company->responsibles()->firstOrFail();

        $anotherCompany = Company::whereKeyNot($company->id)->firstOrFail();
        $this->put(route('coordinacion.empresas.responsables.update', [$anotherCompany, $responsible]), $responsibleFields)
            ->assertNotFound();
        $this->put(route('coordinacion.empresas.responsables.update', [$company, $responsible]),
            array_replace($responsibleFields, ['position' => 'Cargo actualizado']))->assertRedirect(route('coordinacion.empresa', $company));
        $this->assertSame('Cargo actualizado', $responsible->fresh()->position);
    }

    public function test_teacher_pages_show_real_assignments_and_count_active_practices(): void
    {
        $this->actingAs($this->userWithRole('COORDINACION'));
        $teacher = Teacher::firstOrFail();
        $teacher->user->update(['name' => 'Docente actualizado']);
        $this->assertSame(5, $teacher->practices()->count());
        $this->assertSame(4, $teacher->practices()->where('status', Practice::ACTIVE)->count());

        $this->get(route('coordinacion.docentes'))->assertOk()
            ->assertSee('Docente actualizado')->assertSee('Docentes de seguimiento')
            ->assertViewHas('docentes', fn ($teachers) => $teachers->first()->active_practices_count === 4);
        $this->get(route('coordinacion.docente', $teacher))->assertOk()
            ->assertSee('Docente actualizado')->assertSee('DOC-001')->assertSee('Andrea Martínez')
            ->assertSee('Sofía Pérez')->assertSee('Grupo Tecnológico S.A.')
            ->assertViewHas('practicasActivas', 4)->assertViewHas('practicantes', 5);
        $this->get(route('coordinacion.docente', 999999))->assertNotFound();
    }

    public function test_all_four_domains_show_the_same_practice_relationships(): void
    {
        $this->actingAs($this->userWithRole('COORDINACION'));
        $student = Student::where('account_number', '0801-2022-00458')->firstOrFail();
        $practice = $student->latestActivePractice;
        $company = $practice->company;
        $responsible = $practice->companyResponsible;
        $teacher = $practice->teacher;

        $this->assertTrue($practice->student->is($student));
        $this->assertTrue($company->practices()->whereKey($practice->id)->exists());
        $this->assertTrue($responsible->company->is($company));
        $this->assertTrue($teacher->practices()->whereKey($practice->id)->exists());

        $company->update(['official_name' => 'Razón social desde Eloquent']);
        $responsible->update(['full_name' => 'Responsable desde Eloquent']);
        $teacher->user->update(['name' => 'Docente desde Eloquent']);
        $practice->update(['stage' => 'Etapa flexible de prueba', 'progress' => 73, 'completed_hours' => 134, 'required_hours' => 377]);

        foreach ([
            route('coordinacion.estudiante', $student),
            route('coordinacion.empresa', $company),
            route('coordinacion.docente', $teacher),
            route('coordinacion.practica', $practice),
        ] as $url) {
            $this->get($url)->assertOk()->assertSee($student->user->name)
                ->assertSee('Razón social desde Eloquent')->assertSee('Docente desde Eloquent')
                ->assertSee('Etapa flexible de prueba');
        }

        $this->get(route('coordinacion.practicas'))->assertOk()->assertSee('Razón social desde Eloquent')
            ->assertSee('73%')->assertSee(route('coordinacion.practica', $practice));
        $this->get(route('coordinacion.practica', $practice))->assertOk()
            ->assertSee('Responsable desde Eloquent')->assertSee('III Período 2026')
            ->assertSee('01/09/2026')->assertSee('30/11/2026')
            ->assertSee('Horas completadas')->assertSee('134')->assertSee('Horas requeridas')->assertSee('377');
        $this->get(route('coordinacion.practica', 999999))->assertNotFound();
    }

    public function test_practice_counts_from_the_real_start_and_shows_the_visit_threshold_from_day_45(): void
    {
        $this->actingAs($this->userWithRole('COORDINACION'));
        $practice = Student::where('account_number', '0801-2022-00458')->firstOrFail()->latestActivePractice;
        $url = route('coordinacion.practica', $practice);

        $this->travelTo(Carbon::parse('2026-08-31'));
        $this->assertNull($practice->currentDay());

        $this->travelTo(Carbon::parse('2026-09-01'));
        $this->assertSame(1, $practice->currentDay());

        $this->travelTo(Carbon::parse('2026-10-14'));
        $this->assertSame(44, $practice->currentDay());
        $this->get($url)->assertOk()->assertDontSee('Día 45 alcanzado');

        foreach (['2026-10-15', '2026-10-16'] as $date) {
            $this->travelTo(Carbon::parse($date));
            $this->get($url)->assertOk()->assertSee('Día 45 alcanzado');
        }

        $practice->update(['start_date' => null, 'proposed_start_date' => '2026-09-01']);
        $this->assertNull($practice->currentDay());
        $this->get($url)->assertOk()->assertDontSee('Día 45 alcanzado');
        $this->travelBack();
    }

    private function userWithRole(string $role): User
    {
        return Role::where('name', $role)->firstOrFail()->users()->firstOrFail();
    }
}
