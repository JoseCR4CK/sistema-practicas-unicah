<?php

namespace Tests\Feature;

use App\Models\PracticeRequest;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AcademicFlowsTest extends TestCase
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

    private function userFor(string $role): User
    {
        return Role::where('name', $role)->firstOrFail()->users()->firstOrFail();
    }

    public function test_request_access_and_review_persist_without_changing_the_practice_stage(): void
    {
        $application = PracticeRequest::firstOrFail();
        $show = route('coordinacion.solicitud', $application);
        $review = route('coordinacion.solicitud.review', $application);
        $this->get($show)->assertRedirect(route('login'));
        foreach (['ESTUDIANTE', 'DOCENTE_SEGUIMIENTO', 'JEFE_INMEDIATO'] as $role) {
            $this->actingAs($this->userFor($role))->get($show)->assertForbidden();
            $this->post($review, ['status' => 'Aprobada'])->assertForbidden();
        }
        $this->actingAs($this->userFor('COORDINACION'));
        $stage = $application->practice->stage;
        $this->get(route('coordinacion.solicitudes'))->assertOk()->assertSee($application->student->user->name);
        $this->get($show)->assertOk();
        $this->get(route('coordinacion.solicitud', 999999))->assertNotFound();
        $this->post($review, ['status' => 'Aprobada'])->assertRedirect($show);
        $this->assertSame('Aprobada', $application->fresh()->status);
        $this->assertSame($stage, $application->practice->fresh()->stage);
        $this->post($review, ['status' => 'Aprobada'])->assertStatus(409);
        $this->get($show)->assertSee('Aprobada');
        $this->get(route('coordinacion.solicitudes'))->assertSee('Aprobada');
    }

    public function test_rejection_records_the_reason_and_restarts_the_unspecified_deadline_once(): void
    {
        $this->actingAs($this->userFor('COORDINACION'));
        $application = PracticeRequest::firstOrFail();
        $url = route('coordinacion.solicitud.review', $application);
        $this->post($url, ['status' => 'Rechazada'])->assertSessionHasErrors('observation');
        $this->post($url, ['status' => 'Rechazada', 'observation' => 'Revisar información'])
            ->assertRedirect(route('coordinacion.solicitud', $application));
        $this->assertSame('Revisar información', $application->fresh()->observation);
        $this->assertNotNull($application->fresh()->deadline_restarted_at);
        $this->post($url, ['status' => 'Rechazada', 'observation' => 'Otra razón'])->assertStatus(409);
        $this->assertSame('Revisar información', $application->fresh()->observation);
    }
}
