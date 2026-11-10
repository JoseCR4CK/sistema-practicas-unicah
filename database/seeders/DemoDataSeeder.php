<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyResponsible;
use App\Models\Practice;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction($this->seedDemo(...));
    }

    private function seedDemo(): void
    {
        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        $rootRole = Role::where('name', 'ROOT')->firstOrFail();

        $coordinationRole =
            Role::where('name', 'COORDINACION')->firstOrFail();

        $teacherRole =
            Role::where('name', 'DOCENTE_SEGUIMIENTO')->firstOrFail();

        $studentRole =
            Role::where('name', 'ESTUDIANTE')->firstOrFail();

        $bossRole =
            Role::where('name', 'JEFE_INMEDIATO')->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | ROOT
        |--------------------------------------------------------------------------
        */

        User::firstOrCreate(
            ['email' => 'root@unicah.demo'],
            [
                'role_id' => $rootRole->id,
                'name' => 'Administrador',
                'dni' => '0000000000001',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | COORDINACIÓN
        |--------------------------------------------------------------------------
        */

        User::firstOrCreate(
            ['email' => 'coordinacion@unicah.demo'],
            [
                'role_id' => $coordinationRole->id,
                'name' => 'Coordinación Académica',
                'dni' => '0000000000002',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | DOCENTE
        |--------------------------------------------------------------------------
        */

        $teacherUser = User::firstOrCreate(
            ['email' => 'cmartinez@unicah.demo'],
            [
                'role_id' => $teacherRole->id,
                'name' => 'Msc. Carlos Martínez',
                'dni' => '0000000000003',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'employee_code' => 'DOC-001',
                'faculty' => 'Facultad de Ingeniería',
                'phone' => null,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | EMPRESA 1
        |--------------------------------------------------------------------------
        */

        $bossUser1 = User::firstOrCreate(
            ['email' => 'responsable@empresa.demo'],
            [
                'role_id' => $bossRole->id,
                'name' => 'Ing. Roberto Martínez',
                'dni' => '0000000000004',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $company1 = $bossUser1->companyResponsible?->company
            ?? Company::where('rtn', '08019000000000')->first()
            ?? Company::firstOrCreate(
                ['official_name' => 'Grupo Tecnológico S.A.'],
                [
                    'commercial_name' => 'GT Solutions',
                    'rtn' => '08019000000000',
                    'sector' => 'Tecnología',
                    'address' => 'Barrio El Centro',
                    'city' => 'Danlí',
                    'department' => 'El Paraíso',
                    'phone' => '2763-0000',
                    'email' => 'info@empresa.demo',
                    'status' => 'ACTIVE',
                ],
            );


        $responsible1 = CompanyResponsible::firstOrCreate(
            [
                'company_id' => $company1->id,
                'user_id' => $bossUser1->id,
            ],
            [
                'email' => 'responsable@empresa.demo',
                'full_name' => 'Ing. Roberto Martínez',
                'position' => 'Jefe inmediato',
                'phone' => '9999-0001',
                'status' => 'ACTIVE',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | ANDREA
        |--------------------------------------------------------------------------
        */

        $andreaUser = User::where('email', 'amartinez@unicah.edu')->first()
            ?? User::firstOrCreate(
                ['email' => 'amartinez@unicah.demo'],
                [
                    'role_id' => $studentRole->id,
                    'name' => 'Andrea Martínez',
                    'dni' => '0000000000005',
                    'password' => 'Demo1234!',
                    'status' => 'ACTIVE',
                ],
            );


        $andrea = Student::firstOrCreate(
            ['account_number' => '0801-2022-00458'],
            [
                'user_id' => $andreaUser->id,
                'career' => 'Ingeniería en Ciencias de la Computación',
                'phone' => '0801-2003-12345',
            ]
        );


        Practice::firstOrCreate(
            [
                'student_id' => $andrea->id,
                'academic_period' => 'III Período',
                'academic_year' => 2026,
            ],
            [
                'company_id' => $company1->id,
                'company_responsible_id' => $responsible1->id,
                'teacher_id' => $teacher->id,

                'stage' => 'Práctica',
                'status' => 'ACTIVE',
                'progress' => 58,

                'start_date' => '2026-09-01',
                'end_date' => '2026-11-30',

                'completed_hours' => 0,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | EMPRESA 2
        |--------------------------------------------------------------------------
        */

        $bossUser2 = User::firstOrCreate(
            ['email' => 'jgarcia@empresa.demo'],
            [
                'role_id' => $bossRole->id,
                'name' => 'Lic. José García',
                'dni' => '0000000000006',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $company2 = $bossUser2->companyResponsible?->company
            ?? Company::firstOrCreate(
                ['official_name' => 'Inversiones del Valle S. de R.L.'],
                [
                    'commercial_name' => 'Inversiones del Valle',
                    'sector' => 'Servicios',
                    'city' => 'Danlí',
                    'department' => 'El Paraíso',
                    'status' => 'ACTIVE',
                ],
            );


        $responsible2 = CompanyResponsible::firstOrCreate(
            [
                'company_id' => $company2->id,
                'user_id' => $bossUser2->id,
            ],
            [
                'email' => 'jgarcia@empresa.demo',
                'full_name' => 'Lic. José García',
                'position' => 'Jefe inmediato',
                'status' => 'ACTIVE',
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | MARÍA
        |--------------------------------------------------------------------------
        */

        $mariaUser = User::where('email', 'mlopez@unicah.edu')->first()
            ?? User::firstOrCreate(
                ['email' => 'mlopez@unicah.demo'],
                [
                    'role_id' => $studentRole->id,
                    'name' => 'María López',
                    'dni' => '0000000000007',
                    'password' => 'Demo1234!',
                    'status' => 'ACTIVE',
                ],
            );


        $maria = Student::firstOrCreate(
            ['account_number' => '0801-2022-00671'],
            [
                'user_id' => $mariaUser->id,
                'career' => 'Administración de Empresas',
                'phone' => null,
            ]
        );


        Practice::firstOrCreate(
            [
                'student_id' => $maria->id,
                'academic_period' => 'III Período',
                'academic_year' => 2026,
            ],
            [
                'company_id' => $company2->id,
                'company_responsible_id' => $responsible2->id,
                'teacher_id' => $teacher->id,

                'stage' => 'Monografía',
                'status' => 'ACTIVE',
                'progress' => 79,

                'start_date' => '2026-08-20',
                'end_date' => '2026-11-20',

                'completed_hours' => 0,
            ]
        );
        $this->addDemoStudent($studentRole, $teacher, [
            'name' => 'Carlos Rodríguez',
            'email' => 'crodriguez@unicah.demo',
            'dni' => '0000000000008',
            'account' => '0801-2021-00231',
            'career' => 'Ingeniería Industrial',
        ], fn () => [
            'company_id' => Company::firstOrCreate(
                ['rtn' => '00000000000003'],
                ['official_name' => 'Corporación Industrial HN', 'status' => 'ACTIVE'],
            )->id,
            'stage' => 'Documentación',
            'status' => 'ACTIVE',
            'progress' => 32,
        ]);

        $this->addDemoStudent($studentRole, $teacher, [
            'name' => 'Luis Hernández',
            'email' => 'lhernandez@unicah.demo',
            'dni' => '0000000000009',
            'account' => '0801-2022-00714',
            'career' => 'Ingeniería Industrial',
        ], fn () => [
            'company_id' => $company1->id,
            'company_responsible_id' => $responsible1->id,
            'stage' => 'Práctica',
            'status' => 'ACTIVE',
            'progress' => 45,
            'start_date' => '2026-09-08',
            'end_date' => '2026-12-08',
        ]);

        $this->addDemoStudent($studentRole, $teacher, [
            'name' => 'Sofía Pérez',
            'email' => 'sperez@unicah.demo',
            'dni' => '0000000000010',
            'account' => '0801-2020-00118',
            'career' => 'Ingeniería Industrial',
        ], fn () => [
            'company_id' => Company::firstOrCreate(
                ['rtn' => '00000000000004'],
                ['official_name' => 'Manufacturas Centroamericanas', 'status' => 'ACTIVE'],
            )->id,
            'stage' => 'Finalización',
            'status' => 'COMPLETED',
            'progress' => 100,
            'start_date' => '2026-06-01',
            'end_date' => '2026-08-31',
        ], 'II Período');
    }

    private function addDemoStudent(Role $role, Teacher $teacher, array $data, \Closure $practice, string $period = 'III Período'): void
    {
        $student = Student::where('account_number', $data['account'])->first();

        if (!$student) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'dni' => $data['dni'],
                    'role_id' => $role->id,
                    'password' => 'Demo1234!',
                    'status' => 'ACTIVE',
                ],
            );

            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                ['account_number' => $data['account'], 'career' => $data['career']],
            );
        }

        if ($student->user->email !== $data['email']) {
            return;
        }

        $identity = ['academic_period' => $period, 'academic_year' => 2026];

        if (!$student->practices()->where($identity)->exists()) {
            $student->practices()->firstOrCreate($identity, $practice() + ['teacher_id' => $teacher->id]);
        }
    }
}
