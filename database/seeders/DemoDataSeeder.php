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

class DemoDataSeeder extends Seeder
{
    public function run(): void
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

        User::updateOrCreate(
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

        User::updateOrCreate(
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

        $teacherUser = User::updateOrCreate(
            ['email' => 'cmartinez@unicah.demo'],
            [
                'role_id' => $teacherRole->id,
                'name' => 'Msc. Carlos Martínez',
                'dni' => '0000000000003',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $teacher = Teacher::updateOrCreate(
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

        $company1 = Company::updateOrCreate(
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
            ]
        );


        $bossUser1 = User::updateOrCreate(
            ['email' => 'responsable@empresa.demo'],
            [
                'role_id' => $bossRole->id,
                'name' => 'Ing. Roberto Martínez',
                'dni' => '0000000000004',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $responsible1 = CompanyResponsible::updateOrCreate(
            [
                'company_id' => $company1->id,
                'email' => 'responsable@empresa.demo',
            ],
            [
                'user_id' => $bossUser1->id,
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

        $andreaUser = User::updateOrCreate(
            ['email' => 'amartinez@unicah.edu'],
            [
                'role_id' => $studentRole->id,
                'name' => 'Andrea Martínez',
                'dni' => '0703200603787',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $andrea = Student::updateOrCreate(
            ['account_number' => '0801-2022-00458'],
            [
                'user_id' => $andreaUser->id,
                'career' => 'Ingeniería en Ciencias de la Computación',
                'phone' => '0801-2003-12345',
            ]
        );


        Practice::updateOrCreate(
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

        $company2 = Company::updateOrCreate(
            ['official_name' => 'Inversiones del Valle S. de R.L.'],
            [
                'commercial_name' => 'Inversiones del Valle',
                'sector' => 'Servicios',
                'city' => 'Danlí',
                'department' => 'El Paraíso',
                'status' => 'ACTIVE',
            ]
        );


        $bossUser2 = User::updateOrCreate(
            ['email' => 'jgarcia@empresa.demo'],
            [
                'role_id' => $bossRole->id,
                'name' => 'Lic. José García',
                'dni' => '0000000000006',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $responsible2 = CompanyResponsible::updateOrCreate(
            [
                'company_id' => $company2->id,
                'email' => 'jgarcia@empresa.demo',
            ],
            [
                'user_id' => $bossUser2->id,
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

        $mariaUser = User::updateOrCreate(
            ['email' => 'mlopez@unicah.edu'],
            [
                'role_id' => $studentRole->id,
                'name' => 'María López',
                'dni' => '0000000000007',
                'password' => 'Demo1234!',
                'status' => 'ACTIVE',
            ]
        );


        $maria = Student::updateOrCreate(
            ['account_number' => '0801-2022-00671'],
            [
                'user_id' => $mariaUser->id,
                'career' => 'Administración de Empresas',
                'phone' => null,
            ]
        );


        Practice::updateOrCreate(
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
    }
}
