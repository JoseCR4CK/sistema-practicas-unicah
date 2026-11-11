<?php

// Comprobación de lectura contra la base MySQL autorizada.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['session.driver' => 'array', 'cache.default' => 'array']);

$connection = Illuminate\Support\Facades\DB::connection();
if ($connection->getDriverName() !== 'mysql' || $connection->getDatabaseName() !== 'sistema_practicas_unicah') {
    throw new RuntimeException('La conexión no corresponde a la base MySQL autorizada.');
}

$counts = [];
foreach ([App\Models\PracticeRequest::class, App\Models\AcademicDocument::class, App\Models\DocumentVersion::class, App\Models\Visit::class, App\Models\Followup::class, App\Models\Monograph::class, App\Models\MonographVersion::class, App\Models\AcademicCommittee::class, App\Models\Defense::class, App\Models\AcademicLoad::class] as $model) {
    $counts[class_basename($model)] = $model::count();
}
$coordinator = App\Models\User::whereHas('role', fn ($query) => $query->where('name', 'COORDINACION'))->firstOrFail();
$teacher = App\Models\User::whereHas('role', fn ($query) => $query->where('name', 'DOCENTE_SEGUIMIENTO'))->firstOrFail();
$student = App\Models\User::whereHas('role', fn ($query) => $query->where('name', 'ESTUDIANTE'))->firstOrFail();

$cases = [];
foreach (['/coordinacion', '/coordinacion/estudiantes', '/coordinacion/solicitudes', '/coordinacion/documentos', '/coordinacion/visitas', '/coordinacion/seguimientos'] as $url) {
    $cases[] = [$coordinator, $url, 200];
}
foreach (['/docente', '/docente/practicantes', '/docente/alertas', '/docente/visitas', '/docente/seguimientos'] as $url) {
    $cases[] = [$teacher, $url, 200];
}
foreach (['/estudiante', '/estudiante/practica', '/estudiante/solicitudes', '/estudiante/documentos', '/estudiante/visita'] as $url) {
    $cases[] = [$student, $url, 200];
}
foreach (['/coordinacion/empresas', '/coordinacion/docentes', '/coordinacion/practicas', '/coordinacion/estudiantes/informe', '/coordinacion/monografias', '/coordinacion/ternas', '/coordinacion/defensas'] as $url) {
    $cases[] = [$coordinator, $url, 200];
}
foreach (['/docente/monografias', '/docente/ternas', '/docente/defensas', '/docente/carga-academica'] as $url) {
    $cases[] = [$teacher, $url, 200];
}
foreach (['/estudiante/monografia', '/estudiante/terna', '/estudiante/defensa'] as $url) {
    $cases[] = [$student, $url, 200];
}
$cases[] = [$coordinator, '/coordinacion/docentes/'.$teacher->teacher->id.'/carga-academica', 200];
$cases[] = [$coordinator, '/coordinacion/docentes/'.$teacher->teacher->id, 200];
foreach ([App\Models\Monograph::class => '/coordinacion/monografias/', App\Models\AcademicCommittee::class => '/coordinacion/ternas/', App\Models\Defense::class => '/coordinacion/defensas/'] as $model => $prefix) {
    $cases[] = [$coordinator, $prefix.'999999999', 404];
    foreach ($model::all() as $record) {
        $cases[] = [$coordinator, $prefix.$record->id, 200];
    }
}
foreach (App\Models\Monograph::with('practice.teacher.user')->get() as $monograph) {
    $cases[] = [$monograph->practice->student->user, '/estudiante/monografia/'.$monograph->id, 200];
    if ($monograph->practice->teacher) {
        $cases[] = [$monograph->practice->teacher->user, '/docente/monografias/'.$monograph->id, 200];
        $cases[] = [$monograph->practice->teacher->user, '/docente/practicantes/'.$monograph->practice->student_id, 200];
    }
}
foreach (['/coordinacion/solicitudes/999999999', '/coordinacion/documentos/999999999', '/coordinacion/visitas/999999999', '/coordinacion/seguimientos/999999999'] as $url) {
    $cases[] = [$coordinator, $url, 404];
}
foreach ([App\Models\PracticeRequest::class => '/coordinacion/solicitudes/', App\Models\AcademicDocument::class => '/coordinacion/documentos/', App\Models\Visit::class => '/coordinacion/visitas/', App\Models\Followup::class => '/coordinacion/seguimientos/'] as $model => $prefix) {
    $record = $model::firstOrFail();
    $cases[] = [$coordinator, $prefix.$record->id, 200];
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$scripts = [];
foreach ($cases as [$user, $url, $expected]) {
    Illuminate\Support\Facades\Auth::guard()->setUser($user);
    $response = $kernel->handle(Illuminate\Http\Request::create($url, 'GET'));
    if ($response->getStatusCode() !== $expected) {
        throw new RuntimeException($url.' respondió '.$response->getStatusCode().' en vez de '.$expected);
    }
    if ($expected === 200) {
        preg_match_all('/<script\b[^>]*>(.*?)<\/script>/si', $response->getContent(), $matches);
        array_push($scripts, ...$matches[1]);
    }
}

$result = ['counts' => $counts, 'pages' => count($cases), 'scripts_count' => count($scripts)];
if (in_array('--scripts', $argv, true)) {
    $result['scripts'] = $scripts;
}
echo json_encode($result, JSON_THROW_ON_ERROR).PHP_EOL;
