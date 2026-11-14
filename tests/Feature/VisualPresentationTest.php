<?php

namespace Tests\Feature;

use App\Models\Monograph;
use App\Models\Practice;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VisualPresentationTest extends TestCase
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

    private function xpath(string $html): \DOMXPath
    {
        $document = new \DOMDocument();
        @$document->loadHTML('<?xml encoding="UTF-8">'.$html);

        return new \DOMXPath($document);
    }

    public function test_all_five_process_panels_keep_eight_named_stages_in_three_unnamed_groups(): void
    {
        $monograph = Monograph::firstOrFail();
        $practice = $monograph->practice;
        $coordinator = User::whereHas('role', fn ($query) => $query->where('name', 'COORDINACION'))->firstOrFail();
        $before = $practice->getRawOriginal();
        $cases = [
            [$coordinator, route('coordinacion.estudiante', $practice->student_id)],
            [$coordinator, route('coordinacion.practica', $practice)],
            [$practice->teacher->user, route('docente.practicante', $practice->student_id)],
            [$practice->student->user, route('estudiante.dashboard')],
            [$practice->student->user, route('estudiante.practica')],
        ];
        foreach ($cases as [$user, $url]) {
            $response = $this->actingAs($user)->get($url)->assertOk()->assertSee('En revisión');
            $xpath = $this->xpath($response->getContent());
            $groups = $xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " practice-process-group ")]');
            $this->assertSame(3, $groups->length);
            foreach ([3, 3, 2] as $index => $count) {
                $this->assertSame($count, $xpath->query('./*[@role="listitem"]', $groups->item($index))->length);
            }
            $names = $xpath->query('//*[@data-stage]');
            $this->assertSame(8, $names->length);
            foreach (['Solicitud', 'Documentos', 'Empresa', 'Práctica', 'Visita', 'Monografía', 'Defensa', 'Finalización'] as $index => $name) {
                $step = $names->item($index);
                $this->assertSame($name, $step->getAttribute('data-stage'));
                $this->assertNotSame('', trim($xpath->query('.//span[contains(@class,"practice-process-state")]', $step)->item(0)->textContent));
                $this->assertSame(1, $xpath->query('.//*[contains(@class,"practice-process-icon")]', $step)->length);
            }
            $response->assertDontSee('Bloque 1')->assertDontSee('Bloque 2')->assertDontSee('Bloque 3');
        }
        $this->assertSame($before, $practice->fresh()->getRawOriginal());
    }

    public function test_shared_process_preserves_and_escapes_long_dynamic_states_and_existing_links(): void
    {
        $text = str_repeat('Estado con observaciones extensas ', 8).'<script>alert(1)</script>';
        $steps = collect(Practice::STAGES)->map(fn ($stage) => [
            'name' => $stage, 'state' => $text, 'icon' => 'bi-journal-text', 'class' => 'active',
            'href' => route('estudiante.practica'),
        ])->all();
        $html = Blade::render('<x-practice-process :steps="$steps" />', ['steps' => $steps]);
        $xpath = $this->xpath($html);
        $this->assertSame(0, $xpath->query('//script')->length);
        $states = $xpath->query('//span[contains(@class,"practice-process-state")]');
        $this->assertSame(8, $states->length);
        foreach ($states as $state) {
            $this->assertSame($text, $state->textContent);
        }
        $this->assertSame(8, $xpath->query('//a[@href="'.route('estudiante.practica').'"]')->length);
    }

    public function test_all_portals_expose_accessible_controls_for_the_shared_mobile_navigation(): void
    {
        foreach (['COORDINACION' => 'coordinacion.dashboard', 'DOCENTE_SEGUIMIENTO' => 'docente.dashboard', 'ESTUDIANTE' => 'estudiante.dashboard', 'JEFE_INMEDIATO' => 'jefe.dashboard'] as $role => $route) {
            $user = User::whereHas('role', fn ($query) => $query->where('name', $role))->firstOrFail();
            $response = $this->actingAs($user)->get(route($route))->assertOk();
            $xpath = $this->xpath($response->getContent());
            $this->assertSame(1, $xpath->query('//button[@id="menuButton" and @aria-controls="sidebar" and @aria-label="Abrir menú"]')->length);
            $this->assertSame(1, $xpath->query('//button[@id="sidebarClose" and @aria-label="Cerrar menú"]')->length);
            $this->assertSame(1, $xpath->query('//*[@id="sidebarOverlay"]')->length);
            $this->assertSame(1, $xpath->query('//script[contains(@src,"js/navigation.js")]')->length);
        }
    }
}
