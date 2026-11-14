<?php

namespace Tests\Feature;

use App\Models\AcademicCareer;
use App\Models\AcademicLoad;
use App\Models\AcademicSubject;
use App\Models\User;
use App\Services\AssociateLegacyAcademicLoads;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicCatalogTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        foreach (glob(database_path('migrations/*.php')) as $file) (require $file)->up();
        $this->seed(DatabaseSeeder::class);
        $this->actingAs($this->user('DOCENTE_SEGUIMIENTO'));
    }

    private function user(string $role): User
    {
        return User::whereHas('role', fn ($query) => $query->where('name', $role))->firstOrFail();
    }

    private function subject(int $credits = 2, ?AcademicCareer $career = null, array $overrides = []): AcademicSubject
    {
        return AcademicSubject::create(array_replace([
            'academic_career_id' => ($career ?? AcademicCareer::firstOrFail())->id,
            'name' => 'Asignatura del escenario '.Str::uuid(), 'credits' => $credits,
            'weekly_class_hours' => \App\Services\AcademicClassTime::hoursForCredits($credits),
            'source' => 'Fixture exclusivo de SQLite en memoria',
        ], $overrides));
    }

    private function fields(AcademicSubject $subject, array $overrides = []): array
    {
        return array_replace([
            'academic_career_id' => $subject->academic_career_id, 'academic_subject_id' => $subject->id,
            'academic_year' => 2026, 'academic_period' => 'III', 'weekdays' => [1, 3, 5],
            'starts_at' => '10:00', 'ends_at' => '10:50', 'valid_from' => '2026-09-01', 'valid_until' => '2026-12-20',
            'status' => 'ACTIVE', 'submission_key' => (string) Str::uuid(),
        ], $overrides);
    }

    public function test_periods_and_subject_career_relationship_are_validated_on_server(): void
    {
        $subject = $this->subject();
        foreach (['I', 'II', 'III'] as $period) {
            $this->post(route('docente.carga-academica.store'), $this->fields($subject, ['academic_period' => $period]))->assertRedirect();
        }
        foreach (['IV', 'Período libre', 'III Período Académico'] as $period) {
            $this->post(route('docente.carga-academica.store'), $this->fields($subject, ['academic_period' => $period]))->assertSessionHasErrors('academic_period');
        }
        $otherCareer = AcademicCareer::whereKeyNot($subject->academic_career_id)->firstOrFail();
        $this->post(route('docente.carga-academica.store'), $this->fields($subject, ['academic_career_id' => $otherCareer->id]))->assertSessionHasErrors('academic_subject_id');
        $this->post(route('docente.carga-academica.store'), $this->fields($subject, ['academic_subject_id' => 999999]))->assertSessionHasErrors('academic_subject_id');
        $this->assertDatabaseCount('academic_loads', 3);
    }

    public function test_confirmed_credits_hours_and_maximum_minutes_override_tampered_values_for_both_roles(): void
    {
        foreach (['DOCENTE_SEGUIMIENTO', 'COORDINACION'] as $role) {
            $actor = $this->user($role);
            $this->actingAs($actor);
            $teacher = $this->user('DOCENTE_SEGUIMIENTO')->teacher;
            $route = $role === 'COORDINACION' ? route('coordinacion.carga-academica.store', $teacher) : route('docente.carga-academica.store');
            foreach ([2 => 3, 3 => 4, 4 => 5] as $credits => $hours) {
                $subject = $this->subject($credits);
                $days = range(1, $hours);
                $fields = $this->fields($subject, ['weekdays' => $days, 'credits' => 999, 'weekly_class_hours' => 999, 'subject' => 'Nombre adulterado', 'career' => 'Carrera adulterada']);
                $this->post($route, $fields)->assertRedirect();
                $load = AcademicLoad::latest('id')->firstOrFail();
                $this->assertSame(number_format($credits, 2), $load->credits);
                $this->assertSame(number_format($hours, 2), $load->weekly_class_hours);
                $this->assertSame($subject->name, $load->subject);
                $this->assertSame($hours * 50, $load->scheduledWeeklyMinutes());
                $this->post($route, $this->fields($subject, ['weekdays' => $days, 'ends_at' => '10:51']))->assertSessionHasErrors('ends_at');
            }
        }
        $this->assertDatabaseCount('academic_loads', 6);
    }

    public function test_excess_minutes_are_rejected_partial_load_is_preserved_and_unknown_rules_do_not_become_defaults(): void
    {
        $subject = $this->subject();
        $this->post(route('docente.carga-academica.store'), $this->fields($subject, ['weekdays' => [1, 2, 3, 4, 5]]))
            ->assertSessionHasErrors(['ends_at' => 'Esta asignatura permite 3 horas clase semanales (150 minutos). El horario seleccionado suma 250 minutos.']);
        $this->post(route('docente.carga-academica.store'), $this->fields($subject, ['weekdays' => [1, 2]]))->assertRedirect();
        $this->assertSame(100, AcademicLoad::firstOrFail()->scheduledWeeklyMinutes());
        $this->get(route('docente.carga-academica'))->assertOk()->assertSee('Horario parcial');
        foreach ([$this->subject(5), $this->subject(3, null, ['credits' => null]), $this->subject(2, null, ['weekly_class_hours' => 5]), $this->subject(2, null, ['status' => 'INACTIVE'])] as $incomplete) {
            $this->post(route('docente.carga-academica.store'), $this->fields($incomplete))->assertSessionHasErrors('academic_subject_id');
        }
        $subject->career->update(['status' => 'INACTIVE']);
        $this->post(route('docente.carga-academica.store'), $this->fields($subject))->assertSessionHasErrors('academic_subject_id');
        $this->assertDatabaseCount('academic_loads', 1);
    }

    public function test_edit_recalculates_from_subject_and_preserves_previous_values_and_revision(): void
    {
        $subject = $this->subject(4);
        $this->post(route('docente.carga-academica.store'), $this->fields($subject, ['weekdays' => [1, 2, 3, 4, 5]]))->assertRedirect();
        $load = AcademicLoad::firstOrFail();
        $next = $this->subject(2);
        $this->actingAs($this->user('COORDINACION'));
        $fields = $this->fields($next, ['revision' => 0, 'reason' => 'Corregir asignatura y horario', 'credits' => 700, 'weekly_class_hours' => 700]);
        $this->put(route('coordinacion.carga-academica.update', $load), $fields)->assertRedirect();
        $this->assertSame('2.00', $load->fresh()->credits);
        $this->assertSame('3.00', $load->fresh()->weekly_class_hours);
        $change = $load->changes()->firstOrFail();
        $this->assertSame('4.00', $change->before['credits']);
        $this->assertSame('5.00', $change->before['weekly_class_hours']);
        $this->assertSame($subject->id, $change->before['academic_subject_id']);
        $this->assertSame($next->id, $change->after['academic_subject_id']);
        $this->put(route('coordinacion.carga-academica.update', $load), $fields)->assertStatus(409);
        $fields['revision'] = 1;
        unset($fields['reason']);
        $this->put(route('coordinacion.carga-academica.update', $load), $fields)->assertSessionHasErrors('reason');
    }

    public function test_new_form_has_locked_subject_and_read_only_catalog_values_with_scoped_catalog_data(): void
    {
        $subject = $this->subject();
        $other = $this->subject(3, AcademicCareer::whereKeyNot($subject->academic_career_id)->firstOrFail());
        $response = $this->get(route('docente.carga-academica'))->assertOk();
        $document = new \DOMDocument();
        @$document->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
        $xpath = new \DOMXPath($document);
        $this->assertSame(1, $xpath->query('//select[@id="loadnewSubject" and @disabled]')->length);
        $this->assertSame(1, $xpath->query('//select[@id="loadnewCareer" and not(@disabled)]')->length);
        $this->assertSame(2, $xpath->query('//input[(@id="loadnewCredits" or @id="loadnewHours") and @readonly and not(@name)]')->length);
        $data = json_decode($xpath->query('//script[@id="academicCatalogData"]')->item(0)->textContent, true, 512, JSON_THROW_ON_ERROR);
        $career = collect($data)->firstWhere('id', $subject->academic_career_id);
        $this->assertSame([$subject->id], array_column($career['subjects'], 'id'));
        $this->assertNotContains($other->id, array_column($career['subjects'], 'id'));
        $this->assertSame(150, $career['subjects'][0]['allowed_minutes']);
    }

    public function test_legacy_load_is_only_linked_for_unique_matching_names_and_consistent_confirmed_values(): void
    {
        $subject = $this->subject(4, null, ['name' => 'Redes del escenario']);
        $teacher = $this->user('DOCENTE_SEGUIMIENTO')->teacher;
        $load = new AcademicLoad([
            'subject' => $subject->name, 'career' => $subject->career->name, 'credits' => 4, 'weekly_class_hours' => 5,
            'academic_period' => 'III Periodo Académico', 'academic_year' => 2026, 'weekdays' => [1, 2, 3, 4, 5],
            'starts_at' => '10:00', 'ends_at' => '10:50', 'valid_from' => '2026-09-01', 'valid_until' => '2026-12-01', 'status' => 'ACTIVE',
        ]);
        $load->teacher_id = $teacher->id;
        $load->recorded_by = $teacher->user_id;
        $load->submission_key = (string) Str::uuid();
        $load->save();
        $duplicate = $subject->replicate();
        $duplicate->save();
        $this->assertSame([$load->id], app(AssociateLegacyAcademicLoads::class)->run()['pending']);
        $this->assertNull($load->fresh()->academic_subject_id);
        $duplicate->update(['status' => 'INACTIVE']);
        $original = $load->only(['subject', 'career', 'credits', 'weekly_class_hours', 'academic_period', 'weekdays', 'starts_at', 'ends_at']);
        $this->assertSame([$load->id], app(AssociateLegacyAcademicLoads::class)->run()['associated']);
        $this->assertSame($original, $load->fresh()->only(array_keys($original)));
        $this->assertSame($subject->id, $load->fresh()->academic_subject_id);
        $this->assertSame([], app(AssociateLegacyAcademicLoads::class)->run()['associated']);
        $this->assertSame(1, $load->changes()->count());
        $this->get(route('docente.carga-academica', ['period' => 'III']))->assertOk()->assertSee($subject->name);
        $unmatched = $load->replicate();
        $unmatched->academic_career_id = null;
        $unmatched->academic_subject_id = null;
        $unmatched->career = 'Abreviatura sin equivalencia confirmada';
        $unmatched->submission_key = (string) Str::uuid();
        $unmatched->save();
        $original = $unmatched->fresh()->getRawOriginal();
        $this->assertSame([$unmatched->id], app(AssociateLegacyAcademicLoads::class)->run()['pending']);
        $this->assertSame($original, $unmatched->fresh()->getRawOriginal());
        $unmatched->update(['career' => $subject->career->name, 'ends_at' => '11:00']);
        $this->assertSame([$unmatched->id], app(AssociateLegacyAcademicLoads::class)->run()['pending']);
        $this->assertNull($unmatched->fresh()->academic_subject_id);
    }
}
