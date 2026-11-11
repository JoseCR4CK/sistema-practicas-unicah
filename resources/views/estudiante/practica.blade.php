@extends('layouts.student')


@section('title', 'Mi práctica | UNICAH')


@section('content')


<!-- =====================================
     CABECERA
====================================== -->

<div class="page-header">

    <div>

        <h1>
            Mi práctica
        </h1>

        <p>
            Consulta la información y el avance
            de tu proceso de práctica profesional.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-briefcase"></i>

        {{ $process?->statusLabel() ?? 'Sin práctica' }}

    </div>

</div>



<!-- =====================================
     RESUMEN PRINCIPAL
====================================== -->

<section class="practice-overview-card">

    <div class="practice-overview-main">

        <span class="section-label">
            PRÁCTICA PROFESIONAL
        </span>

        <h2>
            {{ $process?->company?->official_name ?? 'Sin empresa asignada' }}
        </h2>

        <p>
            {{ $student?->career ?? 'Sin carrera registrada' }}
        </p>


        <div class="practice-status-row">

            <span class="status-pill active">
                {{ $process?->stage ?? 'Sin práctica' }}
            </span>

            <span class="practice-code">
                {{ $process ? 'Proceso #'.$process->id : 'Sin proceso registrado' }}
            </span>

        </div>

    </div>


    <div class="practice-overview-progress">

        <span>
            Progreso general
        </span>

        <strong>
            {{ $process?->progress ?? 0 }}%
        </strong>

        <div class="mini-progress">

            <div
                class="mini-progress-fill"
                style="width: {{ $process?->progress ?? 0 }}%;"
            ></div>

        </div>

    </div>

</section>



<!-- =====================================
     INFORMACIÓN GENERAL
====================================== -->

<section class="practice-info-grid">


    <article class="practice-info-card">

        <div class="practice-info-icon blue">

            <i class="bi bi-calendar2-check"></i>

        </div>


        <div>

            <span>
                Fecha de inicio
            </span>

            <strong>
                {{ $process?->start_date?->format('d/m/Y') ?? 'Pendiente' }}
            </strong>

        </div>

    </article>



    <article class="practice-info-card">

        <div class="practice-info-icon red">

            <i class="bi bi-calendar2-event"></i>

        </div>


        <div>

            <span>
                Fecha de finalización
            </span>

            <strong>
                {{ $process?->end_date?->format('d/m/Y') ?? 'Pendiente' }}
            </strong>

        </div>

    </article>



    <article class="practice-info-card">

        <div class="practice-info-icon gold">

            <i class="bi bi-clock-history"></i>

        </div>


        <div>

            <span>
                Día actual
            </span>

            <strong>
                {{ $process?->currentDay() ?? 'No iniciada' }}
            </strong>

        </div>

    </article>



    <article class="practice-info-card">

        <div class="practice-info-icon green">

            <i class="bi bi-check2-circle"></i>

        </div>


        <div>

            <span>
                Estado
            </span>

            <strong>
                {{ $process?->statusLabel() ?? 'Sin práctica' }}
            </strong>

        </div>

    </article>


</section>



<!-- =====================================
     EMPRESA Y JEFE INMEDIATO
====================================== -->

<section class="student-secondary-grid">


    <article class="content-card">


        <div class="card-header">

            <div>

                <h2>
                    Empresa
                </h2>

                <p>
                    Lugar donde realizas tu práctica.
                </p>

            </div>

        </div>


        <div class="practice-detail-body">


            <div class="detail-icon blue">

                <i class="bi bi-buildings"></i>

            </div>


            <div class="detail-content">

                <h3>
                    {{ $process?->company?->official_name ?? 'Sin empresa asignada' }}
                </h3>

                <span>
                    <i class="bi bi-geo-alt"></i>
                    {{ collect([$process?->company?->city, $process?->company?->department])->filter()->implode(', ') ?: 'Sin ubicación registrada' }}
                </span>

                <span>
                    <i class="bi bi-envelope"></i>
                    {{ $process?->company?->email ?? 'Sin correo' }}
                </span>

                <span>
                    <i class="bi bi-telephone"></i>
                    {{ $process?->company?->phone ?? 'Sin teléfono' }}
                </span>

            </div>


        </div>


    </article>



    <article class="content-card">


        <div class="card-header">

            <div>

                <h2>
                    Jefe inmediato
                </h2>

                <p>
                    Responsable de tu supervisión en la empresa.
                </p>

            </div>

        </div>


        <div class="practice-detail-body">


            <div class="detail-icon gold">

                <i class="bi bi-person-vcard"></i>

            </div>


            <div class="detail-content">

                <h3>
                    {{ $process?->companyResponsible?->full_name ?? 'Sin responsable asignado' }}
                </h3>

                <span>
                    <i class="bi bi-briefcase"></i>
                    {{ $process?->companyResponsible?->position ?? 'Sin cargo registrado' }}
                </span>

                <span>
                    <i class="bi bi-envelope"></i>
                    {{ $process?->companyResponsible?->email ?? 'Sin correo' }}
                </span>

                <span>
                    <i class="bi bi-telephone"></i>
                    {{ $process?->companyResponsible?->phone ?? 'Sin teléfono' }}
                </span>

            </div>


        </div>


    </article>


</section>



<!-- =====================================
     ETAPAS DEL PROCESO
====================================== -->

<section class="content-card practice-stages-card">


    <div class="card-header">

        <div>

            <h2>
                Etapas del proceso
            </h2>

            <p>
                Estado actual de cada fase de tu práctica.
            </p>

        </div>

    </div>



    <div class="practice-stage-list">


        <!-- SOLICITUD -->

        <div class="practice-stage">

            <div class="practice-stage-icon">

                <i class="bi bi-check-lg"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Solicitud de práctica
                    </strong>

                    <span>
                        {{ $applicationStatus }}
                    </span>

                </div>


                <span class="badge info">
                    {{ $applicationStatus }}
                </span>

            </div>

        </div>



        <!-- DOCUMENTOS -->

        <div class="practice-stage">

            <div class="practice-stage-icon">

                <i class="bi bi-check-lg"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Documentación inicial
                    </strong>

                    <span>
                        {{ $documentSummary }}
                    </span>

                </div>


                <span class="badge info">
                    {{ $documentSummary }}
                </span>

            </div>

        </div>



        <!-- EMPRESA -->

        <div class="practice-stage {{ $process?->company ? 'completed' : 'pending' }}">

            <div class="practice-stage-icon">

                <i class="bi bi-check-lg"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Empresa de práctica
                    </strong>

                    <span>
                        {{ $process?->company?->official_name ?? 'Sin empresa asignada' }} · {{ $process?->companyResponsible?->full_name ?? 'Sin responsable asignado' }}
                    </span>

                </div>


                <span class="badge success">
                    {{ $process?->company ? 'Asignada' : 'Sin asignar' }}
                </span>

            </div>

        </div>



        <!-- PRACTICA -->

        <div class="practice-stage {{ $process?->status === 'ACTIVE' ? 'active' : ($process?->status === 'COMPLETED' ? 'completed' : 'pending') }}">

            <div class="practice-stage-icon">

                <i class="bi bi-briefcase"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Desarrollo de práctica
                    </strong>

                    <span>
                        Día de práctica: {{ $process?->currentDay() ?? 'No iniciada' }}
                    </span>

                </div>


                <span class="badge warning">
                    {{ $process?->statusLabel() ?? 'Sin práctica registrada' }}
                </span>

            </div>

        </div>



        <!-- VISITA -->

        <div class="practice-stage pending">

            <div class="practice-stage-icon">

                <i class="bi bi-calendar-check"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Visita de seguimiento
                    </strong>

                    <span>
                        Disponible a partir del día 45
                    </span>

                </div>


                <span class="badge info">
                    {{ $visitStatus }}
                </span>

            </div>

        </div>



        <!-- MONOGRAFIA -->

        <div class="practice-stage pending">

            <div class="practice-stage-icon">

                <i class="bi bi-journal-text"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Monografía
                    </strong>

                    <span>
                        Entrega requerida una semana antes de finalizar
                    </span>

                </div>


                <span class="badge info">
                    {{ $closing['monograph_state'] }}
                </span>

            </div>

        </div>



        <!-- DEFENSA -->

        <div class="practice-stage pending">

            <div class="practice-stage-icon">

                <i class="bi bi-mortarboard"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Defensa
                    </strong>

                    <span>
                        Consulta el estado de la programación de tu defensa
                    </span>

                </div>


                <span class="badge info">
                    {{ $closing['defense_state'] }}
                </span>

            </div>

        </div>



        <!-- FINALIZACION -->

        <div class="practice-stage pending">

            <div class="practice-stage-icon">

                <i class="bi bi-flag"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Finalización
                    </strong>

                    <span>
                        Cierre oficial del proceso
                    </span>

                </div>


                <span class="badge info">
                    Pendiente
                </span>

            </div>

        </div>


    </div>


</section>



<!-- =====================================
     PRÓXIMAS FECHAS
====================================== -->

<section class="content-card upcoming-events-card">


    <div class="card-header">

        <div>

            <h2>
                Próximas fechas importantes
            </h2>

            <p>
                Actividades previstas dentro de tu proceso.
            </p>

        </div>

    </div>



    <div class="upcoming-events-list">


        <div class="upcoming-event">

            <div class="event-date">

                <strong>
                    16
                </strong>

                <span>
                    OCT
                </span>

            </div>


            <div class="event-content">

                <strong>
                    Habilitación para visita
                </strong>

                <p>
                    A partir de esta fecha podrá realizarse
                    la visita de seguimiento.
                </p>

            </div>


            <span class="event-status soon">
                Próximamente
            </span>

        </div>



        <div class="upcoming-event">
            <div class="event-date">
                <strong>{{ $closing['deadline']?->format('d') ?? '—' }}</strong>
                <span>{{ $closing['deadline']?->format('m/Y') ?? 'Pendiente' }}</span>
            </div>
            <div class="event-content">
                <strong>Entrega de monografía</strong>
                <p>{{ $closing['deadline']?->format('d/m/Y') ?? 'Fecha final de práctica pendiente' }}</p>
            </div>
            <span class="event-status">{{ $closing['monograph_state'] }}</span>
        </div>

        <div class="upcoming-event">

            <div class="event-date">

                <strong>
                    30
                </strong>

                <span>
                    NOV
                </span>

            </div>


            <div class="event-content">

                <strong>
                    Finalización de práctica
                </strong>

                <p>
                    Fecha programada de cierre del período
                    de práctica profesional.
                </p>

            </div>


            <span class="event-status">
                Programada
            </span>

        </div>


    </div>


</section>


@endsection
