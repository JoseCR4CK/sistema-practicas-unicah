@extends('layouts.teacher')

@section('title', 'Visitas | Portal del docente')

@section('content')

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO
        </span>

        <h1>
            Visitas
        </h1>

        <p>
            Consulta las visitas correspondientes
            a tus practicantes asignados.
        </p>

    </div>

</div>


<section class="row g-3 mb-4">

    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-calendar3"></i>
            </div>

            <div>
                <span>Mis visitas</span>
                <strong id="teacherVisitTotal">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-unlock"></i>
            </div>

            <div>
                <span>Habilitadas</span>
                <strong id="teacherVisitEnabled">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-calendar-event"></i>
            </div>

            <div>
                <span>Programadas</span>
                <strong id="teacherVisitScheduled">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Realizadas</span>
                <strong id="teacherVisitCompleted">0</strong>
            </div>

        </div>

    </div>

</section>


<section class="content-card">

    <div class="teacher-visit-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="teacherVisitSearch"
                placeholder="Buscar practicante o empresa..."
                oninput="renderTeacherVisits()"
            >

        </div>


        <select
            id="teacherVisitStatus"
            class="form-select"
            onchange="renderTeacherVisits()"
        >

            <option value="">
                Todos los estados
            </option>

            <option value="No habilitada">
                No habilitadas
            </option>

            <option value="Habilitada">
                Habilitadas
            </option>

            <option value="Programada">
                Programadas
            </option>

            <option value="Realizada">
                Realizadas
            </option>

        </select>

    </div>


    <div id="teacherVisitsList"></div>

</section>

@endsection


@push('scripts')

<script src="{{ asset('js/visits-store.js') }}"></script>

<script>

const CURRENT_TEACHER =
    'Msc. Carlos Martínez';


function calculateTeacherVisitDay(start) {

    const startDate =
        new Date(start + 'T00:00:00');

    const today =
        new Date();

    today.setHours(0, 0, 0, 0);


    if (today < startDate) {
        return 0;
    }


    return Math.floor(
        (today - startDate) / 86400000
    ) + 1;

}


function getTeacherVisitStatus(visit) {

    if (visit.estado === 'Realizada') {
        return 'Realizada';
    }

    if (visit.estado === 'Programada') {
        return 'Programada';
    }


    const day =
        calculateTeacherVisitDay(
            visit.inicio_practica
        );


    return day >= 45
        ? 'Habilitada'
        : 'No habilitada';

}


function getTeacherVisitStatusClass(status) {

    if (status === 'Realizada') {
        return 'completed';
    }

    if (status === 'Programada') {
        return 'scheduled';
    }

    if (status === 'Habilitada') {
        return 'enabled';
    }

    return 'disabled';

}


function getMyVisits() {

    return VisitStore
        .getAll()
        .filter(visit =>
            visit.docente === CURRENT_TEACHER
        );

}


function renderTeacherVisitStats(visits) {

    document.getElementById(
        'teacherVisitTotal'
    ).textContent =
        visits.length;


    document.getElementById(
        'teacherVisitEnabled'
    ).textContent =
        visits.filter(visit =>
            getTeacherVisitStatus(visit) ===
            'Habilitada'
        ).length;


    document.getElementById(
        'teacherVisitScheduled'
    ).textContent =
        visits.filter(visit =>
            getTeacherVisitStatus(visit) ===
            'Programada'
        ).length;


    document.getElementById(
        'teacherVisitCompleted'
    ).textContent =
        visits.filter(visit =>
            getTeacherVisitStatus(visit) ===
            'Realizada'
        ).length;

}


function renderTeacherVisits() {

    const visits =
        getMyVisits();


    renderTeacherVisitStats(visits);


    const search =
        document
            .getElementById(
                'teacherVisitSearch'
            )
            .value
            .trim()
            .toLowerCase();


    const status =
        document.getElementById(
            'teacherVisitStatus'
        ).value;


    const filtered =
        visits.filter(visit => {

            const text =
                `
                    ${visit.estudiante}
                    ${visit.cuenta}
                    ${visit.empresa}
                `
                .toLowerCase();


            const effectiveStatus =
                getTeacherVisitStatus(
                    visit
                );


            return (
                text.includes(search) &&
                (
                    !status ||
                    effectiveStatus === status
                )
            );

        });


    const container =
        document.getElementById(
            'teacherVisitsList'
        );


    if (!filtered.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-calendar-check"></i>

                <strong>
                    No se encontraron visitas
                </strong>

                <span>
                    No tienes visitas que coincidan
                    con los filtros seleccionados.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(visit => {

            const status =
                getTeacherVisitStatus(
                    visit
                );


            const day =
                calculateTeacherVisitDay(
                    visit.inicio_practica
                );


            const initials =
                visit.estudiante
                    .split(' ')
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(word =>
                        word.charAt(0)
                    )
                    .join('')
                    .toUpperCase();


            return `

                <article class="teacher-visit-row">

                    <div class="coord-request-avatar">
                        ${initials}
                    </div>


                    <div class="teacher-visit-student">

                        <span>
                            PRACTICANTE
                        </span>

                        <strong>
                            ${visit.estudiante}
                        </strong>

                        <small>
                            ${visit.cuenta}
                        </small>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Empresa
                        </span>

                        <strong class="coord-request-value">
                            ${visit.empresa}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Responsable
                        </span>

                        <strong class="coord-request-value">
                            ${visit.responsable}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Día de práctica
                        </span>

                        <strong class="coord-request-value">
                            ${day}
                        </strong>

                    </div>


                    <span
                        class="coord-visit-status ${getTeacherVisitStatusClass(status)}"
                    >
                        ${status}
                    </span>


                    <a
                        href="/docente/visitas/${visit.id}"
                        class="coord-teacher-view"
                    >
                        <i class="bi bi-eye"></i>
                        Revisar
                    </a>

                </article>

            `;

        }).join('');

}


renderTeacherVisits();

</script>

@endpush