<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Practice;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompanyApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        foreach (glob(database_path('migrations/*.php')) as $file) (require $file)->up();
        $this->seed(DatabaseSeeder::class);
        $this->asRole('COORDINACION');
    }

    private function asRole(string $role): User
    {
        $user = User::whereHas('role', fn ($query) => $query->where('name', $role))->firstOrFail();
        $this->actingAs($user);
        return $user;
    }

    private function fields(array $overrides = []): array
    {
        return array_replace([
            'official_name' => 'Empresa API escenario', 'commercial_name' => 'Nombre comercial',
            'rtn' => 'RTN-ESCENARIO', 'sector' => 'Servicios', 'address' => 'Dirección',
            'city' => 'Ciudad', 'department' => 'Departamento', 'phone' => '00000000',
            'email' => 'empresa@example.test', 'status' => 'ACTIVE',
        ], $overrides);
    }

    public function test_list_and_show_return_real_json_records_without_accept_header(): void
    {
        $company = Company::create($this->fields());
        $this->get('/api/companies')->assertOk()->assertHeader('Content-Type', 'application/json')
            ->assertJsonCount(Company::count(), 'data')->assertJsonFragment(['official_name' => $company->official_name]);
        $this->get('/api/companies/'.$company->id)->assertOk()->assertJsonPath('data.id', $company->id)
            ->assertJsonPath('data.commercial_name', 'Nombre comercial');
    }

    public function test_create_persists_validated_fields_and_ignores_id_injection(): void
    {
        $result = $this->postJson('/api/companies', $this->fields(['id' => 999999, 'invented_column' => 'ignored']))
            ->assertCreated()->assertJsonPath('data.official_name', 'Empresa API escenario');
        $id = $result->json('data.id');
        $this->assertNotSame(999999, $id);
        $result->assertHeader('Location', url('/api/companies/'.$id));
        $this->assertDatabaseHas('companies', ['id' => $id, 'rtn' => 'RTN-ESCENARIO']);
    }

    public function test_invalid_fields_and_duplicates_return_422(): void
    {
        $this->postJson('/api/companies', [])->assertUnprocessable()->assertJsonValidationErrors(['official_name', 'sector', 'status']);
        $this->postJson('/api/companies', $this->fields(['email' => 'incorrecto', 'status' => 'UNKNOWN', 'phone' => str_repeat('x', 256)]))
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'status', 'phone']);
        Company::create($this->fields());
        $this->postJson('/api/companies', $this->fields())->assertUnprocessable()->assertJsonValidationErrors(['official_name', 'rtn']);
    }

    public function test_put_and_patch_persist_and_preserve_own_unique_values(): void
    {
        $company = Company::create($this->fields());
        $url = '/api/companies/'.$company->id;
        $this->putJson($url, $this->fields(['city' => 'Nueva ciudad']))->assertOk()->assertJsonPath('data.city', 'Nueva ciudad');
        $this->patchJson($url, ['status' => 'INACTIVE'])->assertOk()->assertJsonPath('data.status', 'INACTIVE');
        $this->assertSame('Nueva ciudad', $company->fresh()->city);
        $this->patchJson($url, ['official_name' => null])->assertUnprocessable()->assertJsonValidationErrors('official_name');
        $this->putJson($url, ['city' => 'Incompleto'])->assertUnprocessable();
        $other = Company::create($this->fields(['official_name' => 'Otra', 'rtn' => 'OTRO']));
        $this->patchJson($url, ['rtn' => $other->rtn])->assertUnprocessable()->assertJsonValidationErrors('rtn');
    }

    public function test_missing_records_return_json_404(): void
    {
        $this->get('/api/companies/999999')->assertNotFound()->assertHeader('Content-Type', 'application/json');
        $this->putJson('/api/companies/999999', $this->fields())->assertNotFound();
        $this->patchJson('/api/companies/999999', ['city' => 'Ciudad'])->assertNotFound();
        $this->deleteJson('/api/companies/999999')->assertNotFound();
    }

    public function test_delete_only_unlinked_companies_preserving_relations(): void
    {
        $free = Company::create($this->fields());
        $this->deleteJson('/api/companies/'.$free->id)->assertNoContent();
        $this->assertDatabaseMissing('companies', ['id' => $free->id]);
        $linked = Company::create($this->fields());
        $responsible = $linked->responsibles()->create(['full_name' => 'Responsable escenario']);
        $this->deleteJson('/api/companies/'.$linked->id)->assertConflict();
        $this->assertDatabaseHas('company_responsibles', ['id' => $responsible->id, 'company_id' => $linked->id]);
        $practice = Practice::whereNotNull('company_id')->firstOrFail();
        $this->deleteJson('/api/companies/'.$practice->company_id)->assertConflict();
        $this->assertSame($practice->company_id, $practice->fresh()->company_id);
    }

    public function test_authentication_roles_and_inactive_accounts_are_enforced(): void
    {
        User::create([
            'role_id' => Role::where('name', 'PERSONAL_CAMPUS')->firstOrFail()->id,
            'name' => 'Usuario del escenario', 'dni' => 'API-ESCENARIO',
            'email' => 'api-personal@example.test', 'password' => 'Scenario123!', 'status' => 'ACTIVE',
        ]);
        auth()->forgetGuards();
        $this->get('/api/companies')->assertUnauthorized()->assertHeader('Content-Type', 'application/json');
        $company = Company::firstOrFail();
        foreach (['ESTUDIANTE', 'DOCENTE_SEGUIMIENTO', 'JEFE_INMEDIATO', 'PERSONAL_CAMPUS'] as $role) {
            $this->asRole($role);
            $this->getJson('/api/companies')->assertForbidden();
            $this->postJson('/api/companies', $this->fields())->assertForbidden();
            $this->patchJson('/api/companies/'.$company->id, ['status' => 'INACTIVE'])->assertForbidden();
            $this->deleteJson('/api/companies/'.$company->id)->assertForbidden();
        }
        $root = $this->asRole('ROOT');
        $this->getJson('/api/companies')->assertOk();
        $root->update(['status' => 'INACTIVE']);
        $this->getJson('/api/companies')->assertForbidden();
    }

    public function test_session_writes_require_csrf_outside_test_bypass(): void
    {
        // Laravel omite CSRF durante pruebas: activar temporalmente su verificación real.
        $this->app['env'] = 'local';
        try {
            $this->postJson('/api/companies', $this->fields())->assertStatus(419);
            $this->withSession(['_token' => 'scenario-csrf-token'])
                ->postJson('/api/companies', $this->fields(), ['X-CSRF-TOKEN' => 'scenario-csrf-token'])
                ->assertCreated();
        } finally {
            $this->app['env'] = 'testing';
        }
    }
}
