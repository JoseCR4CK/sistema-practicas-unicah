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

        Práctica activa

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
            Grupo Tecnológico S.A.
        </h2>

        <p>
            Ingeniería en Ciencias de la Computación
        </p>


        <div class="practice-status-row">

            <span class="status-pill active">
                En práctica
            </span>

            <span class="practice-code">
                Proceso #PP-2026-00458
            </span>

        </div>

    </div>


    <div class="practice-overview-progress">

        <span>
            Progreso general
        </span>

        <strong>
            58%
        </strong>

        <div class="mini-progress">

            <div
                class="mini-progress-fill"
                style="width: 58%;"
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
                01/09/2026
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
                30/11/2026
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
                Día 44
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
                Activa
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
                    Grupo Tecnológico S.A.
                </h3>

                <span>
                    <i class="bi bi-geo-alt"></i>
                    Tegucigalpa, Honduras
                </span>

                <span>
                    <i class="bi bi-envelope"></i>
                    contacto@grupotecnologico.hn
                </span>

                <span>
                    <i class="bi bi-telephone"></i>
                    +504 2222-2222
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
                    Ing. Roberto Martínez
                </h3>

                <span>
                    <i class="bi bi-briefcase"></i>
                    Gerente de Tecnología
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

        <div class="practice-stage completed">

            <div class="practice-stage-icon">

                <i class="bi bi-check-lg"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Solicitud de práctica
                    </strong>

                    <span>
                        Aprobada por Coordinación Académica
                    </span>

                </div>


                <span class="badge success">
                    Completada
                </span>

            </div>

        </div>



        <!-- DOCUMENTOS -->

        <div class="practice-stage completed">

            <div class="practice-stage-icon">

                <i class="bi bi-check-lg"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Documentación inicial
                    </strong>

                    <span>
                        Documentos revisados y aprobados
                    </span>

                </div>


                <span class="badge success">
                    Completada
                </span>

            </div>

        </div>



        <!-- EMPRESA -->

        <div class="practice-stage completed">

            <div class="practice-stage-icon">

                <i class="bi bi-check-lg"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Validación de empresa
                    </strong>

                    <span>
                        Empresa y jefe inmediato confirmados
                    </span>

                </div>


                <span class="badge success">
                    Completada
                </span>

            </div>

        </div>



        <!-- PRACTICA -->

        <div class="practice-stage active">

            <div class="practice-stage-icon">

                <i class="bi bi-briefcase"></i>

            </div>


            <div class="practice-stage-content">

                <div>

                    <strong>
                        Desarrollo de práctica
                    </strong>

                    <span>
                        Actualmente te encuentras en el día 44
                    </span>

                </div>


                <span class="badge warning">
                    En curso
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
                    Próximamente
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
                    Pendiente
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
                        Se habilitará después de aprobar la monografía
                    </span>

                </div>


                <span class="badge info">
                    Pendiente
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

                <strong>
                    23
                </strong>

                <span>
                    NOV
                </span>

            </div>


            <div class="event-content">

                <strong>
                    Entrega de monografía
                </strong>

                <p>
                    Fecha estimada de entrega antes
                    de finalizar tu práctica.
                </p>

            </div>


            <span class="event-status">
                Pendiente
            </span>

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