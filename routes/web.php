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