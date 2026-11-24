<?php

namespace Tests\Feature;

use App\Models\Practice;
use Database\Seeders\DemoDataSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CommitteeSchemaTest extends TestCase
{
    public function test_legacy_committee_and_members_are_preserved_without_guessing_roles_and_migration_is_reversible(): void
    {
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $files = glob(database_path('migrations/*.php'));
        $advanced = array_pop($files);
        foreach ($files as $file) {
            (require $file)->up();
        }
        $this->seed([RoleSeeder::class, DemoDataSeeder::class]);
        $practice = Practice::firstOrFail();
        $id = DB::table('academic_committees')->insertGetId(['practice_id' => $practice->id, 'status' => 'Asignada', 'revision' => 3, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('committee_members')->insert(['academic_committee_id' => $id, 'user_id' => $practice->teacher->user_id, 'function' => 'Miembro', 'created_at' => now(), 'updated_at' => now()]);
        $migration = require $advanced;
        $migration->up();
        $this->assertDatabaseHas('academic_committees', ['id' => $id, 'revision' => 3, 'status' => 'Asignada', 'origin_period' => $practice->academic_period, 'origin_year' => $practice->academic_year]);
        $this->assertDatabaseHas('committee_members', ['academic_committee_id' => $id, 'function' => 'Miembro', 'teacher_id' => $practice->teacher_id, 'academic_career_id' => null, 'ended_at' => null]);
        $this->assertDatabaseCount('committee_events', 1);
        $migration->down();
        $this->assertFalse(Schema::hasTable('committee_events'));
        $this->assertDatabaseHas('committee_members', ['academic_committee_id' => $id, 'function' => 'Miembro']);
        $this->assertFalse(Schema::hasColumn('academic_committees', 'origin_period'));
        $migration->up();
        $this->assertDatabaseCount('committee_events', 1);
    }
}
