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

        {{ $process?->company ? 'Empresa asignada' : 'Sin empresa asignada' }}

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
                {{ $process?->company?->official_name ?? 'Sin empresa asignada' }}
            </h2>

            <p>
                Información registrada
                para tu proceso de práctica profesional.
            </p>

        </div>

    </div>


    <span class="badge success">

        <i class="bi bi-check-circle"></i>

        {{ $process?->company ? 'Asignada' : 'Sin asignar' }}

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
                {{ $process?->company?->official_name ?? 'Sin empresa asignada' }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                RTN
            </span>

            <strong>
                {{ $process?->company?->rtn ?? 'Sin registrar' }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Ciudad
            </span>

            <strong>
                {{ $process?->company?->city ?? 'Sin registrar' }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Dirección
            </span>

            <strong>
                {{ $process?->company?->address ?? 'Sin registrar' }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Teléfono
            </span>

            <strong>
                {{ $process?->company?->phone ?? 'Sin registrar' }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Correo
            </span>

            <strong>
                {{ $process?->company?->email ?? 'Sin registrar' }}
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
            {{ $process?->companyResponsible ? Str::initials($process->companyResponsible->full_name, true) : '—' }}
        </div>


        <div class="supervisor-main">

            <h3>
                {{ $process?->companyResponsible?->full_name ?? 'Sin responsable asignado' }}
            </h3>

            <span>
                {{ $process?->companyResponsible?->position ?? 'Sin registrar' }}
            </span>

        </div>


        <div class="supervisor-contact">

            <span>

                <i class="bi bi-envelope"></i>

                {{ $process?->companyResponsible?->email ?? 'Sin registrar' }}

            </span>


            <span>

                <i class="bi bi-telephone"></i>

                {{ $process?->companyResponsible?->phone ?? 'Sin registrar' }}

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
                Finalización
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
                {{ $process?->currentDay() ?? 'Sin iniciar' }}
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
                {{ $process?->statusLabel() ?? 'Sin práctica registrada' }}
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
                    {{ $process?->company ? 'Información registrada' : 'Sin empresa registrada' }}
                </span>

            </div>

            <span class="badge success">
                Consultar registro
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
                    {{ $process?->companyResponsible ? 'Responsable asignado' : 'Sin responsable asignado' }}
                </span>

            </div>

            <span class="badge success">
                Consultar registro
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
                    {{ $process?->start_date && $process?->end_date ? 'Inicio y finalización registrados' : 'Fechas pendientes' }}
                </span>

            </div>

            <span class="badge success">
                Consultar registro
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
            por el área responsable antes de modificar el registro.
        </p>

    </div>

</section>


@endsection
