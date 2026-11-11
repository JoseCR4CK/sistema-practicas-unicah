@extends('layouts.student')

@section('title', 'Mi Práctica Profesional | UNICAH')

@section('content')


<!-- =====================================
     BIENVENIDA
====================================== -->

<div class="page-header student-home-header">

    <div>

        <span class="student-home-eyebrow">
            BUEN DÍA,
        </span>

        <h1>
            {{ auth()->user()->name }}
        </h1>

        <p>
            Organiza tu día y consulta el avance de tu práctica profesional.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-calendar3"></i>

        III Período 2026

    </div>

</div>



<!-- =====================================
     RESUMEN DEL DÍA
====================================== -->

<section class="student-today-card">

    <div class="student-today-main">

        <div class="student-today-icon">

            <i class="bi bi-sun"></i>

        </div>


        <div>

            <span class="section-label">
                HOY
            </span>

            <h2>
                Tu práctica está al día
            </h2>

            <p>
                Tienes 3 pendientes personales y una actividad
                importante próxima.
            </p>

        </div>

    </div>


    <div class="student-today-stats">

        <div>

            <strong>
                3
            </strong>

            <span>
                Pendientes
            </span>

        </div>


        <div>

            <strong>
                6
            </strong>

            <span>
                Notas
            </span>

        </div>


        <div>

            <strong>
                12
            </strong>

            <span>
                Registros
            </span>

        </div>

    </div>

</section>



<!-- =====================================
     ACCIONES RÁPIDAS
====================================== -->

<section class="student-section">

    <div class="student-section-heading">

        <div>

            <h2>
                Acciones rápidas
            </h2>

            <p>
                Herramientas para organizar tu práctica.
            </p>

        </div>

    </div>


    <div class="row g-3">


        <div class="col-12 col-md-4">

            <a
                href="{{ route('estudiante.notas') }}"
                class="student-quick-action"
            >

                <div class="quick-action-icon blue">

                    <i class="bi bi-plus-lg"></i>

                </div>


                <div>

                    <strong>
                        Nueva nota
                    </strong>

                    <span>
                        Guarda una idea o recordatorio.
                    </span>

                </div>

            </a>

        </div>



        <div class="col-12 col-md-4">

            <a
                href="{{ route('estudiante.modulo', 'bitacora') }}"
                class="student-quick-action"
            >

                <div class="quick-action-icon gold">

                    <i class="bi bi-journal-plus"></i>

                </div>


                <div>

                    <strong>
                        Registrar actividad
                    </strong>

                    <span>
                        Añade una entrada a tu bitácora.
                    </span>

                </div>

            </a>

        </div>



        <div class="col-12 col-md-4">

            <a
                href="{{ route('estudiante.pendientes') }}"
                class="student-quick-action"
            >

                <div class="quick-action-icon green">

                    <i class="bi bi-check2-square"></i>

                </div>


                <div>

                    <strong>
                        Agregar pendiente
                    </strong>

                    <span>
                        Organiza una tarea personal.
                    </span>

                </div>

            </a>

        </div>


    </div>

</section>



<!-- =====================================
     MI ESPACIO + PRÓXIMA ACTIVIDAD
====================================== -->

<section class="row g-4 mb-4">


    <!-- MI ESPACIO -->

    <div class="col-12 col-xl-7">

        <div class="content-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Mi espacio
                    </h2>

                    <p>
                        Tus herramientas personales de organización.
                    </p>

                </div>

            </div>


            <div class="student-space-grid">


                <a
                    href="{{ route('estudiante.agenda') }}"
                    class="student-space-item"
                >

                    <div class="space-icon blue">
                        <i class="bi bi-calendar3"></i>
                    </div>

                    <div>

                        <strong>
                            Agenda
                        </strong>

                        <span>
                            2 actividades próximas
                        </span>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>



                <a
                    href="{{ route('estudiante.modulo', 'notas') }}"
                    class="student-space-item"
                >

                    <div class="space-icon gold">
                        <i class="bi bi-sticky"></i>
                    </div>

                    <div>

                        <strong>
                            Mis notas
                        </strong>

                        <span>
                            6 notas privadas
                        </span>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>



                <a
                    href="{{ route('estudiante.modulo', 'pendientes') }}"
                    class="student-space-item"
                >

                    <div class="space-icon green">
                        <i class="bi bi-check2-square"></i>
                    </div>

                    <div>

                        <strong>
                            Mis pendientes
                        </strong>

                        <span>
                            3 tareas abiertas
                        </span>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>



                <a
                    href="{{ route('estudiante.bitacora') }}"
                    class="student-space-item"
                >

                    <div class="space-icon purple">
                        <i class="bi bi-journal-text"></i>
                    </div>

                    <div>

                        <strong>
                            Bitácora
                        </strong>

                        <span>
                            12 actividades registradas
                        </span>

                    </div>

                    <i class="bi bi-chevron-right"></i>

                </a>


            </div>

        </div>

    </div>



    <!-- PRÓXIMA ACTIVIDAD -->

    <div class="col-12 col-xl-5">

        <div class="content-card student-next-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Próxima actividad
                    </h2>

                    <p>
                        Lo siguiente en tu proceso.
                    </p>

                </div>

            </div>


            <div class="student-next-content">

                <div class="student-next-date">

                    <strong>
                        45
                    </strong>

                    <span>
                        DÍA
                    </span>

                </div>


                <div class="student-next-info">

                    <span class="section-label">
                        VISITA DE SEGUIMIENTO
                    </span>

                    <h3>
                        {{ $visitStatus }}
                    </h3>

                    <p>
                        La visita podrá programarse a partir
                        del día 45 de tu práctica.
                    </p>


                    <a
                        href="{{ route('estudiante.visita') }}"
                        class="student-inline-link"
                    >
                        Ver información

                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

            </div>

        </div>

    </div>


</section>



<!-- =====================================
     PROGRESO DE PRÁCTICA
====================================== -->

<section class="content-card student-practice-summary">

    <div class="student-practice-summary-header">

        <div>

            <span class="section-label">
                MI PRÁCTICA PROFESIONAL
            </span>

            <h2>
                Seguimiento de práctica
            </h2>

            <p>
                Grupo Tecnológico S.A.
            </p>

        </div>


        <div class="student-progress-number">

            <strong>
                {{ $process?->progress ?? 0 }}%
            </strong>

            <span>
                completado
            </span>

        </div>

    </div>


    <div class="student-main-progress">

        <div style="width: {{ $process?->progress ?? 0 }}%;"></div>

    </div>


    <div class="student-process-timeline">


        <div class="student-process-step {{ $applicationStatus === 'Aprobada' ? 'completed' : '' }}">

            <div class="student-process-circle">
                <i class="bi bi-check-lg"></i>
            </div>

            <span><a href="{{ route('estudiante.solicitudes') }}">Solicitud</a></span><small>{{ $applicationStatus }}</small>

        </div>


        <div class="student-process-step">

            <div class="student-process-circle">
                <i class="bi bi-check-lg"></i>
            </div>

            <span><a href="{{ route('estudiante.documentos') }}">Documentos</a></span><small>{{ $documentSummary }}</small>

        </div>


        <div class="student-process-step completed">

            <div class="student-process-circle">
                <i class="bi bi-check-lg"></i>
            </div>

            <span>
                Empresa
            </span>

        </div>


        <div class="student-process-step active">

            <div class="student-process-circle">

                <i class="bi bi-briefcase"></i>

            </div>

            <span>
                Práctica
            </span>

        </div>


        <div class="student-process-step">

            <div class="student-process-circle">

                <i class="bi bi-calendar-check"></i>

            </div>

            <span>Visita</span><small>{{ $visitStatus }}</small>

        </div>


        <div class="student-process-step">

            <div class="student-process-circle">

                <i class="bi bi-journal-text"></i>

            </div>

            <span><a href="{{ route('estudiante.monografia') }}">Monografía</a></span><small>{{ $closing['monograph_state'] }}</small>

        </div>


        <div class="student-process-step">

            <div class="student-process-circle">

                <i class="bi bi-mortarboard"></i>

            </div>

            <span><a href="{{ route('estudiante.defensa') }}">Defensa</a></span><small>{{ $closing['defense_state'] }}</small>

        </div>


        <div class="student-process-step">

            <div class="student-process-circle">

                <i class="bi bi-flag"></i>

            </div>

            <span>
                Finalización
            </span>

        </div>


    </div>

</section>



<!-- =====================================
     INFORMACIÓN ÚTIL
====================================== -->

<section class="row g-3 mt-1">


    <div class="col-12 col-md-6">

        <div class="student-helper-card">

            <div class="student-helper-icon">

                <i class="bi bi-person-video3"></i>

            </div>


            <div>

                <span>
                    Docente asignado
                </span>

                <strong>
                    Pendiente de asignación
                </strong>

                <a href="{{ route('estudiante.docente') }}">
                    Ver información
                </a>

            </div>

        </div>

    </div>



    <div class="col-12 col-md-6">

        <div class="student-helper-card">

            <div class="student-helper-icon">

                <i class="bi bi-buildings"></i>

            </div>


            <div>

                <span>
                    Empresa
                </span>

                <strong>
                    Grupo Tecnológico S.A.
                </strong>

                <a href="{{ route('estudiante.empresa') }}">
                    Ver empresa
                </a>

            </div>

        </div>

    </div>


</section>


@endsection