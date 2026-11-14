<?php

require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
if ($db->getDriverName() !== 'mysql' || $db->getDatabaseName() !== 'sistema_practicas_unicah') {
    throw new RuntimeException('Base no autorizada.');
}
$snapshot = storage_path('app/catalog-loads-before.json');
if (in_array('--snapshot', $argv, true)) {
    if (file_exists($snapshot)) throw new RuntimeException('El respaldo existente se conserva; no se sobrescribe.');
    file_put_contents($snapshot, json_encode($db->table('academic_loads')->orderBy('id')->get(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    echo 'Respaldo previo de cargas conservado dentro del repositorio.'.PHP_EOL;
    exit;
}
$before = json_decode(file_get_contents($snapshot), true, 512, JSON_THROW_ON_ERROR);
Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => Database\Seeders\AcademicCatalogSeeder::class, '--no-interaction' => true]);
$state = fn () => json_encode([
    $db->table('academic_careers')->orderBy('id')->get(), $db->table('academic_subjects')->orderBy('id')->get(),
    $db->table('academic_loads')->orderBy('id')->get(), $db->table('academic_load_changes')->orderBy('id')->get(),
], JSON_THROW_ON_ERROR);
$first = $state();
Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => Database\Seeders\AcademicCatalogSeeder::class, '--no-interaction' => true]);
if ($state() !== $first) throw new RuntimeException('El catálogo no es idempotente.');
foreach ($before as $original) {
    $current = (array) $db->table('academic_loads')->find($original['id']);
    foreach ($original as $field => $value) {
        if (in_array($field, ['revision', 'updated_at'], true) && $current['academic_subject_id'] !== null) continue;
        if ($current[$field] !== $value) throw new RuntimeException('Dato histórico alterado: '.$field);
    }
}
echo json_encode([
    'database' => $db->getDatabaseName(), 'original_fields_preserved' => true, 'catalog_idempotent' => true,
    'careers' => App\Models\AcademicCareer::pluck('name')->all(), 'subjects' => App\Models\AcademicSubject::count(),
    'loads' => App\Models\AcademicLoad::get()->map(fn ($load) => ['id' => $load->id, 'subject' => $load->subject, 'career' => $load->career, 'credits' => $load->credits, 'weekly_class_hours' => $load->weekly_class_hours, 'scheduled_minutes' => $load->scheduledWeeklyMinutes(), 'catalog_subject_id' => $load->academic_subject_id])->all(),
], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL;
