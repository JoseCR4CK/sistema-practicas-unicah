@extends('layouts.student')

@section('title', 'Visita de seguimiento | UNICAH')

@section('content')


<!-- CABECERA -->

<div class="page-header">

    <div>

        <h1>
            Visita de seguimiento
        </h1>

        <p>
            Consulta el estado y la información relacionada
            con la visita de seguimiento de tu práctica profesional.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-calendar-check"></i>

        Próximamente

    </div>

</div>



<!-- ESTADO PRINCIPAL -->

<section class="visit-overview-card">

    <div class="visit-overview-content">

        <span class="section-label">
            ESTADO DE LA VISITA
        </span>

        <h2>
            Aún no disponible
        </h2>

        <p>
            La visita de seguimiento podrá realizarse
            a partir del día 45 de tu práctica profesional.
        </p>

    </div>


    <div class="visit-countdown">

        <span>
            Día actual
        </span>

        <strong>
            44
        </strong>

        <small>
            Falta 1 día
        </small>

    </div>

</section>



<!-- PROGRESO HACIA LA VISITA -->

<section class="content-card visit-progress-card">

    <div class="visit-progress-header">

        <div>

            <h2>
                Habilitación de visita
            </h2>

            <p>
                La visita se habilita al alcanzar 45 días
                desde el inicio de la práctica.
            </p>

        </div>

        <strong>
            44 / 45 días
        </strong>

    </div>


    <div class="visit-progress-bar">

        <div
            class="visit-progress-fill"
            style="width: 97.7%;"
        ></div>

    </div>


    <div class="visit-progress-dates">

        <span>
            <i class="bi bi-calendar2-check"></i>
            Inicio: 01/09/2026
        </span>

        <span>
            <i class="bi bi-flag"></i>
            Habilitación: día 45
        </span>

    </div>

</section>



<!-- INFORMACIÓN -->

<section class="visit-info-grid">


    <article class="visit-info-card">

        <div class="visit-info-icon blue">

            <i class="bi bi-person-video3"></i>

        </div>


        <div>

            <span>
                Responsable
            </span>

            <strong>
                Docente asignado
            </strong>

            <p>
                La información aparecerá cuando
                la visita sea programada.
            </p>

        </div>

    </article>



    <article class="visit-info-card">

        <div class="visit-info-icon gold">

            <i class="bi bi-calendar-event"></i>

        </div>


        <div>

            <span>
                Fecha
            </span>

            <strong>
                Por programar
            </strong>

            <p>
                Estará disponible después
                de cumplir el día 45.
            </p>

        </div>

    </article>



    <article class="visit-info-card">

        <div class="visit-info-icon green">

            <i class="bi bi-buildings"></i>

        </div>


        <div>

            <span>
                Empresa
            </span>

            <strong>
                Grupo Tecnológico S.A.
            </strong>

            <p>
                Lugar donde se realizará
                el seguimiento de práctica.
            </p>

        </div>

    </article>


</section>



<!-- FLUJO -->

<section class="content-card visit-process-card">

    <div class="card-header">

        <div>

            <h2>
                Proceso de visita
            </h2>

            <p>
                Etapas relacionadas con la visita de seguimiento.
            </p>

        </div>

    </div>


    <div class="visit-process-list">


        <article class="visit-process-step completed">

            <div class="visit-process-icon">

                <i class="bi bi-check-lg"></i>

            </div>


            <div class="visit-process-content">

                <div>

                    <strong>
                        Inicio de práctica
                    </strong>

                    <span>
                        01 de septiembre de 2026
                    </span>

                </div>


                <span class="badge success">
                    Completado
                </span>

            </div>

        </article>



        <article class="visit-process-step active">

            <div class="visit-process-icon">

                <i class="bi bi-clock-history"></i>

            </div>


            <div class="visit-process-content">

                <div>

                    <strong>
                        Cumplimiento de 45 días
                    </strong>

                    <span>
                        Actualmente te encuentras en el día 44
                    </span>

                </div>


                <span class="badge warning">
                    En proceso
                </span>

            </div>

        </article>



        <article class="visit-process-step pending">

            <div class="visit-process-icon">

                <i class="bi bi-calendar-plus"></i>

            </div>


            <div class="visit-process-content">

                <div>

                    <strong>
                        Programación de visita
                    </strong>

                    <span>
                        Se habilitará al cumplir el requisito de tiempo
                    </span>

                </div>


                <span class="badge info">
                    Pendiente
                </span>

            </div>

        </article>



        <article class="visit-process-step pending">

            <div class="visit-process-icon">

                <i class="bi bi-person-check"></i>

            </div>


            <div class="visit-process-content">

                <div>

                    <strong>
                        Realización de visita
                    </strong>

                    <span>
                        El docente realizará el seguimiento correspondiente
                    </span>

                </div>


                <span class="badge info">
                    Pendiente
                </span>

            </div>

        </article>



        <article class="visit-process-step pending">

            <div class="visit-process-icon">

                <i class="bi bi-file-earmark-check"></i>

            </div>


            <div class="visit-process-content">

                <div>

                    <strong>
                        Validación de visita
                    </strong>

                    <span>
                        Se registrarán las evidencias o documentos requeridos
                    </span>

                </div>


                <span class="badge info">
                    Pendiente
                </span>

            </div>

        </article>


    </div>

</section>



<!-- DOCUMENTOS / EVIDENCIAS -->

<section class="content-card visit-evidence-card">

    <div class="card-header">

        <div>

            <h2>
                Evidencias de la visita
            </h2>

            <p>
                Documentos o evidencias que respaldarán
                la realización de la visita.
            </p>

        </div>

    </div>


    <div class="visit-locked-section">

        <div class="visit-lock-icon">

            <i class="bi bi-lock"></i>

        </div>


        <h3>
            Evidencias aún no disponibles
        </h3>


        <p>
            Esta sección se habilitará cuando la visita
            haya sido programada y realizada.
        </p>


        <div class="visit-note">

            <i class="bi bi-info-circle"></i>

            <span>
                Los documentos específicos para validar la visita
                se definirán según los requisitos establecidos
                para el proceso de práctica.
            </span>

        </div>

    </div>

</section>



<!-- INFORMACIÓN IMPORTANTE -->

<section class="documents-info-box">

    <div class="documents-info-icon">

        <i class="bi bi-info-circle"></i>

    </div>


    <div>

        <strong>
            Sobre la visita de seguimiento
        </strong>

        <p>
            El cumplimiento del día 45 habilita la posibilidad
            de realizar la visita, pero no significa que deba
            efectuarse exactamente ese día. La fecha dependerá
            de la programación correspondiente.
        </p>

    </div>

</section>


@endsection