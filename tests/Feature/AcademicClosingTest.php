<?php

namespace Tests\Feature;

use App\Models\Monograph;
use App\Models\MonographReview;
use App\Models\Practice;
use App\Models\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicClosingTest extends TestCase
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

    private function otherTeacher(): Teacher
    {
        $user = User::factory()->create([
            'role_id' => Role::where('name', 'DOCENTE_SEGUIMIENTO')->firstOrFail()->id,
            'status' => 'ACTIVE',
        ]);
        return Teacher::create(['user_id' => $user->id]);
    }

    private function submit(Practice $practice): Monograph
    {
        $this->actingAs($practice->student->user)->post(route('estudiante.monografia.submit'), [
            'practice_id' => $practice->id,
            'title' => 'Informe académico',
            'submission_key' => (string) Str::uuid(),
        ])->assertRedirect();

        return Monograph::where('practice_id', $practice->id)->firstOrFail();
    }

    public function test_monograph_ownership_deadline_and_versions_preserve_other_review_clocks(): void
    {
        $this->travelTo(Carbon::parse('2026-11-01 09:00:00'));
        $practice = Student::where('account_number', '0801-2022-00458')->firstOrFail()->preferredPractice();
        $this->assertSame('2026-11-23', $practice->monographDeadline()->format('Y-m-d'));
        $monograph = $this->submit($practice);
        $first = $monograph->currentVersion;
        $technical = $monograph->currentReviews()->get('tecnica');
        $this->assertSame('2026-11-11 09:00:00', $technical->due_at->format('Y-m-d H:i:s'));
        $style = $monograph->currentReviews()->get('estilo');
        $coordinator = $monograph->currentReviews()->get('coordinador');
        $this->assertNull($style->started_at);
        $this->assertNull($coordinator->started_at);
        $this->actingAs($practice->teacher->user)->post(route('docente.monografia.review', $monograph), [
            'review_id' => $technical->id, 'status' => 'Requiere corrección', 'observation' => 'Precisar resultados',
        ])->assertRedirect();
        $this->travelTo(Carbon::parse('2026-11-03 12:00:00'));
        $monograph = $this->submit($practice);
        $this->assertSame(2, $monograph->versions()->count());
        $this->assertSame('Requiere corrección', $first->fresh()->status);
        $newTechnical = $monograph->currentReviews()->get('tecnica');
        $this->assertSame('2026-11-13 12:00:00', $newTechnical->due_at->format('Y-m-d H:i:s'));
        $this->assertSame($style->id, $monograph->currentReviews()->get('estilo')->id);
        $this->assertNull($style->fresh()->due_at);
        $this->assertNull($coordinator->fresh()->due_at);
        $this->actingAs($practice->teacher->user)->post(route('docente.monografia.review', $monograph), [
            'review_id' => $newTechnical->id, 'status' => 'Aprobada',
        ])->assertRedirect();
        $this->assertSame('2026-11-06 12:00:00', $style->fresh()->due_at->format('Y-m-d H:i:s'));
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.monografia.review', $monograph), [
            'review_id' => $style->id, 'status' => 'Requiere corrección', 'observation' => 'Revisar redacción',
        ])->assertRedirect();
        $technicalClock = $newTechnical->fresh()->due_at->toDateTimeString();
        $this->travelTo(Carbon::parse('2026-11-04 10:00:00'));
        $monograph = $this->submit($practice);
        $newStyle = $monograph->currentReviews()->get('estilo');
        $this->assertSame('2026-11-07 10:00:00', $newStyle->due_at->toDateTimeString());
        $this->assertSame($technicalClock, $newTechnical->fresh()->due_at->toDateTimeString());
        $this->actingAs($this->userFor('COORDINACION'))->post(route('coordinacion.monografia.review', $monograph), [
            'review_id' => $newStyle->id, 'status' => 'Aprobada',
        ])->assertRedirect();
        $this->assertSame('2026-11-05 10:00:00', $coordinator->fresh()->due_at->toDateTimeString());
        $otherStudent = Student::whereKeyNot($practice->student_id)->firstOrFail();
        $this->actingAs($otherStudent->user)->get(route('estudiante.monografia.show', $monograph))->assertForbidden();
        $this->actingAs($this->otherTeacher()->user)->get(route('docente.monografia.show', $monograph))->assertForbidden();
        $this->actingAs($practice->student->user)->get(route('estudiante.monografia.show', $monograph))->assertOk()->assertSee('Informe académico');
        $this->assertSame('Práctica', $practice->fresh()->stage);
        $practice->update(['end_date' => null]);
        $this->assertNull($practice->fresh()->monographDeadline());
        $this->travelBack();
    }
}
