<?php

// Escenario de interfaz con datos sintéticos exclusivamente en SQLite en memoria.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'session.driver' => 'array', 'cache.default' => 'array']);
if (Illuminate\Support\Facades\DB::connection()->getDatabaseName() !== ':memory:') throw new RuntimeException('La fixture requiere memoria.');
foreach (glob(database_path('migrations/*.php')) as $file) (require $file)->up();
(new Database\Seeders\DatabaseSeeder())->run();
$careers = App\Models\AcademicCareer::orderBy('id')->get();
foreach ([2, 3] as $index => $credits) {
    App\Models\AcademicSubject::create([
        'academic_career_id' => $careers[$index]->id, 'name' => 'Asignatura del escenario '.($index + 1),
        'credits' => $credits, 'weekly_class_hours' => App\Services\AcademicClassTime::hoursForCredits($credits),
        'source' => 'Fixture sintética de interfaz; nunca se carga en MySQL',
    ]);
}
Illuminate\Support\Facades\Auth::setUser(App\Models\User::whereHas('role', fn ($query) => $query->where('name', 'DOCENTE_SEGUIMIENTO'))->firstOrFail());
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(Illuminate\Http\Request::create('http://127.0.0.1:8000/docente/carga-academica', 'GET'));
if ($response->getStatusCode() !== 200) throw new RuntimeException('Error al renderizar la fixture.');
$html = str_replace('</body>', '<script>document.addEventListener("submit",event=>event.preventDefault());</script></body>', $response->getContent());
file_put_contents(public_path('academic-catalog-fixture.html'), $html);
echo 'Fixture de interfaz creada sin modificar MySQL; retirar public/academic-catalog-fixture.html al terminar.'.PHP_EOL;
