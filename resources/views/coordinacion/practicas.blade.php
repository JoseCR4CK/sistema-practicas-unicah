@extends('layouts.app')

@section('title', 'Prácticas | Coordinación Académica')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO GENERAL
        </span>

        <h1>
            Prácticas
        </h1>

        <p>
            Consulta el estado general de los procesos
            de práctica profesional actualmente registrados.
        </p>

    </div>

</div>



<section class="row g-3 mb-4">

    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-briefcase"></i>
            </div>

            <div>
                <span>Prácticas</span>
                <strong id="practiceStatTotal">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-play-circle"></i>
            </div>

            <div>
                <span>Activas</span>
                <strong id="practiceStatActive">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>
                <span>Visita habilitada</span>
                <strong id="practiceStatVisits">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>

            <div>
                <span>Requieren atención</span>
                <strong id="practiceStatAlerts">0</strong>
            </div>

        </div>

    </div>

</section>



<section class="content-card">

    <div class="coord-practice-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="practiceSearch"
                placeholder="Buscar estudiante, cuenta o empresa..."
                oninput="renderPractices()"
            >

        </div>


        <select
            id="practiceStage"
            class="form-select"
            onchange="renderPractices()"
        >

            <option value="">
                Todas las etapas
            </option>

            <option value="Documentación">
                Documentación
            </option>

            <option value="Práctica">
                Práctica
            </option>

            <option value="Visita">
                Visita
            </option>

            <option value="Monografía">
                Monografía
            </option>

            <option value="Defensa">
                Defensa
            </option>

        </select>


        <select
            id="practiceAttention"
            class="form-select"
            onchange="renderPractices()"
        >

            <option value="">
                Todos
            </option>

            <option value="attention">
                Requieren atención
            </option>

            <option value="normal">
                Sin alertas
            </option>

        </select>

    </div>


    <div id="practicesList"></div>

</section>

@endsection



@push('scripts')

<script src="{{ asset('js/practices-store.js') }}"></script>

<script>

function formatPracticeDate(date) {

    if (!date) {
        return '—';
    }


    return new Date(
        date + 'T12:00:00'
    ).toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

}


function calculatePracticeDay(start) {

    const startDate =
        new Date(start + 'T00:00:00');


    const today =
        new Date();


    today.setHours(0, 0, 0, 0);


    if (today < startDate) {
        return 0;
    }


    const milliseconds =
        today - startDate;


    return Math.floor(
        milliseconds / 86400000
    ) + 1;

}


function getPracticeAlert(practice) {

    const practiceStartedStages = [
        'Práctica',
        'Visita',
        'Monografía',
        'Defensa',
        'Finalización'
    ];

    if (!practiceStartedStages.includes(practice.etapa)) {
        return null;
    }

    const day =
        practiceStartedStages.includes(practice.etapa)
            ? calculatePracticeDay(practice.inicio)
            : null;


    if (
        day >= 45 &&
        practice.visita_estado === 'Pendiente'
    ) {

        return {
            level: 'warning',
            text: 'Visita habilitada'
        };

    }


    if (!practice.docente) {

        return {
            level: 'danger',
            text: 'Sin docente asignado'
        };

    }


    return null;

}


function renderPracticeStats(practices) {

    document.getElementById(
        'practiceStatTotal'
    ).textContent =
        practices.length;


    document.getElementById(
        'practiceStatActive'
    ).textContent =
        practices.filter(item =>
            item.estado === 'Activa'
        ).length;


    document.getElementById(
        'practiceStatVisits'
    ).textContent =
        practices.filter(item => {

            const day =
                calculatePracticeDay(
                    item.inicio
                );


            return (
                day >= 45 &&
                item.visita_estado === 'Pendiente'
            );

        }).length;


    document.getElementById(
        'practiceStatAlerts'
    ).textContent =
        practices.filter(item =>
            getPracticeAlert(item)
        ).length;

}


function renderPractices() {

    const practices =
        PracticeStore.getAll();


    renderPracticeStats(practices);


    const search =
        document
            .getElementById('practiceSearch')
            .value
            .trim()
            .toLowerCase();


    const stage =
        document.getElementById(
            'practiceStage'
        ).value;


    const attention =
        document.getElementById(
            'practiceAttention'
        ).value;


    const filtered =
        practices.filter(practice => {

            const searchable =
                `
                    ${practice.estudiante}
                    ${practice.cuenta}
                    ${practice.empresa}
                    ${practice.docente}
                `
                .toLowerCase();


            const matchesSearch =
                searchable.includes(search);


            const matchesStage =
                !stage ||
                practice.etapa === stage;


            const alert =
                getPracticeAlert(practice);


            let matchesAttention = true;


            if (attention === 'attention') {

                matchesAttention =
                    !!alert;

            }


            if (attention === 'normal') {

                matchesAttention =
                    !alert;

            }


            return (
                matchesSearch &&
                matchesStage &&
                matchesAttention
            );

        });


    const container =
        document.getElementById(
            'practicesList'
        );


    if (!filtered.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-briefcase"></i>

                <strong>
                    No se encontraron prácticas
                </strong>

                <span>
                    Cambia los criterios de búsqueda o filtrado.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(practice => {

            const initials =
                practice.estudiante
                    .split(' ')
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(word =>
                        word.charAt(0)
                    )
                    .join('')
                    .toUpperCase();


            const day =
                calculatePracticeDay(
                    practice.inicio
                );


            const alert =
                getPracticeAlert(
                    practice
                );


            return `

                <article class="coord-practice-row">

                    <div class="coord-request-avatar">
                        ${initials}
                    </div>


                    <div class="coord-practice-student">

                        <span>
                            ESTUDIANTE
                        </span>

                        <strong>
                            ${practice.estudiante}
                        </strong>

                        <small>
                            ${practice.cuenta}
                        </small>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Empresa
                        </span>

                        <strong class="coord-request-value">
                            ${practice.empresa}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Docente asignado
                        </span>

                        <strong class="coord-request-value">
                            ${practice.docente || 'Pendiente'}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Etapa
                        </span>

                        <strong class="coord-request-value">
                            ${practice.etapa}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Día
                        </span>

                        <strong class="coord-request-value">
                            ${day ?? '—'}
                        </strong>

                    </div>


                    <div class="coord-practice-progress">

                        <span>
                            Progreso
                        </span>

                        <strong>
                            ${practice.progreso}%
                        </strong>

                        <div>
                            <span
                                style="width:${practice.progreso}%"
                            ></span>
                        </div>

                    </div>


                    <div>

                        ${
                            alert
                                ? `
                                    <span class="coord-practice-alert ${alert.level}">
                                        ${alert.text}
                                    </span>
                                `
                                : `
                                    <span class="coord-practice-alert normal">
                                        Sin alertas
                                    </span>
                                `
                        }

                    </div>


                    <a
                        href="/coordinacion/practicas/${practice.id}"
                        class="coord-teacher-view"
                    >
                        <i class="bi bi-eye"></i>
                        Ver
                    </a>

                </article>

            `;

        }).join('');

}


renderPractices();

</script>

@endpush