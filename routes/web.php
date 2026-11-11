
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\TeacherPortalController;
use App\Http\Controllers\AcademicDocumentController;
use App\Http\Controllers\Coordination\CompanyController;
use App\Http\Controllers\Coordination\PracticeController;
use App\Http\Controllers\Coordination\StudentController;
use App\Http\Controllers\Coordination\TeacherController;
use App\Http\Controllers\Coordination\PracticeRequestController;
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

Route::get('/coordinacion/solicitudes', [PracticeRequestController::class, 'index'])->name('coordinacion.solicitudes');
Route::get('/coordinacion/solicitudes/{application}', [PracticeRequestController::class, 'show'])->whereNumber('application')->name('coordinacion.solicitud');
Route::post('/coordinacion/solicitudes/{application}/revision', [PracticeRequestController::class, 'review'])->whereNumber('application')->name('coordinacion.solicitud.review');

Route::get('/coordinacion/practicas', [PracticeController::class, 'index'])->name('coordinacion.practicas');
Route::get('/coordinacion/practicas/{practice}', [PracticeController::class, 'show'])
    ->whereNumber('practice')->name('coordinacion.practica');

Route::get('/coordinacion/documentos', [AcademicDocumentController::class, 'index'])->name('coordinacion.documentos');
Route::get('/coordinacion/documentos/{document}', [AcademicDocumentController::class, 'show'])->whereNumber('document')->name('coordinacion.documento');
Route::post('/coordinacion/documentos/{document}/revision', [AcademicDocumentController::class, 'review'])->whereNumber('document')->name('coordinacion.documento.review');

Route::get('/coordinacion/visitas', [VisitController::class, 'index'])->name('coordinacion.visitas');
Route::get('/coordinacion/visitas/{visit}', [VisitController::class, 'show'])->whereNumber('visit')->name('coordinacion.visita');

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

Route::get('/estudiante/solicitudes', [StudentApplicationController::class, 'index'])->name('estudiante.solicitudes');
Route::post('/estudiante/solicitudes', [StudentApplicationController::class, 'store'])->name('estudiante.solicitudes.store');
Route::get('/estudiante/solicitudes/{application}', [StudentApplicationController::class, 'show'])->whereNumber('application')->name('estudiante.solicitud');

Route::get('/estudiante', function () {
    return view('estudiante.dashboard');
})->name('estudiante.dashboard');

Route::get('/estudiante/practica', function () {
    return view('estudiante.practica');
})->name('estudiante.practica');

Route::get('/estudiante/documentos', [AcademicDocumentController::class, 'index'])->name('estudiante.documentos');
Route::post('/estudiante/documentos', [AcademicDocumentController::class, 'storeVersion'])->name('estudiante.documentos.store');
Route::get('/estudiante/documentos/{document}', [AcademicDocumentController::class, 'show'])->whereNumber('document')->name('estudiante.documento');

Route::get('/estudiante/monografia', function () {
    return view('estudiante.monografia');
})->name('estudiante.monografia');

Route::get('/estudiante/defensa', function () {
    return view('estudiante.defensa');
})->name('estudiante.defensa');

Route::get('/estudiante/visita', [VisitController::class, 'index'])->name('estudiante.visita');
Route::get('/estudiante/visitas/{visit}', [VisitController::class, 'show'])->whereNumber('visit')->name('estudiante.visita.show');

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

Route::get('/docente', [TeacherPortalController::class, 'dashboard'])->name('docente.dashboard');
Route::get('/docente/practicantes', [TeacherPortalController::class, 'practitioners'])->name('docente.practicantes');
Route::get('/docente/practicantes/{id}', [TeacherPortalController::class, 'practitioner'])->whereNumber('id')->name('docente.practicante');
Route::get('/docente/visitas', [VisitController::class, 'index'])->name('docente.visitas');
Route::post('/docente/visitas', [VisitController::class, 'store'])->name('docente.visitas.store');
Route::get('/docente/visitas/{visit}', [VisitController::class, 'show'])->whereNumber('visit')->name('docente.visita');
Route::post('/docente/visitas/{visit}', [VisitController::class, 'update'])->whereNumber('visit')->name('docente.visita.update');

Route::get('/docente/seguimientos', function () {

    return view('docente.seguimientos');

})->name('docente.seguimientos');


Route::get('/docente/alertas', [TeacherPortalController::class, 'alerts'])->name('docente.alertas');

Route::get('/docente/carga-academica', function () {
    return view('docente.carga-academica');
})->name('docente.carga-academica');

});
