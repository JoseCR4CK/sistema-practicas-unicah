@extends('layouts.student')

@section('title', 'Docente asignado | UNICAH')

@section('content')


<!-- CABECERA -->

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            MI PROCESO
        </span>

        <h1>
            Docente asignado
        </h1>

        <p>
            Consulta la información del docente encargado
            del seguimiento académico de tu práctica profesional.
        </p>

    </div>


    <div class="teacher-status">

        <i class="bi bi-check-circle"></i>

        Docente asignado

    </div>

</div>



<!-- PERFIL PRINCIPAL -->

<section class="teacher-profile-card">

    <div class="teacher-profile-main">

        <div class="teacher-avatar">
            CM
        </div>


        <div>

            <span class="section-label">
                DOCENTE DE SEGUIMIENTO
            </span>

            <h2>
                Msc. Carlos Martínez
            </h2>

            <p>
                Facultad de Ingeniería
            </p>

            <span class="teacher-active-badge">

                <i class="bi bi-check-circle-fill"></i>

                Asignación activa

            </span>

        </div>

    </div>


    <div class="teacher-profile-period">

        <span>
            Período académico
        </span>

        <strong>
            III Período 2026
        </strong>

    </div>

</section>



<!-- INFORMACIÓN -->

<section class="row g-4 mb-4">


    <div class="col-12 col-xl-7">

        <div class="content-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Información del docente
                    </h2>

                    <p>
                        Datos relacionados con tu seguimiento académico.
                    </p>

                </div>

            </div>


            <div class="teacher-info-grid">


                <div class="teacher-info-item">

                    <span>
                        Docente
                    </span>

                    <strong>
                        Msc. Carlos Martínez
                    </strong>

                </div>


                <div class="teacher-info-item">

                    <span>
                        Facultad
                    </span>

                    <strong>
                        Ingeniería
                    </strong>

                </div>


                <div class="teacher-info-item">

                    <span>
                        Función
                    </span>

                    <strong>
                        Seguimiento de práctica
                    </strong>

                </div>


                <div class="teacher-info-item">

                    <span>
                        Estado
                    </span>

                    <strong class="teacher-success">
                        Asignado
                    </strong>

                </div>


                <div class="teacher-info-item">

                    <span>
                        Período
                    </span>

                    <strong>
                        III Período 2026
                    </strong>

                </div>


                <div class="teacher-info-item">

                    <span>
                        Campus
                    </span>

                    <strong>
                        UNICAH
                    </strong>

                </div>


            </div>

        </div>

    </div>



    <!-- SEGUIMIENTO -->

    <div class="col-12 col-xl-5">

        <div class="content-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Próximo seguimiento
                    </h2>

                    <p>
                        Estado de tu acompañamiento académico.
                    </p>

                </div>

            </div>


            <div class="teacher-followup">

                <div class="teacher-followup-icon">

                    <i class="bi bi-calendar-check"></i>

                </div>


                <div>

                    <span class="section-label">
                        PRÓXIMA ACTIVIDAD
                    </span>

                    <h3>
                        Visita de seguimiento
                    </h3>

                    <p>
                        La visita podrá programarse una vez
                        habilitado el proceso correspondiente.
                    </p>

                    <a
                        href="{{ route('estudiante.visita') }}"
                        class="student-inline-link"
                    >

                        Ver estado de visita

                        <i class="bi bi-arrow-right"></i>

                    </a>

                </div>

            </div>

        </div>

    </div>


</section>



<!-- FUNCIÓN DEL DOCENTE -->

<section class="content-card teacher-functions-card">

    <div class="card-header">

        <div>

            <h2>
                ¿Cómo participa mi docente?
            </h2>

            <p>
                Su función dentro de tu proceso de práctica profesional.
            </p>

        </div>

    </div>


    <div class="row g-3 teacher-functions">


        <div class="col-12 col-md-4">

            <div class="teacher-function-item">

                <div class="teacher-function-icon blue">

                    <i class="bi bi-person-check"></i>

                </div>

                <h3>
                    Seguimiento académico
                </h3>

                <p>
                    Da seguimiento al desarrollo de tu práctica
                    y al cumplimiento de las etapas académicas.
                </p>

            </div>

        </div>


        <div class="col-12 col-md-4">

            <div class="teacher-function-item">

                <div class="teacher-function-icon gold">

                    <i class="bi bi-calendar-check"></i>

                </div>

                <h3>
                    Visita de práctica
                </h3>

                <p>
                    Participa en el proceso de seguimiento
                    relacionado con la visita de práctica.
                </p>

            </div>

        </div>


        <div class="col-12 col-md-4">

            <div class="teacher-function-item">

                <div class="teacher-function-icon green">

                    <i class="bi bi-clipboard-check"></i>

                </div>

                <h3>
                    Acompañamiento
                </h3>

                <p>
                    Consulta el progreso académico y las etapas
                    institucionales relacionadas con el practicante.
                </p>

            </div>

        </div>


    </div>

</section>



<!-- AVISO -->

<div class="teacher-privacy-note">

    <i class="bi bi-shield-lock"></i>

    <div>

        <strong>
            Tu espacio personal continúa siendo privado
        </strong>

        <p>
            El docente asignado no tiene acceso a tus notas,
            pendientes, agenda personal ni bitácora privada.
        </p>

    </div>

</div>


@endsection