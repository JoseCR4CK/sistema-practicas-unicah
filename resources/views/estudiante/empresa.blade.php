@extends('layouts.student')

@section('title', 'Empresa | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <h1>
            Empresa
        </h1>

        <p>
            Consulta la información de la empresa
            donde realizas tu práctica profesional.
        </p>

    </div>

    <div class="page-date">

        <i class="bi bi-patch-check"></i>

        Empresa validada

    </div>

</div>



<!-- ESTADO -->

<section class="company-overview-card">

    <div class="company-overview-main">

        <div class="company-main-icon">

            <i class="bi bi-buildings"></i>

        </div>


        <div>

            <span class="section-label">
                EMPRESA DE PRÁCTICA
            </span>

            <h2>
                Grupo Tecnológico S.A.
            </h2>

            <p>
                Información registrada y validada
                para tu proceso de práctica profesional.
            </p>

        </div>

    </div>


    <span class="badge success">

        <i class="bi bi-check-circle"></i>

        Validada

    </span>

</section>



<!-- INFORMACIÓN GENERAL -->

<section class="content-card company-information-card">

    <div class="card-header">

        <div>

            <h2>
                Información de la empresa
            </h2>

            <p>
                Datos generales registrados en el sistema.
            </p>

        </div>

    </div>


    <div class="summary-grid">

        <div class="summary-block">

            <span>
                Nombre de la empresa
            </span>

            <strong>
                Grupo Tecnológico S.A.
            </strong>

        </div>


        <div class="summary-block">

            <span>
                RTN
            </span>

            <strong>
                0801-1999-123456
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Ciudad
            </span>

            <strong>
                Tegucigalpa
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Dirección
            </span>

            <strong>
                Tegucigalpa, Honduras
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Teléfono
            </span>

            <strong>
                +504 2222-2222
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Correo
            </span>

            <strong>
                contacto@grupotecnologico.hn
            </strong>

        </div>

    </div>

</section>



<!-- JEFE INMEDIATO -->

<section class="content-card company-supervisor-card">

    <div class="card-header">

        <div>

            <h2>
                Jefe inmediato
            </h2>

            <p>
                Responsable de supervisar tu trabajo
                dentro de la empresa.
            </p>

        </div>

    </div>


    <div class="company-supervisor-content">

        <div class="supervisor-avatar">
            RM
        </div>


        <div class="supervisor-main">

            <h3>
                Ing. Roberto Martínez
            </h3>

            <span>
                Gerente de Tecnología
            </span>

        </div>


        <div class="supervisor-contact">

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

</section>



<!-- INFORMACIÓN DE LA PRÁCTICA -->

<section class="practice-info-grid">

    <article class="practice-info-card">

        <div class="practice-info-icon blue">

            <i class="bi bi-calendar2-check"></i>

        </div>

        <div>

            <span>
                Inicio
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
                Finalización
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
                Práctica activa
            </strong>

        </div>

    </article>

</section>



<!-- VALIDACIÓN -->

<section class="content-card company-validation-card">

    <div class="card-header">

        <div>

            <h2>
                Validación
            </h2>

            <p>
                Estado de la información relacionada
                con la empresa.
            </p>

        </div>

    </div>


    <div class="company-validation-list">


        <div class="company-validation-item">

            <div class="validation-icon complete">

                <i class="bi bi-check-lg"></i>

            </div>

            <div>

                <strong>
                    Datos de empresa
                </strong>

                <span>
                    Información validada
                </span>

            </div>

            <span class="badge success">
                Aprobado
            </span>

        </div>



        <div class="company-validation-item">

            <div class="validation-icon complete">

                <i class="bi bi-check-lg"></i>

            </div>

            <div>

                <strong>
                    Jefe inmediato
                </strong>

                <span>
                    Responsable confirmado
                </span>

            </div>

            <span class="badge success">
                Aprobado
            </span>

        </div>



        <div class="company-validation-item">

            <div class="validation-icon complete">

                <i class="bi bi-check-lg"></i>

            </div>

            <div>

                <strong>
                    Fechas de práctica
                </strong>

                <span>
                    Inicio y finalización establecidos
                </span>

            </div>

            <span class="badge success">
                Aprobado
            </span>

        </div>


    </div>

</section>



<!-- NOTA -->

<section class="documents-info-box">

    <div class="documents-info-icon">

        <i class="bi bi-info-circle"></i>

    </div>

    <div>

        <strong>
            Información de la empresa
        </strong>

        <p>
            Estos datos forman parte de tu expediente de práctica.
            Si detectas información incorrecta, deberá ser revisada
            por el área responsable antes de modificar el registro validado.
        </p>

    </div>

</section>


@endsection