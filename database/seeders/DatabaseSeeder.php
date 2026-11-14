<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DemoDataSeeder::class,
            AcademicCatalogSeeder::class,
            AcademicFlowSeeder::class,
            AcademicClosingSeeder::class,
            DevelopmentTextCleanupSeeder::class,
        ]);
    }
}
