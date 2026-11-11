<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'ROOT',
                'display_name' => 'Administrador del sistema',
            ],
            [
                'name' => 'COORDINACION',
                'display_name' => 'Coordinación Académica',
            ],
            [
                'name' => 'DOCENTE_SEGUIMIENTO',
                'display_name' => 'Docente de seguimiento',
            ],
            [
                'name' => 'ESTUDIANTE',
                'display_name' => 'Estudiante',
            ],
            [
                'name' => 'JEFE_INMEDIATO',
                'display_name' => 'Jefe inmediato',
            ],
            [
                'name' => 'PERSONAL_CAMPUS',
                'display_name' => 'Personal de campus',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
