<?php

// Renderizar interfaces reales sin MySQL ni escrituras académicas persistentes.
require dirname(__DIR__).'/vendor/autoload.php';
$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'session.driver' => 'array', 'cache.default' => 'array']);
if (Illuminate\Support\Facades\DB::connection()->getDatabaseName() !== ':memory:') throw new RuntimeException('Solo SQLite en memoria.');
foreach (glob(database_path('migrations/*.php')) as $file) (require $file)->up();
(new Database\Seeders\DatabaseSeeder())->run();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$directory = public_path('frontend-review');
if (!is_dir($directory)) mkdir($directory);
$cases = [['login', null, '/']];
$roles = [
    'COORDINACION' => ['coordinacion', 'coordinacion/estudiantes', 'coordinacion/empresas', 'coordinacion/practicas', 'coordinacion/solicitudes', 'coordinacion/documentos', 'coordinacion/visitas', 'coordinacion/monografias', 'coordinacion/ternas', 'coordinacion/defensas', 'coordinacion/docentes/1/carga-academica'],
    'DOCENTE_SEGUIMIENTO' => ['docente', 'docente/practicantes', 'docente/practicantes/1', 'docente/visitas', 'docente/seguimientos', 'docente/carga-academica', 'docente/monografias'],
    'ESTUDIANTE' => ['estudiante', 'estudiante/practica', 'estudiante/solicitudes', 'estudiante/documentos', 'estudiante/visita', 'estudiante/monografia', 'estudiante/terna', 'estudiante/defensa'],
    'JEFE_INMEDIATO' => ['jefe-inmediato', 'jefe-inmediato/modulo/practicante', 'jefe-inmediato/modulo/empresa', 'jefe-inmediato/modulo/seguimiento'],
];
foreach ($roles as $role => $paths) {
    $user = App\Models\User::whereHas('role', fn ($query) => $query->where('name', $role))->firstOrFail();
    foreach ($paths as $path) $cases[] = [str_replace('/', '-', $path), $user, '/'.$path];
}
$pages = [];
foreach ($cases as [$name, $user, $path]) {
    Illuminate\Support\Facades\Auth::forgetGuards();
    if ($user) Illuminate\Support\Facades\Auth::setUser($user);
    $response = $kernel->handle(Illuminate\Http\Request::create('http://127.0.0.1:8000'.$path));
    if ($response->getStatusCode() !== 200) throw new RuntimeException($path.' respondió '.$response->getStatusCode());
    $html = $response->getContent();
    foreach ($cases as [$target, , $url]) {
        $html = str_replace('href="http://127.0.0.1:8000'.$url.'"', 'href="/frontend-review/'.$target.'.html"', $html);
    }
    // Solo presentación: impedir que los formularios de la fixture escriban registros.
    $html = str_replace('</body>', '<script>document.addEventListener("submit", event => event.preventDefault());</script></body>', $html);
    file_put_contents($directory.'/'.$name.'.html', $html);
    $pages[] = ['name' => $name, 'route' => $path, 'file' => '/frontend-review/'.$name.'.html'];
}
file_put_contents($directory.'/pages.json', json_encode($pages, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo count($pages)." vistas reales renderizadas en SQLite en memoria; archivos temporales ignorados por Git.\n";
