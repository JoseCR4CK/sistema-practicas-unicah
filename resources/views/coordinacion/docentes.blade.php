@extends('layouts.app')

@section('title', 'Docentes de seguimiento | Coordinación Académica')

@section('content')


<!-- CABECERA -->

<div class="page-header">

    <div>

        <h1>
            Docentes de seguimiento
        </h1>

        <p>
            Consulta los docentes asignados al seguimiento
            de prácticas profesionales.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-calendar3"></i>

        Asignaciones registradas

    </div>

</div>



<!-- INDICADORES -->

<section class="row g-3 mb-4">


    <div class="col-12 col-md-6 col-xl-3">

        <div class="coord-teacher-stat h-100">

            <div class="coord-teacher-stat-icon blue">

                <i class="bi bi-person-video3"></i>

            </div>

            <div>

                <span>
                    Docentes asignados
                </span>

                <strong>
                    {{ $docentesAsignados }}
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="coord-teacher-stat h-100">

            <div class="coord-teacher-stat-icon green">

                <i class="bi bi-people"></i>

            </div>

            <div>

                <span>
                    Practicantes asignados
                </span>

                <strong>
                    {{ $practicantes }}
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="coord-teacher-stat h-100">

            <div class="coord-teacher-stat-icon gold">

                <i class="bi bi-calendar-check"></i>

            </div>

            <div>

                <span>
                    Visitas por atender
                </span>

                <strong>
                    {{ $visitasPorAtender }}
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="coord-teacher-stat h-100">

            <div class="coord-teacher-stat-icon red">

                <i class="bi bi-exclamation-circle"></i>

            </div>

            <div>

                <span>
                    Con alertas
                </span>

                <strong>
                    {{ $docentesConAlertas }}
                </strong>

            </div>

        </div>

    </div>


</section>



<!-- BUSCADOR Y FILTROS -->

<section class="coord-teacher-toolbar">

    <div class="coord-teacher-search">

        <i class="bi bi-search"></i>

        <input
            type="text"
            id="teacherSearch"
            placeholder="Buscar docente..."
            oninput="filterTeachers()"
        >

    </div>


    <select
        id="teacherFaculty"
        onchange="filterTeachers()"
    >
        <option value="all">Todas las facultades</option>
        @foreach ($docentes->pluck('faculty')->filter()->unique()->sort() as $facultad)
            <option>{{ $facultad }}</option>
        @endforeach

    </select>


    <select
        id="teacherStatus"
        onchange="filterTeachers()"
    >

        <option value="all">
            Todos los estados
        </option>

        <option value="ACTIVE">
            Activos
        </option>

        <option value="INACTIVE">
            Inactivos
        </option>

    </select>

</section>



<!-- LISTADO -->

<section class="content-card">

    <div class="card-header">

        <div>

            <h2>
                Docentes de seguimiento
            </h2>

            <p>
                Carga actual y estado de los docentes asignados.
            </p>

        </div>

    </div>


    <div class="coord-teacher-list">
        @foreach ($docentes as $docente)
            <article class="coord-teacher-row"
                data-name="{{ Str::lower($docente->user->name) }}"
                data-faculty="{{ $docente->faculty }}"
                data-status="{{ $docente->user->status }}">
                <div class="coord-teacher-avatar">{{ Str::initials($docente->user->name, true) }}</div>
                <div class="coord-teacher-info">
                    <span>DOCENTE DE SEGUIMIENTO</span>
                    <strong>{{ $docente->user->name }}</strong>
                    <small>{{ $docente->faculty ?? 'Sin facultad registrada' }}</small>
                </div>
                <div class="coord-teacher-data">
                    <span>Practicantes</span>
                    <strong>{{ $docente->students_count }}</strong>
                </div>
                <div class="coord-teacher-data">
                    <span>Visitas por atender</span>
                    <strong>{{ $docente->upcoming_visits_count }}</strong>
                </div>
                <div class="coord-teacher-data">
                    <span>Prácticas activas</span>
                    <strong>{{ $docente->active_practices_count }}</strong>
                </div>
                <div>
                    <span class="badge {{ $docente->user->isActive() ? 'success' : 'warning' }}">
                        <i class="bi bi-check-circle"></i>
                        {{ ['ACTIVE' => 'Activo', 'INACTIVE' => 'Inactivo'][$docente->user->status] ?? $docente->user->status }}
                    </span>
                </div>
                <div class="coord-teacher-action">
                    <a href="{{ route('coordinacion.docente', $docente) }}" class="coord-teacher-view">
                        <i class="bi bi-eye"></i> Ver docente
                    </a>
                </div>
            </article>
        @endforeach
    </div>

    <div
        id="teachersEmpty"
        class="coord-teacher-empty"
        style="display:{{ $docentes->isEmpty() ? 'flex' : 'none' }};"
    >

        <i class="bi bi-search"></i>

        <strong>
            No se encontraron docentes
        </strong>

        <span>
            Intenta modificar los filtros de búsqueda.
        </span>

    </div>

</section>


@endsection



@push('scripts')

<script>

function filterTeachers() {

    const search =
        document.getElementById(
            'teacherSearch'
        )
        .value
        .toLowerCase()
        .trim();


    const faculty =
        document.getElementById(
            'teacherFaculty'
        ).value;


    const status =
        document.getElementById(
            'teacherStatus'
        ).value;


    const rows =
        document.querySelectorAll(
            '.coord-teacher-row'
        );


    let visible =
        0;


    rows.forEach(row => {

        const name =
            row.dataset.name;

        const rowFaculty =
            row.dataset.faculty;

        const rowStatus =
            row.dataset.status;


        const matchesSearch =
            name.includes(search);


        const matchesFaculty =
            faculty === 'all'
            ||
            faculty === rowFaculty;


        const matchesStatus =
            status === 'all'
            ||
            status === rowStatus;


        const show =
            matchesSearch
            &&
            matchesFaculty
            &&
            matchesStatus;


        row.style.display =
            show
            ?
            'grid'
            :
            'none';


        if (show) {
            visible++;
        }

    });


    document.getElementById(
        'teachersEmpty'
    ).style.display =
        visible === 0
        ?
        'flex'
        :
        'none';

}

</script>

@endpush
