<?php

namespace Tests\Feature;

use App\Models\Practice;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
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
        $student->latestActivePractice->company->update(['official_name' => 'Empresa editada']);

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('students', 5);
        $this->assertDatabaseCount('practices', 5);
        $this->assertDatabaseCount('companies', 4);
        $this->assertSame(51, $student->fresh()->latestActivePractice->progress);
        $this->assertSame('Empresa editada', $student->fresh()->latestActivePractice->company->official_name);
    }

    private function userWithRole(string $role): User
    {
        return Role::where('name', $role)->firstOrFail()->users()->firstOrFail();
    }
}
