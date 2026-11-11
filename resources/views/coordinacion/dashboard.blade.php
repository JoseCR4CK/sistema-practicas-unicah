@extends('layouts.app')


@section('title', 'Dashboard | Coordinación Académica')


@section('content')


<!-- =====================================
     CABECERA
====================================== -->

<div class="page-header">

    <div>

        <h1>
            Dashboard
        </h1>

        <p>
            Resumen general de los procesos
            de práctica profesional.
        </p>

    </div>


    <div class="page-date">
        <i class="bi bi-calendar3"></i>
        {{ now()->locale('es')->translatedFormat('d \d\e F \d\e Y') }}
    </div>

</div>



<!-- =====================================
     INDICADORES
====================================== -->

<section class="row g-3 mb-4">

    <!-- PRÁCTICAS -->
    <div class="col-12 col-md-6 col-xl-4">

        <div class="stat-card h-100">

            <div class="stat-info">

                <span>
                    Prácticas activas
                </span>

                <strong>
                    {{ $practicasActivas }}
                </strong>

                <small>
                    Prácticas actualmente en proceso
                </small>

            </div>

            <div class="stat-icon blue">
                <i class="bi bi-briefcase"></i>
            </div>

        </div>

    </div>


    <!-- SOLICITUDES -->
    <div class="col-12 col-md-6 col-xl-4">

        <div class="stat-card h-100">

            <div class="stat-info">

                <span>
                    Solicitudes pendientes
                </span>

                <strong>
                    {{ $solicitudesPendientes->count() }}
                </strong>

                <small>
                    Requieren revisión
                </small>

            </div>

            <div class="stat-icon gold">
                <i class="bi bi-file-earmark-text"></i>
            </div>

        </div>

    </div>


    <!-- DOCUMENTOS -->
    <div class="col-12 col-md-6 col-xl-4">

        <div class="stat-card h-100">

            <div class="stat-info">

                <span>
                    Documentos pendientes
                </span>

                <strong>
                    {{ $attentionDocuments }}
                </strong>

                <small>
                    Esperando validación
                </small>

            </div>

            <div class="stat-icon red">
                <i class="bi bi-folder2-open"></i>
            </div>

        </div>

    </div>


    <!-- VISITAS -->
    <div class="col-12 col-md-6 col-xl-4">

        <div class="stat-card h-100">

            <div class="stat-info">

                <span>
                    Visitas próximas
                </span>

                <strong>
                    {{ $visitasProgramadas }}
                </strong>

                <small>
                    Programadas próximamente
                </small>

            </div>

            <div class="stat-icon green">
                <i class="bi bi-calendar-check"></i>
            </div>

        </div>

    </div>


    <!-- MONOGRAFÍAS -->
    <div class="col-12 col-md-6 col-xl-4">

        <div class="stat-card h-100">

            <div class="stat-info">

                <span>
                    Monografías en revisión
                </span>

                <strong>
                    {{ $monographsInReview ?? '—' }}
                </strong>

                <small>
                    En alguna etapa de revisión
                </small>

            </div>

            <div class="stat-icon info">
                <i class="bi bi-journal-text"></i>
            </div>

        </div>

    </div>


    <!-- DEFENSAS -->
    <div class="col-12 col-md-6 col-xl-4">

        <div class="stat-card h-100">

            <div class="stat-info">

                <span>
                    Defensas pendientes
                </span>

                <strong>
                    {{ $defensesPending ?? '—' }}
                </strong>

                <small>
                    Pendientes de calendarización
                </small>

            </div>

            <div class="stat-icon purple">
                <i class="bi bi-mortarboard"></i>
            </div>

        </div>

    </div>

</section>

<!-- =====================================
     CENTRO DE ALERTAS
====================================== -->

<section class="coord-alert-center">

    <div class="coord-alert-heading">

        <div>

            <span class="section-label">
                CENTRO DE ALERTAS
            </span>

            <h2>
                Situaciones que requieren atención
            </h2>

            <p>
                Las alertas se clasifican según su nivel
                de prioridad dentro del proceso.
            </p>

        </div>


        <div class="coord-alert-legend">

            <span>
                <i class="coord-alert-dot urgent"></i>
                Urgente
            </span>

            <span>
                <i class="coord-alert-dot warning"></i>
                Atención
            </span>

            <span>
                <i class="coord-alert-dot info"></i>
                Informativo
            </span>

            <span>
                <i class="coord-alert-dot success"></i>
                Correcto
            </span>

        </div>

    </div>


    <div class="row g-3">


        <!-- URGENTE -->

        <div class="col-12 col-xl-6">

            <a
                href="{{ route('coordinacion.documentos') }}"
                class="coord-alert-card urgent"
            >

                <div class="coord-alert-icon">

                    <i class="bi bi-exclamation-triangle-fill"></i>

                </div>


                <div class="coord-alert-content">

                    <div class="coord-alert-title">

                        <span>
                            URGENTE
                        </span>

                        <small>
                            Documentos
                        </small>

                    </div>


                    <strong>
                        {{ $attentionDocuments }} documentos requieren atención
                    </strong>


                    <p>
                        Existen documentos pendientes cuyo período
                        de revisión requiere atención de Coordinación.
                    </p>

                </div>


                <div class="coord-alert-action">

                    <span>
                        Revisar
                    </span>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </a>

        </div>



        <!-- ATENCIÓN -->

        <div class="col-12 col-xl-6">

            <a
                href="{{ route('coordinacion.visitas') }}"
                class="coord-alert-card warning"
            >

                <div class="coord-alert-icon">

                    <i class="bi bi-calendar-event"></i>

                </div>


                <div class="coord-alert-content">

                    <div class="coord-alert-title">

                        <span>
                            REQUIERE ATENCIÓN
                        </span>

                        <small>
                            Visitas
                        </small>

                    </div>


                    <strong>
                        {{ $visitasHabilitadas }} visitas habilitadas
                    </strong>


                    <p>
                        Los estudiantes están alcanzando el período
                        habilitado para el seguimiento de práctica.
                    </p>

                </div>


                <div class="coord-alert-action">

                    <span>
                        Revisar
                    </span>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </a>

        </div>



        <!-- INFORMATIVO -->

        <div class="col-12 col-xl-6">

            <a
                href="{{ route('coordinacion.monografias') }}"
                class="coord-alert-card info"
            >

                <div class="coord-alert-icon">

                    <i class="bi bi-journal-text"></i>

                </div>


                <div class="coord-alert-content">

                    <div class="coord-alert-title">

                        <span>
                            SEGUIMIENTO
                        </span>

                        <small>
                            Monografías
                        </small>

                    </div>


                    <strong>
                        {{ $monographsInReview ?? '—' }} monografías se encuentran en revisión
                    </strong>


                    <p>
                        Hay procesos activos en las diferentes
                        etapas de revisión de monografía.
                    </p>

                </div>


                <div class="coord-alert-action">

                    <span>
                        Consultar
                    </span>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </a>

        </div>



        <!-- PROGRAMACIÓN -->

        <div class="col-12 col-xl-6">

            <a
                href="{{ route('coordinacion.defensas') }}"
                class="coord-alert-card neutral"
            >

                <div class="coord-alert-icon">

                    <i class="bi bi-mortarboard"></i>

                </div>


                <div class="coord-alert-content">

                    <div class="coord-alert-title">

                        <span>
                            PROGRAMACIÓN
                        </span>

                        <small>
                            Defensas
                        </small>

                    </div>


                    <strong>
                        {{ $defensesPending ?? '—' }} defensas están pendientes de calendarización
                    </strong>


                    <p>
                        Estos procesos requerirán verificar disponibilidad
                        académica y asignación correspondiente.
                    </p>

                </div>


                <div class="coord-alert-action">

                    <span>
                        Consultar
                    </span>

                    <i class="bi bi-chevron-right"></i>

                </div>

            </a>

        </div>


    </div>

</section>

<!-- =====================================
     PROCESOS QUE REQUIEREN ATENCIÓN
====================================== -->

<section class="content-card">


    <div class="card-header">

        <div>

            <h2>
                Procesos que requieren atención
            </h2>

            <p>
                Actividades pendientes dentro
                de los procesos de práctica.
            </p>

        </div>


        <a href="{{ route('coordinacion.estudiantes') }}">
            Ver todos
            <i class="bi bi-arrow-right"></i>
        </a>

    </div>



    <div class="table-container table-responsive">


        <table class="data-table table align-middle">


            <thead>

                <tr>

                    <th>
                        Estudiante
                    </th>

                    <th>
                        Etapa
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Fecha
                    </th>

                    <th>
                        Acción
                    </th>

                </tr>

            </thead>



            <tbody>
                @forelse ($solicitudesPendientes->take(5) as $solicitud)
                    <tr>
                        <td><span class="student-name">{{ $solicitud->student->user->name }}</span><span class="student-code">{{ $solicitud->student->account_number }}</span></td>
                        <td>Solicitud de práctica</td>
                        <td><span class="badge warning">{{ $solicitud->status }}</span></td>
                        <td>{{ $solicitud->submitted_at->format('d/m/Y') }}</td>
                        <td><a href="{{ route('coordinacion.solicitud', $solicitud) }}" class="action-button" title="Ver solicitud"><i class="bi bi-eye"></i></a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">Sin solicitudes pendientes.</td></tr>
                @endforelse
            </tbody>


        </table>


    </div>


</section>


@endsection
