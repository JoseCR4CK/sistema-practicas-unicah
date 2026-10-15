@extends('layouts.student')


@section('title', 'Mi Práctica Profesional | UNICAH')


@section('content')


<!-- CABECERA -->

<div class="page-header student-welcome">

    <div>

        <span class="welcome-label">
            Bienvenida,
        </span>

        <h1>
            Andrea Martínez
        </h1>

        <p>
            Aquí puedes consultar el avance de tu práctica profesional.
        </p>

    </div>


    <div class="student-period">

        <i class="bi bi-calendar3"></i>

        III Período 2026

    </div>

</div>



<!-- =====================================================
     TARJETA PRINCIPAL DE PROGRESO
===================================================== -->

<section class="student-progress-card">

    <div class="student-progress-header">

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
                58%
            </strong>

            <span>
                completado
            </span>

        </div>

    </div>


    <div class="student-main-progress">

        <div
            class="student-main-progress-fill"
            style="width: 58%;"
        ></div>

    </div>



    <!-- ETAPAS -->

    <div class="student-process-steps">


        <div class="student-process-step completed">

            <div class="process-step-icon">

                <i class="bi bi-check-lg"></i>

            </div>

            <span>
                Solicitud
            </span>

        </div>


        <div class="student-process-step completed">

            <div class="process-step-icon">

                <i class="bi bi-check-lg"></i>

            </div>

            <span>
                Documentos
            </span>

        </div>


        <div class="student-process-step completed">

            <div class="process-step-icon">

                <i class="bi bi-check-lg"></i>

            </div>

            <span>
                Empresa
            </span>

        </div>


        <div class="student-process-step active">

            <div class="process-step-icon">

                <i class="bi bi-briefcase"></i>

            </div>

            <span>
                Práctica
            </span>

        </div>


        <div class="student-process-step pending">

            <div class="process-step-icon">

                <i class="bi bi-calendar-check"></i>

            </div>

            <span>
                Visita
            </span>

        </div>


        <div class="student-process-step pending">

            <div class="process-step-icon">

                <i class="bi bi-journal-text"></i>

            </div>

            <span>
                Monografía
            </span>

        </div>


        <div class="student-process-step pending">

            <div class="process-step-icon">

                <i class="bi bi-mortarboard"></i>

            </div>

            <span>
                Defensa
            </span>

        </div>


        <div class="student-process-step pending">

            <div class="process-step-icon">

                <i class="bi bi-flag"></i>

            </div>

            <span>
                Finalización
            </span>

        </div>


    </div>

</section>



<!-- =====================================================
     INFORMACIÓN PRINCIPAL
===================================================== -->

<section class="student-dashboard-grid">


    <!-- ETAPA ACTUAL -->

    <article class="student-dashboard-card current-stage-card">

        <div class="student-card-header">

            <div class="student-card-icon blue">

                <i class="bi bi-briefcase"></i>

            </div>


            <span>
                Etapa actual
            </span>

        </div>


        <h3>
            Práctica en curso
        </h3>


        <p>
            Actualmente te encuentras desarrollando
            tu práctica profesional.
        </p>


        <div class="student-card-footer">

            <span>
                Día 44
            </span>

            <strong>
                de práctica
            </strong>

        </div>

    </article>



    <!-- PRÓXIMA ACTIVIDAD -->

    <article class="student-dashboard-card">

        <div class="student-card-header">

            <div class="student-card-icon gold">

                <i class="bi bi-calendar-event"></i>

            </div>


            <span>
                Próxima actividad
            </span>

        </div>


        <h3>
            Visita de seguimiento
        </h3>


        <p>
            La visita podrá realizarse a partir
            del día 45 de práctica.
        </p>


        <div class="student-warning">

            <i class="bi bi-clock"></i>

            Falta 1 día

        </div>

    </article>



    <!-- FECHA FINAL -->

    <article class="student-dashboard-card">

        <div class="student-card-header">

            <div class="student-card-icon green">

                <i class="bi bi-calendar2-check"></i>

            </div>


            <span>
                Finalización
            </span>

        </div>


        <h3>
            30 de noviembre
        </h3>


        <p>
            Fecha programada para finalizar
            tu período de práctica.
        </p>


        <div class="student-card-footer">

            <span>
                Inicio:
            </span>

            <strong>
                01/09/2026
            </strong>

        </div>

    </article>


</section>



<!-- =====================================================
     FILA SECUNDARIA
===================================================== -->

<section class="student-secondary-grid">


    <!-- INFORMACIÓN EMPRESA -->

    <article class="content-card student-company-card">

        <div class="card-header">

            <div>

                <h2>
                    Mi empresa
                </h2>

                <p>
                    Información de tu lugar de práctica.
                </p>

            </div>


            <a href="#">
                Ver información
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>


        <div class="student-company-body">


            <div class="company-logo-placeholder">

                <i class="bi bi-buildings"></i>

            </div>


            <div class="company-details">

                <h3>
                    Grupo Tecnológico S.A.
                </h3>


                <span>
                    <i class="bi bi-person-vcard"></i>

                    Jefe inmediato:
                    <strong>
                        Ing. Roberto Martínez
                    </strong>
                </span>


                <span>
                    <i class="bi bi-envelope"></i>

                    roberto@empresa.hn
                </span>


                <span>
                    <i class="bi bi-telephone"></i>

                    +504 9999-9999
                </span>

            </div>


        </div>

    </article>



    <!-- DOCUMENTOS -->

    <article class="content-card student-pending-card">


        <div class="card-header">

            <div>

                <h2>
                    Documentación
                </h2>

                <p>
                    Estado de los documentos requeridos.
                </p>

            </div>


            <a href="#">
                Ver todos
                <i class="bi bi-arrow-right"></i>
            </a>

        </div>



        <div class="student-document-summary">


            <div class="student-document-item">

                <div>

                    <i class="bi bi-check-circle-fill success-icon"></i>

                    <span>
                        Documento de identidad
                    </span>

                </div>

                <strong class="document-approved">
                    Aprobado
                </strong>

            </div>


            <div class="student-document-item">

                <div>

                    <i class="bi bi-check-circle-fill success-icon"></i>

                    <span>
                        Documento testigo 1
                    </span>

                </div>

                <strong class="document-approved">
                    Aprobado
                </strong>

            </div>


            <div class="student-document-item">

                <div>

                    <i class="bi bi-exclamation-circle warning-icon"></i>

                    <span>
                        Documento testigo 2
                    </span>

                </div>

                <strong class="document-warning">
                    Pendiente
                </strong>

            </div>


        </div>


    </article>


</section>



<!-- =====================================================
     AVISOS
===================================================== -->

<section class="content-card student-notices">


    <div class="card-header">

        <div>

            <h2>
                Avisos importantes
            </h2>

            <p>
                Información relacionada con tu proceso.
            </p>

        </div>

    </div>



    <div class="student-notice-list">


        <div class="student-notice">

            <div class="notice-icon blue">

                <i class="bi bi-info-circle"></i>

            </div>


            <div>

                <strong>
                    Próxima habilitación de visita
                </strong>

                <p>
                    Mañana cumplirás 45 días de práctica.
                    A partir de esa fecha podrá realizarse
                    la visita de seguimiento.
                </p>

            </div>

        </div>



        <div class="student-notice">

            <div class="notice-icon gold">

                <i class="bi bi-journal-text"></i>

            </div>


            <div>

                <strong>
                    Recuerda tu monografía
                </strong>

                <p>
                    La entrega deberá realizarse una semana
                    antes de finalizar tu práctica profesional.
                </p>

            </div>

        </div>


    </div>


</section>


@endsection