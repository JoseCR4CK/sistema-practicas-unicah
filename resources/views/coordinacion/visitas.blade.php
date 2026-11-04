@extends('layouts.app')

@section('title', 'Visitas | Coordinación Académica')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO DE VISITAS
        </span>

        <h1>
            Visitas
        </h1>

        <p>
            Consulta la habilitación y estado de las visitas
            correspondientes a las prácticas profesionales.
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
                <span>Total</span>
                <strong id="visitStatTotal">0</strong>
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
                <strong id="visitStatEnabled">0</strong>
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
                <strong id="visitStatScheduled">0</strong>
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
                <strong id="visitStatCompleted">0</strong>
            </div>

        </div>

    </div>

</section>


<section class="content-card">

    <div class="coord-visit-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="visitSearch"
                placeholder="Buscar estudiante, empresa o docente..."
                oninput="renderVisits()"
            >

        </div>


        <select
            id="visitStatus"
            class="form-select"
            onchange="renderVisits()"
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


    <div id="visitsList"></div>

</section>

@endsection


@push('scripts')

<script src="{{ asset('js/visits-store.js') }}"></script>

<script>

function calculateVisitDay(start) {

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


function calculateEnableDate(start) {

    const date =
        new Date(start + 'T00:00:00');

    date.setDate(
        date.getDate() + 44
    );


    return date.toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

}


function getVisitStatusClass(status) {

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

function getEffectiveVisitStatus(visit) {

    if (visit.estado === 'Realizada') {
        return 'Realizada';
    }

    if (visit.estado === 'Programada') {
        return 'Programada';
    }

    const day =
        calculateVisitDay(
            visit.inicio_practica
        );

    return day >= 45
        ? 'Habilitada'
        : 'No habilitada';
}

function renderVisitStats(visits) {

    document.getElementById(
        'visitStatTotal'
    ).textContent =
        visits.length;


    document.getElementById(
        'visitStatEnabled'
    ).textContent =
        visits.filter(item =>
            getEffectiveVisitStatus(item) === 'Habilitada'
        ).length;

    
    document.getElementById(
        'visitStatScheduled'
    ).textContent =
        visits.filter(item =>
            getEffectiveVisitStatus(item) === 'Programada'
        ).length;


    document.getElementById(
        'visitStatCompleted'
    ).textContent =
        visits.filter(item =>
            item.estado === 'Realizada'
        ).length;

}


function renderVisits() {

    const visits =
        VisitStore.getAll();


    renderVisitStats(visits);

    const effectiveStatus =
    getEffectiveVisitStatus(visit);

    const search =
        document
            .getElementById('visitSearch')
            .value
            .trim()
            .toLowerCase();


    const status =
        document.getElementById(
            'visitStatus'
        ).value;


    const filtered =
        visits.filter(visit => {

            const text =
                `
                    ${visit.estudiante}
                    ${visit.cuenta}
                    ${visit.empresa}
                    ${visit.docente}
                `
                .toLowerCase();


            return (
                text.includes(search) &&
                (!status ||
                    getEffectiveVisitStatus(visit) === status)
            );

        });


    const container =
        document.getElementById(
            'visitsList'
        );


    if (!filtered.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-calendar-check"></i>

                <strong>
                    No se encontraron visitas
                </strong>

                <span>
                    Cambia los criterios de búsqueda.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(visit => {

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


            const day =
                calculateVisitDay(
                    visit.inicio_practica
                );


            return `

                <article class="coord-visit-row">

                    <div class="coord-request-avatar">
                        ${initials}
                    </div>


                    <div class="coord-visit-student">

                        <span>ESTUDIANTE</span>

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
                            Docente
                        </span>

                        <strong class="coord-request-value">
                            ${visit.docente}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Día actual
                        </span>

                        <strong class="coord-request-value">
                            ${day}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Habilitada desde
                        </span>

                        <strong class="coord-request-value">
                            ${calculateEnableDate(
                                visit.inicio_practica
                            )}
                        </strong>

                    </div>


                    <span
                        class="coord-visit-status ${getVisitStatusClass(effectiveStatus)}"
                    >
                        ${effectiveStatus}
                    </span>


                    <a
                        href="/coordinacion/visitas/${visit.id}"
                        class="coord-teacher-view"
                    >
                        <i class="bi bi-eye"></i>
                        Ver
                    </a>

                </article>

            `;

        }).join('');

}


renderVisits();

</script>

@endpush