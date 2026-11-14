@extends('layouts.teacher')

@section('title', 'Seguimiento de practicante | UNICAH')

@section('content')


@php

    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN GENERAL DEL PRACTICANTE
    |--------------------------------------------------------------------------
    */

    $partesNombre = explode(' ', trim($practicante['nombre']));
    $iniciales = '';

    foreach ($partesNombre as $parte) {
        $iniciales .= strtoupper(substr($parte, 0, 1));
    }

    $iniciales = substr($iniciales, 0, 2);


    /*
    |--------------------------------------------------------------------------
    | BADGE SUPERIOR
    |--------------------------------------------------------------------------
    */

    $badgeTexto = match($practicante['etapa']) {
        'Documentación' => 'Documentación',
        'Empresa' => 'Empresa',
        'Práctica' => 'Práctica activa',
        'Visita' => 'Visita',
        'Monografía' => 'Monografía',
        'Defensa' => 'Defensa',
        'Finalización' => 'Finalización',
        default => 'Proceso activo',
    };


    /*
    |--------------------------------------------------------------------------
    | ETAPAS DEL PROCESO
    |--------------------------------------------------------------------------
    */

    $etapas = [

        'Solicitud' => [
            'nombre' => 'Solicitud',
            'icono' => 'bi-file-earmark-text',
        ],

        'Documentación' => [
            'nombre' => 'Documentos',
            'icono' => 'bi-files',
        ],

        'Empresa' => [
            'nombre' => 'Empresa',
            'icono' => 'bi-buildings',
        ],

        'Práctica' => [
            'nombre' => 'Práctica',
            'icono' => 'bi-briefcase',
        ],

        'Visita' => [
            'nombre' => 'Visita',
            'icono' => 'bi-calendar-check',
        ],

        'Monografía' => [
            'nombre' => 'Monografía',
            'icono' => 'bi-journal-text',
        ],

        'Defensa' => [
            'nombre' => 'Defensa',
            'icono' => 'bi-mortarboard',
        ],

        'Finalización' => [
            'nombre' => 'Finalización',
            'icono' => 'bi-flag',
        ],

    ];


    $clavesEtapas = array_keys($etapas);

    $indiceActual = array_search(
        $practicante['etapa'],
        $clavesEtapas,
        true
    );

    if ($indiceActual === false) {
        $indiceActual = 0;
    }


    /*
    |--------------------------------------------------------------------------
    | ESTADOS ADICIONALES
    |--------------------------------------------------------------------------
    */

    $estadoVisita =
        $practicante['visita_estado']
        ?? 'No habilitada';

    $estadoMonografia =
        $practicante['monografia_estado']
        ?? 'Pendiente';

    $estadoDefensa =
        $practicante['defensa_estado']
        ?? 'Pendiente';

@endphp



<!-- CABECERA -->

<div class="page-header">

    <div>

        <a
            href="{{ route('docente.dashboard') }}"
            class="teacher-back-link"
        >
            <i class="bi bi-arrow-left"></i>
            Volver a mis practicantes
        </a>

        <h1>
            Seguimiento de practicante
        </h1>

        <p>
            Consulta el estado académico y las etapas
            del proceso de práctica profesional.
        </p>

    </div>


    <span class="badge success">

        <i class="bi bi-check-circle"></i>

        {{ $badgeTexto }}

    </span>

</div>



<!-- ESTUDIANTE -->

<section class="teacher-tracking-profile">

    <div class="teacher-tracking-student">

        <div class="teacher-tracking-avatar">

            {{ $iniciales }}

        </div>


        <div>

            <span class="section-label">
                PRACTICANTE ASIGNADO
            </span>

            <h2>
                {{ $practicante['nombre'] }}
            </h2>

            <p>
                {{ $practicante['carrera'] }}
            </p>

            <small>
                Cuenta: {{ $practicante['cuenta'] }}
            </small>

        </div>

    </div>


    <div class="teacher-tracking-progress">

        <span>
            Progreso general
        </span>

        <strong>
            {{ $practicante['progreso'] }}%
        </strong>

        <div>
            <span
                style="width: {{ $practicante['progreso'] }}%;"
            ></span>
        </div>

    </div>

</section>



<!-- INFORMACIÓN GENERAL -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-track-stat">

            <span>
                Empresa
            </span>

            <strong>
                {{ $practicante['empresa'] }}
            </strong>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-track-stat">

            <span>
                Etapa actual
            </span>

            <strong>
                {{ $practicante['etapa'] }}
            </strong>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-track-stat">

            <span>
                Inicio
            </span>

            <strong>
                {{ $practicante['inicio'] }}
            </strong>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-track-stat">

            <span>
                Finalización
            </span>

            <strong>
                {{ $practicante['finalizacion'] }}
            </strong>

        </div>

    </div>

</section>



<!-- PROCESO -->

<section class="content-card teacher-process-card">

    <div class="card-header">

        <div>

            <h2>
                Proceso de práctica
            </h2>

            <p>
                Estado actual de las etapas institucionales.
            </p>

        </div>


        <div class="teacher-current-day">

            Día

            <strong>
                {{ $practicante['dia'] ?? '—' }}
            </strong>

        </div>

    </div>



    @php
        $processSteps = [];
    @endphp
        @foreach($etapas as $clave => $etapa)

            @php

                $indice = array_search(
                    $clave,
                    $clavesEtapas,
                    true
                );


                $completada =
                    $indice < $indiceActual;


                $actual =
                    $indice === $indiceActual;


                $estadoTexto =
                    'Pendiente';


                /*
                |--------------------------------------------------------------------------
                | SOLICITUD
                |--------------------------------------------------------------------------
                */

                if ($clave === 'Solicitud') {

                    if ($completada) {

                        $estadoTexto =
                            'Aprobada';

                    }

                    elseif ($actual) {

                        $estadoTexto =
                            'Actual';

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | DOCUMENTACIÓN
                |--------------------------------------------------------------------------
                */

                elseif ($clave === 'Documentación') {

                    if ($completada) {

                        $estadoTexto =
                            'Validados';

                    }

                    elseif ($actual) {

                        $estadoTexto =
                            'Actual';

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | EMPRESA
                |--------------------------------------------------------------------------
                */

                elseif ($clave === 'Empresa') {

                    if ($completada) {

                        $estadoTexto =
                            'Validada';

                    }

                    elseif ($actual) {

                        $estadoTexto =
                            'Actual';

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | PRÁCTICA
                |--------------------------------------------------------------------------
                */

                elseif ($clave === 'Práctica') {

                    if ($completada) {

                        $estadoTexto =
                            'Completada';

                    }

                    elseif ($actual) {

                        $estadoTexto =
                            'En curso';

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | VISITA
                |--------------------------------------------------------------------------
                */

                elseif ($clave === 'Visita') {

                    if ($estadoVisita === 'Realizada') {

                        $estadoTexto =
                            'Realizada';

                        $completada =
                            true;

                        $actual =
                            false;

                    }

                    elseif ($estadoVisita === 'Programada') {

                        $estadoTexto =
                            'Programada';

                    }

                    elseif ($estadoVisita === 'Habilitada') {

                        $estadoTexto =
                            'Habilitada';

                    }

                    elseif ($actual) {

                        $estadoTexto =
                            'Actual';

                    }

                    elseif (
                        ($practicante['dia'] ?? 0) < 45
                    ) {

                        $estadoTexto =
                            'Próxima';

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | MONOGRAFÍA
                |--------------------------------------------------------------------------
                */

                elseif ($clave === 'Monografía') {
                    $estadoTexto = $estadoMonografia;
                }




                /*
                |--------------------------------------------------------------------------
                | DEFENSA
                |--------------------------------------------------------------------------
                */

                elseif ($clave === 'Defensa') {
                    $estadoTexto = $estadoDefensa;
                }




                /*
                |--------------------------------------------------------------------------
                | FINALIZACIÓN
                |--------------------------------------------------------------------------
                */

                elseif ($clave === 'Finalización') {

                    if (
                        $practicante['progreso'] >= 100
                    ) {

                        $estadoTexto =
                            'Completada';

                        $completada =
                            true;

                        $actual =
                            false;

                    }

                    elseif ($actual) {

                        $estadoTexto =
                            'Actual';

                    }

                }

                if ($clave === 'Visita') {
                    $estadoTexto = $estadoVisita;
                    $completada = $estadoVisita === 'Realizada';
                    $actual = in_array($estadoVisita, ['Habilitada', 'Programada'], true);
                } elseif ($clave === 'Monografía') {
                    $completada = $estadoMonografia === 'Aprobada';
                    $actual = in_array($estadoMonografia, ['En revisión', 'Requiere corrección'], true);
                }
                $processSteps[] = [
                    'name' => $etapa['nombre'],
                    'state' => $estadoTexto,
                    'icon' => $completada ? 'bi-check-lg' : $etapa['icono'],
                    'class' => trim(($completada ? 'completed' : '').' '.($actual ? 'active' : '')),
                ];
            @endphp
        @endforeach
    <x-practice-process :steps="$processSteps" />
</section>



<!-- SEGUIMIENTO Y ALERTAS -->

<section class="row g-4 mt-1">


    <!-- SEGUIMIENTO ACADÉMICO -->

    <div class="col-12 col-lg-7">

        <div class="content-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Seguimiento académico
                    </h2>

                    <p>
                        Actividades relacionadas con el acompañamiento
                        del practicante.
                    </p>

                </div>

            </div>


            <div class="teacher-tracking-list">


                <!-- ASIGNACIÓN -->

                <div class="teacher-tracking-item completed">

                    <div class="tracking-status-icon">

                        <i class="bi bi-check-lg"></i>

                    </div>


                    <div>

                        <strong>
                            Asignación del practicante
                        </strong>

                        <span>
                            Practicante asignado correctamente
                            al docente.
                        </span>

                    </div>


                    <small>
                        Completado
                    </small>

                </div>



                <!-- PRÁCTICA -->

                @if(
                    in_array(
                        $practicante['etapa'],
                        [
                            'Práctica',
                            'Visita',
                            'Monografía',
                            'Defensa',
                            'Finalización'
                        ]
                    )
                )

                    <div
                        class="teacher-tracking-item
                        {{ $practicante['etapa'] === 'Práctica' ? 'active' : 'completed' }}"
                    >

                        <div class="tracking-status-icon">

                            @if($practicante['etapa'] === 'Práctica')

                                <i class="bi bi-clock"></i>

                            @else

                                <i class="bi bi-check-lg"></i>

                            @endif

                        </div>


                        <div>

                            <strong>
                                Seguimiento de práctica
                            </strong>

                            <span>

                                @if($practicante['etapa'] === 'Práctica')

                                    La práctica se encuentra
                                    actualmente en desarrollo.

                                @else

                                    La etapa principal de práctica
                                    ya fue completada.

                                @endif

                            </span>

                        </div>


                        <small>

                            @if($practicante['etapa'] === 'Práctica')

                                En curso

                            @else

                                Completado

                            @endif

                        </small>

                    </div>

                @endif



                <!-- VISITA -->

                <div
                    class="teacher-tracking-item
                    {{ $estadoVisita === 'Realizada' ? 'completed' : '' }}
                    {{ $estadoVisita === 'Habilitada' || $estadoVisita === 'Programada' ? 'active' : '' }}"
                >

                    <div class="tracking-status-icon">

                        @if($estadoVisita === 'Realizada')

                            <i class="bi bi-check-lg"></i>

                        @elseif(
                            $estadoVisita === 'Habilitada' ||
                            $estadoVisita === 'Programada'
                        )

                            <i class="bi bi-calendar-check"></i>

                        @else

                            <i class="bi bi-clock"></i>

                        @endif

                    </div>


                    <div>

                        <strong>
                            Visita de seguimiento
                        </strong>


                        <span>

                            @if($estadoVisita === 'Realizada')

                                La visita de seguimiento
                                ya fue realizada.

                            @elseif($estadoVisita === 'Programada')

                                La visita cuenta con una fecha
                                programada.

                            @elseif($estadoVisita === 'Habilitada')

                                El practicante ya cumple la condición
                                para realizar la visita.

                            @else

                                Disponible a partir del día 45.

                            @endif

                        </span>

                    </div>


                    <small>

                        @if($estadoVisita === 'Realizada')

                            Realizada

                        @elseif($estadoVisita === 'Programada')

                            Programada

                        @elseif($estadoVisita === 'Habilitada')

                            Habilitada

                        @else

                            Próxima

                        @endif

                    </small>

                </div>



                <!-- MONOGRAFÍA -->

                @if($monografia)

                    <div
                        class="teacher-tracking-item
                        {{ $estadoMonografia === 'Aprobada' ? 'completed' : 'active' }}"
                    >

                        <div class="tracking-status-icon">

                            @if($estadoMonografia !== 'Aprobada')

                                <i class="bi bi-journal-text"></i>

                            @else

                                <i class="bi bi-check-lg"></i>

                            @endif

                        </div>


                        <div>

                            <strong>
                                Monografía
                            </strong>

                            <span>

                                Estado de monografía: {{ $estadoMonografia }}

                            </span>

                            <a href="{{ route('docente.monografia.show', $monografia) }}" class="coord-teacher-view">Consultar monografía y revisión técnica</a>

                        </div>


                        <small>

                            {{ $estadoMonografia }}

                        </small>

                    </div>

                @endif


            </div>

        </div>

    </div>



    <!-- ALERTAS -->

    <div class="col-12 col-lg-5">

        <div class="content-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Alertas
                    </h2>

                    <p>
                        Situaciones que requieren atención.
                    </p>

                </div>

            </div>



            @if($estadoVisita === 'Habilitada')

                <div class="teacher-tracking-alert">

                    <div>

                        <i class="bi bi-calendar-event"></i>

                    </div>


                    <div>

                        <strong>
                            Visita habilitada
                        </strong>

                        <p>
                            El practicante ya superó el día 45
                            y la visita de seguimiento puede continuar.
                        </p>

                    </div>

                </div>


            @elseif($estadoVisita === 'Programada')

                <div class="teacher-tracking-alert">

                    <div>

                        <i class="bi bi-calendar-check"></i>

                    </div>


                    <div>

                        <strong>
                            Visita programada
                        </strong>

                        <p>
                            La visita de seguimiento ya cuenta
                            con una programación registrada.
                        </p>

                    </div>

                </div>


            @elseif(
                $estadoVisita !== 'Realizada' &&
                ($practicante['dia'] ?? 0) > 0 &&
                ($practicante['dia'] ?? 0) < 45
            )

                <div class="teacher-tracking-alert">

                    <div>

                        <i class="bi bi-calendar-event"></i>

                    </div>


                    <div>

                        <strong>
                            Visita próxima
                        </strong>

                        <p>
                            El practicante se encuentra en el día
                            {{ $practicante['dia'] }}
                            y la visita se habilita a partir del día 45.
                        </p>

                    </div>

                </div>


            @elseif(
                $practicante['etapa'] === 'Monografía'
            )

                <div class="teacher-tracking-alert">

                    <div>

                        <i class="bi bi-journal-text"></i>

                    </div>


                    <div>

                        <strong>
                            Monografía en seguimiento
                        </strong>

                        <p>
                            La monografía se encuentra actualmente
                            en estado: {{ $estadoMonografia }}.
                        </p>

                    </div>

                </div>


            @else

                <div class="teacher-tracking-info">

                    <i class="bi bi-check-circle"></i>

                    <p>
                        No hay alertas adicionales registradas
                        para este practicante.
                    </p>

                </div>

            @endif



            <div class="teacher-tracking-info">

                <i class="bi bi-info-circle"></i>

                <p>
                    Las notas, agenda, pendientes y bitácora
                    personal del estudiante no son visibles
                    para el docente.
                </p>

            </div>

        </div>

    </div>

</section>


@endsection
