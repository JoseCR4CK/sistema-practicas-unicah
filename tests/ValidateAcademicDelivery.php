<?php

// Validación explícita de integración, separada de PHPUnit y limitada a la base autorizada.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['session.driver' => 'array', 'cache.default' => 'array']);
$connection = Illuminate\Support\Facades\DB::connection();
if ($connection->getDriverName() !== 'mysql' || $connection->getDatabaseName() !== 'sistema_practicas_unicah') {
    throw new RuntimeException('La conexión no corresponde a la base MySQL autorizada.');
}
$tables = ['roles', 'users', 'students', 'teachers', 'companies', 'company_responsibles', 'practices', 'practice_requests', 'academic_documents', 'document_versions', 'visits', 'followups', 'monographs', 'monograph_versions', 'monograph_reviews', 'academic_committees', 'committee_members', 'defenses', 'defense_options', 'defense_availabilities', 'defense_class_blocks', 'academic_loads', 'academic_load_changes'];
$snapshot = function () use ($connection, $tables) {
    return collect($tables)->mapWithKeys(fn ($table) => [$table => [
        'count' => $connection->table($table)->count(),
        'hash' => hash('sha256', json_encode($connection->table($table)->orderBy('id')->get(), JSON_THROW_ON_ERROR)),
    ]])->all();
};
$before = $snapshot();
$seeders = [Database\Seeders\AcademicFlowSeeder::class, Database\Seeders\AcademicClosingSeeder::class, Database\Seeders\DevelopmentTextCleanupSeeder::class];
$connection->transaction(function () use ($app, $connection, $before, $snapshot, $seeders, &$after) {
    foreach ($seeders as $seeder) {
        $app->make($seeder)->run();
    }
    $after = $snapshot();
    foreach (['roles', 'users', 'students', 'teachers', 'companies', 'company_responsibles', 'practices', 'academic_loads', 'academic_load_changes'] as $table) {
        if ($before[$table] !== $after[$table]) {
            throw new RuntimeException('El seeder alteró información base: '.$table);
        }
    }
    foreach ($seeders as $seeder) {
        $app->make($seeder)->run();
    }
    if ($after !== $snapshot()) {
        throw new RuntimeException('Los seeders no fueron idempotentes.');
    }
});

// Probar escritura/lectura de carga y cruce de horario en MySQL, sin dejar datos ficticios.
$connection->beginTransaction();
try {
    $teacher = App\Models\Teacher::firstOrFail();
    $date = today()->addDay();
    $load = new App\Models\AcademicLoad([
        'academic_period' => 'Validación transaccional', 'academic_year' => $date->year,
        'subject' => 'Validación transaccional', 'weekdays' => [$date->dayOfWeekIso],
        'starts_at' => '12:00', 'ends_at' => '13:00', 'valid_from' => $date, 'valid_until' => $date,
        'credits' => 3, 'weekly_class_hours' => 5, 'status' => 'ACTIVE',
    ]);
    $load->teacher_id = $teacher->id;
    $load->recorded_by = $teacher->user_id;
    $load->submission_key = (string) Illuminate\Support\Str::uuid();
    $load->save();
    if ($load->fresh()->weekdays !== [$date->dayOfWeekIso] || $load->fresh()->credits !== '3.00' || $load->fresh()->weekly_class_hours !== '5.00') {
        throw new RuntimeException('La carga no conservó sus campos independientes.');
    }
    $committee = new App\Models\AcademicCommittee();
    $committee->setRelation('members', collect([new App\Models\CommitteeMember(['user_id' => $teacher->user_id])]));
    $defense = new App\Models\Defense();
    $defense->setRelation('committee', $committee);
    $workflow = app(App\Services\DefenseScheduling::class);
    if (!$workflow->hasClassConflict($defense, $date->copy()->setTime(12, 0), $date->copy()->setTime(13, 0))) {
        throw new RuntimeException('No se detectó el cruce académico en MySQL.');
    }
} finally {
    $connection->rollBack();
}
if ($after !== $snapshot()) {
    throw new RuntimeException('La validación dejó datos residuales.');
}
echo json_encode(['database' => $connection->getDatabaseName(), 'seeders' => $seeders, 'idempotent' => true, 'base_records_preserved' => true, 'mysql_load_write_and_conflict' => true, 'probe_rolled_back' => true, 'before' => array_map(fn ($state) => $state['count'], $before), 'after' => array_map(fn ($state) => $state['count'], $after)], JSON_THROW_ON_ERROR).PHP_EOL;
