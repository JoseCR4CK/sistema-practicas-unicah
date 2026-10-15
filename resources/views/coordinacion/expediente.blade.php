@extends('layouts.app')

@section('title', 'Expediente del estudiante | Coordinación Académica')

@section('content')

<!-- CABECERA -->

<div class="page-header expediente-page-header">

    <div>

        <a
            href="{{ route('coordinacion.estudiantes') }}"
            class="back-link"
        >
            <i class="bi bi-arrow-left"></i>
            Volver a estudiantes
        </a>

        <h1>
            Expediente del estudiante
        </h1>

        <p>
            Seguimiento completo del proceso de práctica profesional.
        </p>

    </div>

    <div class="expediente-actions">

        <button class="secondary-button">
            <i class="bi bi-printer"></i>
            Imprimir
        </button>

        <button class="primary-button">
            <i class="bi bi-pencil"></i>
            Editar información
        </button>

    </div>

</div>


<!-- INFORMACIÓN DEL ESTUDIANTE -->

<section class="student-profile-card">

    <div class="student-profile-main">

        <div class="student-profile-avatar">
            AM
        </div>

        <div class="student-profile-info">

            <div class="student-profile-title">

                <h2>
                    Andrea Martínez
                </h2>

                <span class="status-pill active">
                    En práctica
                </span>

            </div>

            <p>
                Ingeniería en Ciencias de la Computación
            </p>

            <div class="student-profile-meta">

                <span>
                    <i class="bi bi-person-badge"></i>
                    0801-2022-00458
                </span>

                <span>
                    <i class="bi bi-envelope"></i>
                    amartinez@unicah.edu
                </span>

                <span>
                    <i class="bi bi-card-text"></i>
                    0801-2003-12345
                </span>

            </div>

        </div>

    </div>

    <div class="student-profile-side">

        <span>
            Etapa actual
        </span>

        <strong>
            Seguimiento de práctica
        </strong>

    </div>

</section>


<!-- PROGRESO GENERAL -->

<section class="content-card progress-section">

    <div class="progress-section-header">

        <div>

            <h2>
                Progreso de la práctica
            </h2>

            <p>
                Seguimiento general de las etapas del proceso.
            </p>

        </div>

        <div class="progress-percentage">
            58%
        </div>

    </div>


    <div class="main-progress-bar">

        <div
            class="main-progress-fill"
            style="width: 58%;"
        ></div>

    </div>


    <div class="practice-timeline">


        <div class="timeline-step completed">

            <div class="timeline-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <div>
                <strong>
                    Solicitud
                </strong>

                <span>
                    Aprobada
                </span>
            </div>

        </div>


        <div class="timeline-step completed">

            <div class="timeline-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <div>
                <strong>
                    Documentación
                </strong>

                <span>
                    Validada
                </span>
            </div>

        </div>


        <div class="timeline-step completed">

            <div class="timeline-icon">
                <i class="bi bi-check-lg"></i>
            </div>

            <div>
                <strong>
                    Empresa
                </strong>

                <span>
                    Validada
                </span>
            </div>

        </div>


        <div class="timeline-step active">

            <div class="timeline-icon">
                <i class="bi bi-briefcase"></i>
            </div>

            <div>
                <strong>
                    Práctica
                </strong>

                <span>
                    En curso
                </span>
            </div>

        </div>


        <div class="timeline-step pending">

            <div class="timeline-icon">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>
                <strong>
                    Visita
                </strong>

                <span>
                    Próximamente
                </span>
            </div>

        </div>


        <div class="timeline-step pending">

            <div class="timeline-icon">
                <i class="bi bi-journal-text"></i>
            </div>

            <div>
                <strong>
                    Monografía
                </strong>

                <span>
                    Pendiente
                </span>
            </div>

        </div>


        <div class="timeline-step pending">

            <div class="timeline-icon">
                <i class="bi bi-mortarboard"></i>
            </div>

            <div>
                <strong>
                    Defensa
                </strong>

                <span>
                    Pendiente
                </span>
            </div>

        </div>


        <div class="timeline-step pending">

            <div class="timeline-icon">
                <i class="bi bi-flag"></i>
            </div>

            <div>
                <strong>
                    Finalización
                </strong>

                <span>
                    Pendiente
                </span>
            </div>

        </div>

    </div>

</section>


<!-- INFORMACIÓN RÁPIDA -->

<section class="expediente-info-grid">


    <div class="info-card">

        <div class="info-card-icon blue">
            <i class="bi bi-buildings"></i>
        </div>

        <div>

            <span>
                Empresa
            </span>

            <strong>
                Grupo Tecnológico S.A.
            </strong>

        </div>

    </div>


    <div class="info-card">

        <div class="info-card-icon gold">
            <i class="bi bi-person-vcard"></i>
        </div>

        <div>

            <span>
                Jefe inmediato
            </span>

            <strong>
                Ing. Roberto Martínez
            </strong>

        </div>

    </div>


    <div class="info-card">

        <div class="info-card-icon green">
            <i class="bi bi-calendar2-check"></i>
        </div>

        <div>

            <span>
                Inicio de práctica
            </span>

            <strong>
                01/09/2026
            </strong>

        </div>

    </div>


    <div class="info-card">

        <div class="info-card-icon red">
            <i class="bi bi-calendar2-event"></i>
        </div>

        <div>

            <span>
                Finalización
            </span>

            <strong>
                30/11/2026
            </strong>

        </div>

    </div>

</section>


<!-- CONTROL TEMPORAL -->

<section class="practice-time-grid">


    <div class="content-card practice-time-card">

        <div class="time-card-icon">
            <i class="bi bi-clock-history"></i>
        </div>

        <div>

            <span>
                Día actual de práctica
            </span>

            <strong>
                Día 44
            </strong>

            <p>
                La visita podrá realizarse a partir del día 45.
            </p>

        </div>

    </div>


    <div class="content-card practice-time-card">

        <div class="time-card-icon gold">
            <i class="bi bi-file-earmark-arrow-up"></i>
        </div>

        <div>

            <span>
                Entrega de monografía
            </span>

            <strong>
                23/11/2026
            </strong>

            <p>
                Debe entregarse una semana antes de finalizar la práctica.
            </p>

        </div>

    </div>

</section>


<!-- PESTAÑAS -->

<section class="content-card expediente-tabs-card">

    <div class="expediente-tabs">

        <button class="expediente-tab active" data-tab="resumen">
            Resumen
        </button>

        <button class="expediente-tab" data-tab="documentos">
            Documentos
        </button>

        <button class="expediente-tab" data-tab="empresa">
            Empresa
        </button>

        <button class="expediente-tab" data-tab="terna">
            Terna
        </button>

        <button class="expediente-tab" data-tab="visita">
            Visita
        </button>

        <button class="expediente-tab" data-tab="monografia">
            Monografía
        </button>

        <button class="expediente-tab" data-tab="defensa">
            Defensa
        </button>

        <button class="expediente-tab" data-tab="historial">
            Historial
        </button>

    </div>


    <!-- RESUMEN -->

    <div class="tab-content active" id="resumen">

        <div class="tab-section-title">

            <div>

                <h3>
                    Resumen del proceso
                </h3>

                <p>
                    Información principal del proceso de práctica.
                </p>

            </div>

        </div>


        <div class="summary-grid">


            <div class="summary-block">

                <span>
                    Estado de solicitud
                </span>

                <strong class="text-success">
                    Aprobada
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Documentación
                </span>

                <strong class="text-success">
                    Completa
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Empresa
                </span>

                <strong>
                    Grupo Tecnológico S.A.
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Jefe inmediato
                </span>

                <strong>
                    Ing. Roberto Martínez
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Fecha de inicio
                </span>

                <strong>
                    01/09/2026
                </strong>

            </div>


            <div class="summary-block">

                <span>
                    Fecha de finalización
                </span>

                <strong>
                    30/11/2026
                </strong>

            </div>

        </div>

    </div>


    <!-- DOCUMENTOS -->

    <div class="tab-content" id="documentos">

        <div class="tab-section-title">

            <div>

                <h3>
                    Documentación
                </h3>

                <p>
                    Documentos presentados por el estudiante.
                </p>

            </div>

        </div>


        <div class="documents-list">


            <div class="document-row">

                <div class="document-info">

                    <div class="document-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>

                    <div>

                        <strong>
                            Documento de identidad
                        </strong>

                        <span>
                            PDF · Cargado 03/09/2026
                        </span>

                    </div>

                </div>

                <span class="badge success">
                    Aprobado
                </span>

            </div>


            <div class="document-row">

                <div class="document-info">

                    <div class="document-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>

                    <div>

                        <strong>
                            Documento testigo 1
                        </strong>

                        <span>
                            PDF · Cargado 03/09/2026
                        </span>

                    </div>

                </div>

                <span class="badge success">
                    Aprobado
                </span>

            </div>


            <div class="document-row">

                <div class="document-info">

                    <div class="document-icon">
                        <i class="bi bi-file-earmark-pdf"></i>
                    </div>

                    <div>

                        <strong>
                            Documento testigo 2
                        </strong>

                        <span>
                            PDF · Cargado 03/09/2026
                        </span>

                    </div>

                </div>

                <span class="badge success">
                    Aprobado
                </span>

            </div>

        </div>

    </div>


    <!-- EMPRESA -->

    <div class="tab-content" id="empresa">

        <div class="tab-section-title">

            <div>

                <h3>
                    Empresa y jefe inmediato
                </h3>

                <p>
                    Información del lugar donde se desarrolla la práctica.
                </p>

            </div>

        </div>


        <div class="summary-grid">

            <div class="summary-block">
                <span>Empresa</span>
                <strong>Grupo Tecnológico S.A.</strong>
            </div>

            <div class="summary-block">
                <span>RTN</span>
                <strong>0801-1999-123456</strong>
            </div>

            <div class="summary-block">
                <span>Jefe inmediato</span>
                <strong>Ing. Roberto Martínez</strong>
            </div>

            <div class="summary-block">
                <span>Cargo</span>
                <strong>Gerente de Tecnología</strong>
            </div>

            <div class="summary-block">
                <span>Teléfono</span>
                <strong>+504 9999-9999</strong>
            </div>

            <div class="summary-block">
                <span>Correo</span>
                <strong>roberto@empresa.hn</strong>
            </div>

        </div>

    </div>


    <!-- TERNA -->

    <div class="tab-content" id="terna">

        <div class="empty-state">

            <i class="bi bi-people"></i>

            <h3>
                Terna pendiente
            </h3>

            <p>
                La terna de defensa todavía no ha sido asignada.
            </p>

        </div>

    </div>


    <!-- VISITA -->

    <div class="tab-content" id="visita">

        <div class="empty-state">

            <i class="bi bi-calendar-check"></i>

            <h3>
                Visita aún no disponible
            </h3>

            <p>
                La visita podrá programarse a partir del día 45 de práctica.
            </p>

            <span class="countdown-badge">
                Falta 1 día
            </span>

        </div>

    </div>


    <!-- MONOGRAFÍA -->

    <div class="tab-content" id="monografia">

        <div class="empty-state">

            <i class="bi bi-journal-text"></i>

            <h3>
                Monografía pendiente
            </h3>

            <p>
                La entrega estará disponible una semana antes de finalizar la práctica.
            </p>

        </div>

    </div>


    <!-- DEFENSA -->

    <div class="tab-content" id="defensa">

        <div class="empty-state">

            <i class="bi bi-mortarboard"></i>

            <h3>
                Defensa pendiente
            </h3>

            <p>
                La calendarización estará disponible cuando las revisiones de monografía hayan sido aprobadas.
            </p>

        </div>

    </div>


    <!-- HISTORIAL -->

    <div class="tab-content" id="historial">

        <div class="history-list">


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        Inicio de práctica
                    </strong>

                    <span>
                        01 de septiembre de 2026
                    </span>

                </div>

            </div>


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        Documentación aprobada
                    </strong>

                    <span>
                        29 de agosto de 2026
                    </span>

                </div>

            </div>


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        Empresa validada
                    </strong>

                    <span>
                        27 de agosto de 2026
                    </span>

                </div>

            </div>


            <div class="history-item">

                <div class="history-dot"></div>

                <div>

                    <strong>
                        Solicitud aprobada
                    </strong>

                    <span>
                        25 de agosto de 2026
                    </span>

                </div>

            </div>


        </div>

    </div>


</section>


@endsection


@push('scripts')

<script>

    const tabs =
        document.querySelectorAll('.expediente-tab');

    const contents =
        document.querySelectorAll('.tab-content');


    tabs.forEach(tab => {

        tab.addEventListener('click', function () {

            tabs.forEach(item =>
                item.classList.remove('active')
            );

            contents.forEach(content =>
                content.classList.remove('active')
            );

            this.classList.add('active');

            document
                .getElementById(this.dataset.tab)
                .classList.add('active');

        });

    });

</script>

@endpush