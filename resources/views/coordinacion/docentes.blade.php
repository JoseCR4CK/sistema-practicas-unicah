@extends('layouts.app')

@section('title', 'Docentes | Coordinación Académica')

@section('content')


<!-- CABECERA -->

<div class="page-header">

    <div>

        <h1>
            Docentes
        </h1>

        <p>
            Consulta los docentes asignados al seguimiento
            de prácticas profesionales.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-calendar3"></i>

        III Período 2026

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
                    12
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
                    38
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
                    Visitas próximas
                </span>

                <strong>
                    9
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
                    3
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

        <option value="all">
            Todas las facultades
        </option>

        <option value="Ingeniería">
            Ingeniería
        </option>

        <option value="Administración">
            Administración
        </option>

    </select>


    <select
        id="teacherStatus"
        onchange="filterTeachers()"
    >

        <option value="all">
            Todos los estados
        </option>

        <option value="Activo">
            Activos
        </option>

        <option value="Atención">
            Requieren atención
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


        <!-- DOCENTE 1 -->

        <article
            class="coord-teacher-row"
            data-name="carlos martinez"
            data-faculty="Ingeniería"
            data-status="Activo"
        >

            <div class="coord-teacher-avatar">
                CM
            </div>


            <div class="coord-teacher-info">

                <span>
                    DOCENTE
                </span>

                <strong>
                    Msc. Carlos Martínez
                </strong>

                <small>
                    Facultad de Ingeniería
                </small>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Practicantes
                </span>

                <strong>
                    4
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Visitas próximas
                </span>

                <strong>
                    2
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Seguimientos
                </span>

                <strong>
                    4 activos
                </strong>

            </div>


            <div>

                <span class="badge success">

                    <i class="bi bi-check-circle"></i>

                    Activo

                </span>

            </div>


            <button
                type="button"
                class="coord-teacher-action"
            >

                <a
                    href="{{ route('coordinacion.docente', 1) }}"
                    class="coord-teacher-view"
                >
                    <i class="bi bi-eye"></i>
                    Ver docente
                </a>

            </button>

        </article>



        <!-- DOCENTE 2 -->

        <article
            class="coord-teacher-row"
            data-name="ana rodriguez"
            data-faculty="Ingeniería"
            data-status="Atención"
        >

            <div class="coord-teacher-avatar">
                AR
            </div>


            <div class="coord-teacher-info">

                <span>
                    DOCENTE
                </span>

                <strong>
                    Ing. Ana Rodríguez
                </strong>

                <small>
                    Facultad de Ingeniería
                </small>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Practicantes
                </span>

                <strong>
                    5
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Visitas próximas
                </span>

                <strong>
                    3
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Seguimientos
                </span>

                <strong>
                    5 activos
                </strong>

            </div>


            <div>

                <span class="badge warning">

                    <i class="bi bi-exclamation-circle"></i>

                    Atención

                </span>

            </div>


            <button
                type="button"
                class="coord-teacher-action"
            >

                <a
                    href="{{ route('coordinacion.docente', 2) }}"
                    class="coord-teacher-view"
                >
                    <i class="bi bi-eye"></i>
                    Ver docente
                </a>

            </button>

        </article>



        <!-- DOCENTE 3 -->

        <article
            class="coord-teacher-row"
            data-name="jose lopez"
            data-faculty="Administración"
            data-status="Activo"
        >

            <div class="coord-teacher-avatar">
                JL
            </div>


            <div class="coord-teacher-info">

                <span>
                    DOCENTE
                </span>

                <strong>
                    Msc. José López
                </strong>

                <small>
                    Facultad de Administración
                </small>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Practicantes
                </span>

                <strong>
                    3
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Visitas próximas
                </span>

                <strong>
                    1
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Seguimientos
                </span>

                <strong>
                    3 activos
                </strong>

            </div>


            <div>

                <span class="badge success">

                    <i class="bi bi-check-circle"></i>

                    Activo

                </span>

            </div>


            <button
                type="button"
                class="coord-teacher-action"
            >

                <a
                    href="{{ route('coordinacion.docente', 3) }}"
                    class="coord-teacher-view"
                >
                    <i class="bi bi-eye"></i>
                    Ver docente
                </a>

            </button>

        </article>



        <!-- DOCENTE 4 -->

        <article
            class="coord-teacher-row"
            data-name="maria fernandez"
            data-faculty="Ingeniería"
            data-status="Atención"
        >

            <div class="coord-teacher-avatar">
                MF
            </div>


            <div class="coord-teacher-info">

                <span>
                    DOCENTE
                </span>

                <strong>
                    Ing. María Fernández
                </strong>

                <small>
                    Facultad de Ingeniería
                </small>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Practicantes
                </span>

                <strong>
                    6
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Visitas próximas
                </span>

                <strong>
                    3
                </strong>

            </div>


            <div class="coord-teacher-data">

                <span>
                    Seguimientos
                </span>

                <strong>
                    6 activos
                </strong>

            </div>


            <div>

                <span class="badge warning">

                    <i class="bi bi-exclamation-circle"></i>

                    Atención

                </span>

            </div>


            <button
                type="button"
                class="coord-teacher-action"
            >

                <a
                    href="{{ route('coordinacion.docente', 4) }}"
                    class="coord-teacher-view"
                >
                    <i class="bi bi-eye"></i>
                    Ver docente
                </a>

            </button>

        </article>


    </div>


    <div
        id="teachersEmpty"
        class="coord-teacher-empty"
        style="display:none;"
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