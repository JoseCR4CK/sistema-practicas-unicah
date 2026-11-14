<?php

namespace Database\Seeders;

use App\Models\AcademicCareer;
use App\Models\Student;
use App\Services\AssociateLegacyAcademicLoads;
use Illuminate\Database\Seeder;

class AcademicCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Solo carreras que ya existen en los expedientes. No inferir códigos ni asignaturas.
        foreach (Student::whereNotNull('career')->distinct()->pluck('career') as $name) {
            if (trim($name) === '') continue;
            AcademicCareer::firstOrCreate(['name' => trim($name)], ['status' => 'ACTIVE', 'source' => 'Carrera ya registrada en expedientes del proyecto; plan de asignaturas pendiente.']);
        }
        $result = app(AssociateLegacyAcademicLoads::class)->run();
        $this->command?->info('Catálogo: '.count($result['associated']).' cargas asociadas; '.count($result['pending']).' pendientes de asociación.');
    }
}
