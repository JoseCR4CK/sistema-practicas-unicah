
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Coordination\CompanyController;
use App\Http\Controllers\Coordination\PracticeController;
use App\Http\Controllers\Coordination\StudentController;
use App\Http\Controllers\Coordination\TeacherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTENTICACIÓN
|--------------------------------------------------------------------------
*/

Route::get('/', [
    AuthController::class,
    'showLogin'
])->name('login');

Route::post('/login', [
    AuthController::class,
    'login'
])->name('login.attempt');

Route::post('/logout', [
    AuthController::class,
    'logout'
])->middleware('auth')->name('logout');


/*
|--------------------------------------------------------------------------
| COORDINACIÓN ACADÉMICA / ROOT
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:COORDINACION,ROOT',
])->group(function () {

Route::get('/coordinacion', [StudentController::class, 'dashboard'])
    ->name('coordinacion.dashboard');

Route::get('/coordinacion/estudiantes', [StudentController::class, 'index'])
    ->name('coordinacion.estudiantes');

Route::get('/coordinacion/estudiantes/informe', [StudentController::class, 'report'])
    ->name('coordinacion.estudiantes.informe');

Route::get('/coordinacion/estudiantes/{student}', [StudentController::class, 'show'])
    ->whereNumber('student')->name('coordinacion.estudiante');

Route::get('/coordinacion/docentes', [TeacherController::class, 'index'])->name('coordinacion.docentes');
Route::get('/coordinacion/docentes/{teacher}', [TeacherController::class, 'show'])
    ->whereNumber('teacher')->name('coordinacion.docente');

Route::get('/coordinacion/empresas', [CompanyController::class, 'index'])->name('coordinacion.empresas');
Route::post('/coordinacion/empresas', [CompanyController::class, 'store'])->name('coordinacion.empresas.store');
Route::get('/coordinacion/empresas/{company}', [CompanyController::class, 'show'])
    ->whereNumber('company')->name('coordinacion.empresa');
Route::put('/coordinacion/empresas/{company}', [CompanyController::class, 'update'])
    ->whereNumber('company')->name('coordinacion.empresas.update');
Route::post('/coordinacion/empresas/{company}/responsables', [CompanyController::class, 'storeResponsible'])
    ->whereNumber('company')->name('coordinacion.empresas.responsables.store');
Route::put('/coordinacion/empresas/{company}/responsables/{responsible}', [CompanyController::class, 'updateResponsible'])
    ->whereNumber(['company', 'responsible'])->scopeBindings()->name('coordinacion.empresas.responsables.update');

Route::get('/coordinacion/solicitudes', function () {
    return view('coordinacion.solicitudes');
})->name('coordinacion.solicitudes');


Route::get('/coordinacion/solicitudes/{id}', function ($id) {

    return view('coordinacion.solicitud-detalle', [
        'solicitudId' => (int) $id
    ]);

})->name('coordinacion.solicitud');

Route::get('/coordinacion/practicas', [PracticeController::class, 'index'])->name('coordinacion.practicas');
Route::get('/coordinacion/practicas/{practice}', [PracticeController::class, 'show'])
    ->whereNumber('practice')->name('coordinacion.practica');

Route::get('/coordinacion/documentos', function () {
    return view('coordinacion.documentos');
})->name('coordinacion.documentos');


Route::get('/coordinacion/documentos/{id}', function ($id) {

    return view('coordinacion.documento-detalle', [
        'documentoId' => (int) $id
    ]);

})->name('coordinacion.documento');

Route::get('/coordinacion/visitas', function () {
    return view('coordinacion.visitas');
})->name('coordinacion.visitas');


Route::get('/coordinacion/visitas/{id}', function ($id) {

    return view('coordinacion.visita-detalle', [
        'visitaId' => (int) $id
    ]);

})->name('coordinacion.visita');

Route::get('/coordinacion/modulo/{modulo}', function ($modulo) {

    $modulos = [

        'solicitudes' => [
            'titulo' => 'Solicitudes',
            'descripcion' => 'Gestión de solicitudes de práctica profesional.'
        ],

        'practicas' => [
            'titulo' => 'Prácticas',
            'descripcion' => 'Seguimiento general de las prácticas profesionales.'
        ],

        'empresas' => [
            'titulo' => 'Empresas',
            'descripcion' => 'Administración y validación de empresas.'
        ],

        'documentos' => [
            'titulo' => 'Documentos',
            'descripcion' => 'Revisión de documentos enviados por los estudiantes.'
        ],

        'ternas' => [
            'titulo' => 'Ternas',
            'descripcion' => 'Asignación y seguimiento de ternas académicas.'
        ],

        'visitas' => [
            'titulo' => 'Visitas',
            'descripcion' => 'Programación y seguimiento de visitas de práctica.'
        ],

        'monografias' => [
            'titulo' => 'Monografías',
            'descripcion' => 'Seguimiento del proceso de revisión de monografías.'
        ],

        'defensas' => [
            'titulo' => 'Defensas',
            'descripcion' => 'Gestión y calendarización de defensas.'
        ],

        'reportes' => [
            'titulo' => 'Reportes',
            'descripcion' => 'Consulta de información y reportes del sistema.'
        ],

    ];

    abort_unless(isset($modulos[$modulo]), 404);

    return view(
        'coordinacion.modulo',
        $modulos[$modulo]
    );

})->name('coordinacion.modulo');

});


/*
|--------------------------------------------------------------------------
| ESTUDIANTE
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:ESTUDIANTE',
])->group(function () {

Route::get('/estudiante', function () {
    return view('estudiante.dashboard');
})->name('estudiante.dashboard');

Route::get('/estudiante/practica', function () {
    return view('estudiante.practica');
})->name('estudiante.practica');

Route::get('/estudiante/documentos', function () {
    return view('estudiante.documentos');
})->name('estudiante.documentos');

Route::get('/estudiante/monografia', function () {
    return view('estudiante.monografia');
})->name('estudiante.monografia');

Route::get('/estudiante/defensa', function () {
    return view('estudiante.defensa');
})->name('estudiante.defensa');

Route::get('/estudiante/visita', function () {
    return view('estudiante.visita');
})->name('estudiante.visita');

Route::get('/estudiante/empresa', function () {
    return view('estudiante.empresa');
})->name('estudiante.empresa');

Route::get('/estudiante/notas', function () {
    return view('estudiante.notas');
})->name('estudiante.notas');

Route::get('/estudiante/pendientes', function () {
    return view('estudiante.pendientes');
})->name('estudiante.pendientes');

Route::get('/estudiante/bitacora', function () {
    return view('estudiante.bitacora');
})->name('estudiante.bitacora');

Route::get('/estudiante/agenda', function () {
    return view('estudiante.agenda');
})->name('estudiante.agenda');

Route::get('/estudiante/docente', function () {
    return view('estudiante.docente');
})->name('estudiante.docente');

Route::get('/estudiante/modulo/{modulo}', function ($modulo) {

    $modulos = [

        'agenda' => [
            'titulo' => 'Mi agenda',
            'descripcion' => 'Organiza fechas, actividades y recordatorios relacionados con tu práctica profesional.'
        ],

        'notas' => [
            'titulo' => 'Mis notas',
            'descripcion' => 'Espacio privado para guardar ideas, recordatorios y anotaciones personales.'
        ],

        'pendientes' => [
            'titulo' => 'Mis pendientes',
            'descripcion' => 'Organiza las tareas personales que necesitas completar durante tu práctica.'
        ],

        'bitacora' => [
            'titulo' => 'Mi bitácora',
            'descripcion' => 'Registra cronológicamente las actividades y experiencias realizadas durante tu práctica.'
        ],

        'docente' => [
            'titulo' => 'Docente asignado',
            'descripcion' => 'Consulta la información del docente responsable del seguimiento académico de tu práctica.'
        ],

        'historial' => [
            'titulo' => 'Historial',
            'descripcion' => 'Consulta de actividades y cambios realizados durante tu proceso de práctica.'
        ],

        'perfil' => [
            'titulo' => 'Mi perfil',
            'descripcion' => 'Consulta de información personal y académica del estudiante.'
        ],

    ];

    abort_unless(isset($modulos[$modulo]), 404);

    return view(
        'estudiante.modulo',
        $modulos[$modulo]
    );

})->name('estudiante.modulo');

});


/*
|--------------------------------------------------------------------------
| JEFE INMEDIATO
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:JEFE_INMEDIATO',
])->group(function () {

Route::get('/jefe-inmediato', function () {
    return view('jefe_inmediato.dashboard');
})->name('jefe.dashboard');

Route::get('/jefe-inmediato/modulo/{modulo}', function ($modulo) {

    $modulos = [

        'practicante' => [
            'titulo' => 'Practicante',
            'descripcion' => 'Consulta de información del estudiante asignado a la empresa.'
        ],

        'empresa' => [
            'titulo' => 'Empresa',
            'descripcion' => 'Consulta de la información registrada de la empresa.'
        ],

        'seguimiento' => [
            'titulo' => 'Seguimiento',
            'descripcion' => 'Consulta del estado y seguimiento de la práctica profesional.'
        ],

    ];

    abort_unless(isset($modulos[$modulo]), 404);

    return view(
        'jefe_inmediato.modulo',
        $modulos[$modulo]
    );

})->name('jefe.modulo');

});


/*
|--------------------------------------------------------------------------
| DOCENTE DE SEGUIMIENTO
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:DOCENTE_SEGUIMIENTO',
])->group(function () {

Route::get('/docente', function () {
    return view('docente.dashboard');
})->name('docente.dashboard');

Route::get('/docente/practicantes', function () {
    return view('docente.practicantes');
})->name('docente.practicantes');


Route::get('/docente/practicantes/{id}', function ($id) {

    $practicantes = [

        1 => [
            'nombre' => 'Andrea Martínez',
            'cuenta' => '0801-2022-00458',
            'carrera' => 'Ingeniería en Ciencias de la Computación',
            'empresa' => 'Grupo Tecnológico S.A.',
            'etapa' => 'Práctica',
            'progreso' => 58,
            'inicio' => '01/09/2026',
            'finalizacion' => '30/11/2026',

            'visita_estado' => 'Habilitada',
            'monografia_estado' => 'Pendiente',
            'defensa_estado' => 'Pendiente',
        ],

        2 => [
            'nombre' => 'Carlos Rodríguez',
            'cuenta' => '0801-2021-00231',
            'carrera' => 'Ingeniería Industrial',
            'empresa' => 'Corporación Industrial HN',
            'etapa' => 'Documentación',
            'progreso' => 32,
            'inicio' => '15/09/2026',
            'finalizacion' => '15/12/2026',

            'visita_estado' => 'No habilitada',
            'monografia_estado' => 'Pendiente',
            'defensa_estado' => 'Pendiente',
        ],

        3 => [
            'nombre' => 'María López',
            'cuenta' => '0801-2022-00671',
            'carrera' => 'Administración de Empresas',
            'empresa' => 'Inversiones del Valle S. de R.L.',
            'etapa' => 'Monografía',
            'progreso' => 79,
            'inicio' => '20/08/2026',
            'finalizacion' => '20/11/2026',

            'visita_estado' => 'Realizada',
            'monografia_estado' => 'En revisión',
            'defensa_estado' => 'Pendiente',
        ],

    ];

    abort_unless(isset($practicantes[$id]), 404);


    // IMPORTANTE: obtener primero el practicante seleccionado
    $practicante = $practicantes[$id];


    $etapasConPracticaIniciada = [
        'Práctica',
        'Visita',
        'Monografía',
        'Defensa',
        'Finalización'
    ];


    if (
        in_array(
            $practicante['etapa'],
            $etapasConPracticaIniciada
        )
    ) {

        $inicio = \Carbon\Carbon::createFromFormat(
            'd/m/Y',
            $practicante['inicio']
        )->startOfDay();

        $hoy = now()->startOfDay();


        $practicante['dia'] =
            $hoy->greaterThanOrEqualTo($inicio)
                ? $inicio->diffInDays($hoy) + 1
                : 0;

    } else {

        $practicante['dia'] = null;

    }

    return view('docente.seguimiento', [
        'practicante' => $practicante
    ]);

})->name('docente.practicante');


Route::get('/docente/visitas', function () {
    return view('docente.visitas');
})->name('docente.visitas');


Route::get('/docente/visitas/{id}', function ($id) {

    return view('docente.visita-detalle', [
        'visitaId' => (int) $id
    ]);

})->name('docente.visita');


Route::get('/docente/seguimientos', function () {

    return view('docente.seguimientos');

})->name('docente.seguimientos');


Route::get('/docente/alertas', function () {

    return view('docente.alertas');

})->name('docente.alertas');

Route::get('/docente/carga-academica', function () {
    return view('docente.carga-academica');
})->name('docente.carga-academica');

});
