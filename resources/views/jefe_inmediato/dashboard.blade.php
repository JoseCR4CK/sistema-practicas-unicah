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
        {{ $practices->pluck('company.official_name')->filter()->unique()->join(' · ') ?: 'Sin empresa vinculada' }}
    </div>

</div>


<!-- BIENVENIDA -->

<section class="company-role-hero">

    <div>

        <span class="section-label">
            BIENVENIDO
        </span>

        <h2>
            {{ auth()->user()->name }}
        </h2>

        <p>
            Actualmente tienes {{ $practices->unique('student_id')->count() }} practicantes asignados.
        </p>

    </div>

    <div class="company-role-badge">
        <i class="bi bi-person-check"></i>
    </div>

</section>


<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-6 col-xl-4">

        <article class="company-stat-card h-100">

            <div class="company-stat-icon blue">
                <i class="bi bi-person"></i>
            </div>

            <div>
                <span>Practicantes asignados</span>
                <strong>{{ $practices->unique('student_id')->count() }}</strong>
            </div>

        </article>

    </div>


    <div class="col-12 col-md-6 col-xl-4">

        <article class="company-stat-card h-100">

            <div class="company-stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Prácticas activas</span>
                <strong>{{ $practices->where('status', 'ACTIVE')->count() }}</strong>
            </div>

        </article>

    </div>


    <div class="col-12 col-md-6 col-xl-4">

        <article class="company-stat-card h-100">

            <div class="company-stat-icon gold">
                <i class="bi bi-calendar-event"></i>
            </div>

            <div>
                <span>Visitas registradas</span>
                <strong>{{ $practices->sum(fn ($practice) => $practice->visits->count()) }}</strong>
            </div>

        </article>

    </div>

</section>


<!-- ESTUDIANTE -->
@forelse ($practices as $practice)

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
            {{ $practice->statusLabel() }}
        </span>

    </div>


    <div class="company-student-profile">

        <div class="company-student-avatar">
            {{ Str::initials($practice->student->user->name, true) }}
        </div>


        <div class="company-student-main">

            <span class="section-label">
                ESTUDIANTE
            </span>

            <h3>
                {{ $practice->student->user->name }}
            </h3>

            <p>
                {{ $practice->student->career }}
            </p>

        </div>


        <div class="company-student-progress">

            <span>
                Progreso general
            </span>

            <strong>
                {{ $practice->progress }}%
            </strong>

        </div>

    </div>


    <div class="company-progress-bar">

        <div style="width: {{ $practice->progress }}%;"></div>

    </div>


    <div class="summary-grid company-student-data">

        <div class="summary-block">

            <span>
                Número de cuenta
            </span>

            <strong>
                {{ $practice->student->account_number }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Inicio de práctica
            </span>

            <strong>
                {{ $practice->start_date?->format('d/m/Y') ?? 'Pendiente' }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Finalización
            </span>

            <strong>
                {{ $practice->end_date?->format('d/m/Y') ?? 'Pendiente' }}
            </strong>

        </div>


        <div class="summary-block">

            <span>
                Día actual
            </span>

            <strong>
                {{ $practice->currentDay() ?? 'Sin iniciar' }}
            </strong>

        </div>

    </div>

</section>


<!-- EMPRESA Y SEGUIMIENTO -->

<section class="row g-4 mb-4">

    <div class="col-12 col-lg-6">

        <article class="content-card h-100">

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
                    <strong>{{ $practice->company?->official_name ?? 'Sin empresa asignada' }}</strong>
                </div>

                <div>
                    <span>Jefe inmediato</span>
                    <strong>{{ $practice->companyResponsible?->full_name ?? 'Sin responsable asignado' }}</strong>
                </div>

                <div>
                    <span>Cargo</span>
                    <strong>{{ $practice->companyResponsible?->position ?? 'Sin cargo registrado' }}</strong>
                </div>

                <div>
                    <span>Estado</span>

                    <strong class="success-text">
                        {{ $practice->company ? 'Empresa asignada' : 'Sin empresa asignada' }}
                    </strong>
                </div>

            </div>

        </article>

    </div>


    <div class="col-12 col-lg-6">

        <article class="content-card h-100">

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
                        {{ $practice->visits->sortByDesc('id')->first()?->effectiveStatus() ?? 'Sin visita registrada' }}
                    </h3>

                    <p>
                        La visita podrá realizarse a partir
                        del día 45 de la práctica profesional.
                    </p>

                    <span class="badge warning">
                        {{ $practice->reachedVisitDay() ? 'Umbral del día 45 alcanzado' : 'Consulta el estado de la práctica' }}
                    </span>

                </div>

            </div>

        </article>

    </div>

</section>


<!-- NOTA -->
@empty
    <section class="content-card p-3">No tienes practicantes relacionados con tu responsabilidad de empresa.</section>
@endforelse

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
