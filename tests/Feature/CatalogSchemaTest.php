<?php

namespace Tests\Feature;

use App\Models\Company;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CatalogSchemaTest extends TestCase
{
    public function test_new_migrations_are_reversible_without_affecting_existing_domains(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        foreach (glob(database_path('migrations/*.php')) as $file) (require $file)->up();
        $company = Company::create(['official_name' => 'Empresa del escenario de reversión']);
        $access = require database_path('migrations/2026_11_24_110000_create_access_requests.php');
        $catalog = require database_path('migrations/2026_11_24_100000_expand_academic_catalog.php');
        $access->down();
        $catalog->down();
        $this->assertFalse(Schema::hasTable('access_requests'));
        $this->assertFalse(Schema::hasColumn('academic_careers', 'type'));
        $this->assertSame($company->id, Company::firstOrFail()->id);
        $catalog->up();
        $access->up();
        $this->assertTrue(Schema::hasTable('access_requests'));
        $this->assertTrue(Schema::hasColumn('document_versions', 'files'));
        $this->assertDatabaseHas('companies', ['id' => $company->id]);
    }
}
