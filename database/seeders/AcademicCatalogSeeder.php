<?php

namespace Database\Seeders;

use App\Services\AssociateLegacyAcademicLoads;
use Illuminate\Database\Seeder;

class AcademicCatalogSeeder extends Seeder
{
    public function run(): void
    {
        app(\App\Services\ImportAcademicCatalog::class)->run();
        $result = app(AssociateLegacyAcademicLoads::class)->run();
        $this->command?->info('Catálogo: '.count($result['associated']).' cargas asociadas; '.count($result['pending']).' pendientes de asociación.');
    }
}
