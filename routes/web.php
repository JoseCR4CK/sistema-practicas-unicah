
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DefenseController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\MonographController;
use App\Http\Controllers\StudentFlowController;
use App\Http\Controllers\FollowupController;
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

Route::get('/coordinacion/seguimientos', [FollowupController::class, 'index'])->name('coordinacion.seguimientos');
Route::get('/coordinacion/seguimientos/{followup}', [FollowupController::class, 'show'])->whereNumber('followup')->name('coordinacion.seguimiento');

Route::middleware('closing.schema:monographs')->group(function () {
    Route::get('/coordinacion/monografias', [MonographController::class, 'index'])->name('coordinacion.monografias');
    Route::get('/coordinacion/monografias/{monograph}', [MonographController::class, 'show'])->whereNumber('monograph')->name('coordinacion.monografia.show');
    Route::post('/coordinacion/monografias/{monograph}/revision', [MonographController::class, 'review'])->whereNumber('monograph')->name('coordinacion.monografia.review');
});

Route::middleware('closing.schema:committees')->group(function () {
    Route::get('/coordinacion/ternas', [CommitteeController::class, 'index'])->name('coordinacion.ternas');
    Route::post('/coordinacion/ternas', [CommitteeController::class, 'store'])->name('coordinacion.ternas.store');
    Route::get('/coordinacion/ternas/{committee}', [CommitteeController::class, 'show'])->whereNumber('committee')->name('coordinacion.terna.show');
    Route::put('/coordinacion/ternas/{committee}', [CommitteeController::class, 'update'])->whereNumber('committee')->name('coordinacion.terna.update');
});

Route::middleware('closing.schema:defenses')->group(function () {
    Route::get('/coordinacion/defensas', [DefenseController::class, 'index'])->name('coordinacion.defensas');
    Route::post('/coordinacion/defensas', [DefenseController::class, 'store'])->name('coordinacion.defensas.store');
    Route::get('/coordinacion/defensas/{defense}', [DefenseController::class, 'show'])->whereNumber('defense')->name('coordinacion.defensa.show');
    Route::put('/coordinacion/defensas/{defense}/duracion', [DefenseController::class, 'duration'])->whereNumber('defense')->name('coordinacion.defensa.duration');
    Route::post('/coordinacion/defensas/{defense}/opciones', [DefenseController::class, 'propose'])->whereNumber('defense')->name('coordinacion.defensa.options');
    Route::post('/coordinacion/defensas/{defense}/opciones/{option}/confirmar', [DefenseController::class, 'confirm'])->whereNumber(['defense', 'option'])->scopeBindings()->name('coordinacion.defensa.confirm');
    Route::post('/coordinacion/defensas/{defense}/realizada', [DefenseController::class, 'performed'])->whereNumber('defense')->name('coordinacion.defensa.performed');
});

Route::get('/coordinacion/visitas', [VisitController::class, 'index'])->name('coordinacion.visitas');
Route::get('/coordinacion/visitas/{visit}', [VisitController::class, 'show'])->whereNumber('visit')->name('coordinacion.visita');

Route::get('/coordinacion/modulo/{modulo}', function ($modulo) {
    if (in_array($modulo, ['ternas', 'monografias', 'defensas'], true)) {
        return redirect()->route('coordinacion.'.$modulo);
    }

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


        'visitas' => [
            'titulo' => 'Visitas',
            'descripcion' => 'Programación y seguimiento de visitas de práctica.'
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

Route::get('/estudiante', [StudentFlowController::class, 'dashboard'])->name('estudiante.dashboard');

Route::get('/estudiante/practica', [StudentFlowController::class, 'practice'])->name('estudiante.practica');

Route::get('/estudiante/documentos', [AcademicDocumentController::class, 'index'])->name('estudiante.documentos');
Route::post('/estudiante/documentos', [AcademicDocumentController::class, 'storeVersion'])->name('estudiante.documentos.store');
Route::get('/estudiante/documentos/{document}', [AcademicDocumentController::class, 'show'])->whereNumber('document')->name('estudiante.documento');

Route::middleware('closing.schema:monographs')->group(function () {
    Route::get('/estudiante/monografia', [MonographController::class, 'index'])->name('estudiante.monografia');
    Route::post('/estudiante/monografia', [MonographController::class, 'submit'])->name('estudiante.monografia.submit');
    Route::get('/estudiante/monografia/{monograph}', [MonographController::class, 'show'])->whereNumber('monograph')->name('estudiante.monografia.show');
});

Route::middleware('closing.schema:committees')->group(function () {
    Route::get('/estudiante/terna', [CommitteeController::class, 'index'])->name('estudiante.terna');
    Route::get('/estudiante/ternas/{committee}', [CommitteeController::class, 'show'])->whereNumber('committee')->name('estudiante.terna.show');
});

Route::middleware('closing.schema:defenses')->group(function () {
    Route::get('/estudiante/defensa', [DefenseController::class, 'index'])->name('estudiante.defensa');
    Route::get('/estudiante/defensas/{defense}', [DefenseController::class, 'show'])->whereNumber('defense')->name('estudiante.defensa.show');
});

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

Route::get('/docente/seguimientos', [FollowupController::class, 'index'])->name('docente.seguimientos');
Route::post('/docente/seguimientos', [FollowupController::class, 'store'])->name('docente.seguimientos.store');
Route::get('/docente/seguimientos/{followup}', [FollowupController::class, 'show'])->whereNumber('followup')->name('docente.seguimiento');
Route::put('/docente/seguimientos/{followup}', [FollowupController::class, 'update'])->whereNumber('followup')->name('docente.seguimiento.update');
Route::patch('/docente/seguimientos/{followup}/estado', [FollowupController::class, 'changeStatus'])->whereNumber('followup')->name('docente.seguimiento.status');
Route::delete('/docente/seguimientos/{followup}', [FollowupController::class, 'destroy'])->whereNumber('followup')->name('docente.seguimiento.destroy');


Route::middleware('closing.schema:monographs')->group(function () {
    Route::get('/docente/monografias', [MonographController::class, 'index'])->name('docente.monografias');
    Route::get('/docente/monografias/{monograph}', [MonographController::class, 'show'])->whereNumber('monograph')->name('docente.monografia.show');
    Route::post('/docente/monografias/{monograph}/revision', [MonographController::class, 'review'])->whereNumber('monograph')->name('docente.monografia.review');
});

Route::middleware('closing.schema:committees')->group(function () {
    Route::get('/docente/ternas', [CommitteeController::class, 'index'])->name('docente.ternas');
    Route::get('/docente/ternas/{committee}', [CommitteeController::class, 'show'])->whereNumber('committee')->name('docente.terna.show');
});

Route::middleware('closing.schema:defenses')->group(function () {
    Route::get('/docente/defensas', [DefenseController::class, 'index'])->name('docente.defensas');
    Route::get('/docente/defensas/{defense}', [DefenseController::class, 'show'])->whereNumber('defense')->name('docente.defensa.show');
});

Route::get('/docente/alertas', [TeacherPortalController::class, 'alerts'])->name('docente.alertas');

Route::get('/docente/carga-academica', function () {
    return view('docente.carga-academica');
})->name('docente.carga-academica');

});

Route::middleware(['auth', 'closing.schema:committees'])->group(function () {
    Route::get('/academico/ternas', [CommitteeController::class, 'index'])->name('academico.ternas');
    Route::get('/academico/ternas/{committee}', [CommitteeController::class, 'show'])->whereNumber('committee')->name('academico.terna.show');
});
Route::middleware(['auth', 'closing.schema:monographs'])->group(function () {
    Route::get('/academico/monografias/{monograph}', [MonographController::class, 'show'])->whereNumber('monograph')->name('academico.monografia.show');
    Route::post('/academico/monografias/{monograph}/revision', [MonographController::class, 'review'])->whereNumber('monograph')->name('academico.monografia.review');
});

Route::middleware(['auth', 'closing.schema:defenses'])->group(function () {
    Route::get('/academico/defensas', [DefenseController::class, 'index'])->name('academico.defensas');
    Route::get('/academico/defensas/{defense}', [DefenseController::class, 'show'])->whereNumber('defense')->name('academico.defensa.show');
    Route::post('/academico/defensas/{defense}/opciones/{option}/disponibilidad', [DefenseController::class, 'respond'])->whereNumber(['defense', 'option'])->scopeBindings()->name('academico.defensa.respond');
    Route::post('/academico/defensas/{defense}/clases', [DefenseController::class, 'addClassBlock'])->whereNumber('defense')->name('academico.defensa.classes');
});
