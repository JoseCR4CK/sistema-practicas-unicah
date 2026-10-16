@extends('layouts.company')

@section('title', 'Jefe inmediato | UNICAH')

@section('content')

<div class="page-header">

    <div>
        <h1>
            Panel del jefe inmediato
        </h1>

        <p>
            Consulta la información general del estudiante
            que realiza su práctica profesional en la empresa.
        </p>
    </div>

    <div class="page-date">
        <i class="bi bi-buildings"></i>
        Grupo Tecnológico S.A.
    </div>

</div>


<!-- BIENVENIDA -->

<section class="company-role-hero">

    <div>

        <span class="section-label">
            BIENVENIDO
        </span>

        <h2>
            Ing. Roberto Martínez
        </h2>

        <p>
            Actualmente tienes un estudiante asignado
            para seguimiento de práctica profesional.
        </p>

    </div>

    <div class="company-role-badge">
        <i class="bi bi-person-check"></i>
    </div>

</section>


<!-- RESUMEN -->

<section class="company-dashboard-grid">

    <article class="company-stat-card">

        <div class="company-stat-icon blue">
            <i class="bi bi-person"></i>
        </div>

        <div>
            <span>Practicantes asignados</span>
            <strong>1</strong>
        </div>

    </article>


    <article class="company-stat-card">

        <div class="company-stat-icon green">
            <i class="bi bi-check-circle"></i>
        </div>

        <div>
            <span>Prácticas activas</span>
            <strong>1</strong>
        </div>

    </article>


    <article class="company-stat-card">

        <div class="company-stat-icon gold">
            <i class="bi bi-calendar-event"></i>
        </div>

        <div>
            <span>Próximo seguimiento</span>
            <strong>Día 45</strong>
        </div>

    </article>

</section>


<!-- ESTUDIANTE -->

<section class="content-card company-student-card">

    <div class="card-header">

        <div>
            <h2>
                Practicante asignado
            </h2>

            <p>
                Información general del estudiante
                que realiza su práctica en la empresa.
            </p>
        </div>

        <span class="badge success">
            Práctica activa
        </span>

    </div>


    <div class="company-student-profile">

        <div class="company-student-avatar">
            AM
        </div>


        <div class="company-student-main">

            <span class="section-label">
                ESTUDIANTE
            </span>

            <h3>
                Andrea Martínez
            </h3>

            <p>
                Ingeniería en Ciencias de la Computación
            </p>

        </div>


        <div class="company-student-progress">

            <span>
                Progreso general
            </span>

            <strong>
                58%
            </strong>

        </div>

    </div>


    <div class="company-progress-bar">

        <div style="width: 58%;"></div>

    </div>


    <div class="summary-grid company-student-data">

        <div class="summary-block">

            <span>
                Número de cuenta
            </span>

            <strong>
                0801-2022-00458
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Inicio de práctica
            </span>

            <strong>
                01/09/2026
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Finalización
            </span>

            <strong>
                30/11/2026
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Día actual
            </span>

            <strong>
                Día 44
            </strong>

        </div>

    </div>

</section>


<!-- EMPRESA Y SEGUIMIENTO -->

<section class="company-two-column">


    <article class="content-card">

        <div class="card-header">

            <div>
                <h2>
                    Información de la empresa
                </h2>

                <p>
                    Empresa asociada al proceso.
                </p>
            </div>

        </div>


        <div class="company-simple-info">

            <div>
                <span>Empresa</span>
                <strong>Grupo Tecnológico S.A.</strong>
            </div>

            <div>
                <span>Jefe inmediato</span>
                <strong>Ing. Roberto Martínez</strong>
            </div>

            <div>
                <span>Cargo</span>
                <strong>Gerente de Tecnología</strong>
            </div>

            <div>
                <span>Estado</span>

                <strong class="success-text">
                    Empresa validada
                </strong>
            </div>

        </div>

    </article>


    <article class="content-card">

        <div class="card-header">

            <div>
                <h2>
                    Próximo seguimiento
                </h2>

                <p>
                    Estado actual del proceso de práctica.
                </p>
            </div>

        </div>


        <div class="company-followup">

            <div class="company-followup-icon">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>

                <span class="section-label">
                    VISITA DE SEGUIMIENTO
                </span>

                <h3>
                    Próxima habilitación
                </h3>

                <p>
                    La visita podrá realizarse a partir
                    del día 45 de la práctica profesional.
                </p>

                <span class="badge warning">
                    Falta 1 día
                </span>

            </div>

        </div>

    </article>

</section>


<!-- NOTA -->

<section class="documents-info-box">

    <div class="documents-info-icon">
        <i class="bi bi-info-circle"></i>
    </div>

    <div>

        <strong>
            Rol del jefe inmediato
        </strong>

        <p>
            Esta interfaz presenta la información principal
            del estudiante y de la práctica asignada. Las
            operaciones específicas del rol serán integradas
            conforme se definan los requisitos funcionales
            correspondientes.
        </p>

    </div>

</section>

@endsection