<?php

// Fixture visual desechable: no consulta ni modifica registros académicos.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$steps = collect(App\Models\Practice::STAGES)->map(fn ($stage) => [
    'name' => $stage === 'Documentación' ? 'Documentos' : $stage,
    'state' => str_repeat('Estado con observaciones extensas ', 4),
    'icon' => 'bi-journal-text',
    'class' => '',
])->all();
$process = Illuminate\Support\Facades\Blade::render('<x-practice-process :steps="$steps" />', ['steps' => $steps]);
$name = str_repeat('Nombre compuesto y apellidos extensos ', 3);
$company = str_repeat('DenominaciónComercialExtensa', 8);
$html = '<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Revisión visual de contenido extenso</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="/css/app.css"><link rel="stylesheet" href="/css/presentation.css"></head><body><main class="main-content">';
$html .= '<header class="page-header"><div><h1>'.e($name).'</h1><p>Comprobación visual aislada, sin datos de personas.</p></div></header>';
$html .= '<section class="teacher-load-profile"><div><strong>'.e($company).'</strong><small>Sin información adicional</small></div></section>';
$html .= '<section class="content-card practice-stages-card">'.$process.'</section>';
$html .= '<section class="content-card mt-4"><div class="table-container table-responsive"><table class="data-table"><thead><tr><th>Nombre</th><th>Cuenta</th><th>Empresa</th><th>Estado</th></tr></thead><tbody><tr><td><strong>'.e($name).'</strong><small>Sin datos de contacto</small></td><td>0000-0000-00000</td><td>'.e($company).'</td><td><span class="badge info">Estado con observación extensa</span></td></tr></tbody></table></div></section>';
$html .= '</main></body></html>';
file_put_contents(public_path('visual-audit-fixture.html'), $html);
echo 'Fixture creado dentro del repositorio; retirar public/visual-audit-fixture.html después de la revisión.'.PHP_EOL;
