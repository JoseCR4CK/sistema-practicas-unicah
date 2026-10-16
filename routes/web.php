<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::get('/coordinacion', function () {
    return view('coordinacion.dashboard');
})->name('coordinacion.dashboard');

Route::get('/coordinacion/estudiantes', function () {
    return view('coordinacion.estudiantes');
})->name('coordinacion.estudiantes');

Route::get('/coordinacion/estudiantes/{id}', function ($id) {
    return view('coordinacion.expediente', compact('id'));
})->name('coordinacion.estudiantes.expediente');

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

Route::get('/estudiante/modulo/{modulo}', function ($modulo) {

    $modulos = [

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