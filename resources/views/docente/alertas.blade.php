@extends('layouts.teacher')

@section('title', 'Alertas | Portal del docente')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO
        </span>

        <h1>
            Alertas
        </h1>

        <p>
            Consulta las situaciones que requieren atención
            dentro de tus procesos de práctica profesional.
        </p>

    </div>

</div>



<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">

                <i class="bi bi-bell"></i>

            </div>

            <div>

                <span>
                    Alertas activas
                </span>

                <strong id="alertsTotal">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">

                <i class="bi bi-calendar-check"></i>

            </div>

            <div>

                <span>
                    Visitas
                </span>

                <strong id="alertsVisits">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">

                <i class="bi bi-clipboard-check"></i>

            </div>

            <div>

                <span>
                    Seguimientos
                </span>

                <strong id="alertsFollowups">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">

                <i class="bi bi-info-circle"></i>

            </div>

            <div>

                <span>
                    Informativas
                </span>

                <strong id="alertsInfo">
                    0
                </strong>

            </div>

        </div>

    </div>

</section>



<!-- FILTROS -->

<section class="content-card">

    <div class="teacher-alert-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="alertSearch"
                placeholder="Buscar estudiante o alerta..."
                oninput="renderAlerts()"
            >

        </div>


        <select
            id="alertTypeFilter"
            class="form-select"
            onchange="renderAlerts()"
        >

            <option value="">
                Todos los tipos
            </option>

            <option value="Visita">
                Visitas
            </option>

            <option value="Seguimiento">
                Seguimientos
            </option>

            <option value="Proceso">
                Proceso académico
            </option>

        </select>

    </div>


    <div id="teacherAlertsList"></div>

</section>


@endsection



@push('scripts')

<script src="{{ asset('js/practices-store.js') }}"></script>

<script src="{{ asset('js/visits-store.js') }}"></script>

<script src="{{ asset('js/followups-store.js') }}"></script>


<script>

const CURRENT_TEACHER =
    'Msc. Carlos Martínez';



/*
|--------------------------------------------------------------------------
| ID DE ESTUDIANTE
|--------------------------------------------------------------------------
*/

function getPracticeStudentId(practice) {

    return (
        practice.student_id ??
        practice.estudiante_id ??
        practice.id
    );

}



/*
|--------------------------------------------------------------------------
| PRÁCTICAS DEL DOCENTE
|--------------------------------------------------------------------------
*/

function getTeacherPractices() {

    if (!window.PracticeStore) {
        return [];
    }


    return PracticeStore
        .getAll()
        .filter(practice =>
            practice.docente === CURRENT_TEACHER
        );

}



/*
|--------------------------------------------------------------------------
| VISITAS DEL DOCENTE
|--------------------------------------------------------------------------
*/

function getTeacherVisits() {

    if (!window.VisitStore) {
        return [];
    }


    return VisitStore
        .getAll()
        .filter(visit =>
            visit.docente === CURRENT_TEACHER
        );

}



/*
|--------------------------------------------------------------------------
| SEGUIMIENTOS DEL DOCENTE
|--------------------------------------------------------------------------
*/

function getTeacherFollowups() {

    if (!window.FollowupStore) {
        return [];
    }


    return FollowupStore
        .getAll()
        .filter(item =>
            item.docente === CURRENT_TEACHER
        );

}



/*
|--------------------------------------------------------------------------
| DÍA DE PRÁCTICA
|--------------------------------------------------------------------------
*/

function calculateAlertPracticeDay(start) {

    if (!start) {
        return 0;
    }


    const startDate =
        new Date(
            start + 'T00:00:00'
        );


    const today =
        new Date();

    today.setHours(
        0,
        0,
        0,
        0
    );


    if (today < startDate) {
        return 0;
    }


    return Math.floor(
        (today - startDate) /
        86400000
    ) + 1;

}



/*
|--------------------------------------------------------------------------
| ESTADO EFECTIVO DE VISITA
|--------------------------------------------------------------------------
*/

function getAlertVisitStatus(visit) {

    if (visit.estado === 'Realizada') {
        return 'Realizada';
    }


    if (visit.estado === 'Programada') {
        return 'Programada';
    }


    if (visit.estado === 'Habilitada') {
        return 'Habilitada';
    }


    const day =
        calculateAlertPracticeDay(
            visit.inicio_practica
        );


    return day >= 45
        ? 'Habilitada'
        : 'No habilitada';

}



/*
|--------------------------------------------------------------------------
| CONSTRUIR ALERTAS
|--------------------------------------------------------------------------
*/

function buildTeacherAlerts() {

    const alerts = [];


    const practices =
        getTeacherPractices();


    const visits =
        getTeacherVisits();


    const followups =
        getTeacherFollowups();



    /*
    |--------------------------------------------------------------------------
    | VISITAS
    |--------------------------------------------------------------------------
    */

    visits.forEach(visit => {

        const status =
            getAlertVisitStatus(
                visit
            );


        const day =
            calculateAlertPracticeDay(
                visit.inicio_practica
            );


        if (status === 'Habilitada') {

            alerts.push({

                id:
                    `visit-${visit.id}`,

                student:
                    visit.estudiante,

                type:
                    'Visita',

                level:
                    'attention',

                icon:
                    'bi-calendar-check',

                title:
                    'Visita de seguimiento habilitada',

                description:
                    `${visit.estudiante} se encuentra en el día ${day} de práctica y ya cumple la condición para realizar la visita.`,

                action:
                    `/docente/visitas/${visit.id}`,

                actionText:
                    'Revisar visita'

            });

        }


        if (status === 'Programada') {

            alerts.push({

                id:
                    `scheduled-${visit.id}`,

                student:
                    visit.estudiante,

                type:
                    'Visita',

                level:
                    'info',

                icon:
                    'bi-calendar-event',

                title:
                    'Visita programada',

                description:
                    `${visit.estudiante} tiene una visita de seguimiento programada.`,

                action:
                    `/docente/visitas/${visit.id}`,

                actionText:
                    'Ver programación'

            });

        }

    });



    /*
    |--------------------------------------------------------------------------
    | SEGUIMIENTOS PENDIENTES
    |--------------------------------------------------------------------------
    */

    followups
        .filter(item =>
            item.estado === 'Pendiente'
        )
        .forEach(item => {

            alerts.push({

                id:
                    `followup-${item.id}`,

                student:
                    item.estudiante,

                type:
                    'Seguimiento',

                level:
                    'pending',

                icon:
                    'bi-clipboard-check',

                title:
                    item.titulo,

                description:
                    `${item.estudiante} tiene un seguimiento académico pendiente para el ${formatAlertDate(item.fecha)}.`,

                action:
                    '/docente/seguimientos',

                actionText:
                    'Ver seguimiento'

            });

        });



    /*
    |--------------------------------------------------------------------------
    | ETAPAS QUE REQUIEREN SEGUIMIENTO
    |--------------------------------------------------------------------------
    */

    practices.forEach(practice => {

        if (
            practice.etapa ===
            'Monografía'
        ) {

            alerts.push({

                id:
                    `process-${getPracticeStudentId(practice)}`,

                student:
                    practice.estudiante,

                type:
                    'Proceso',

                level:
                    'info',

                icon:
                    'bi-journal-text',

                title:
                    'Monografía en seguimiento',

                description:
                    `${practice.estudiante} se encuentra actualmente en la etapa de monografía.`,

                action:
                    `/docente/practicantes/${getPracticeStudentId(practice)}`,

                actionText:
                    'Ver practicante'

            });

        }

    });


    return alerts;

}



/*
|--------------------------------------------------------------------------
| FECHA
|--------------------------------------------------------------------------
*/

function formatAlertDate(date) {

    if (!date) {
        return 'fecha no registrada';
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



/*
|--------------------------------------------------------------------------
| ESTADÍSTICAS
|--------------------------------------------------------------------------
*/

function renderAlertStats(alerts) {

    document.getElementById(
        'alertsTotal'
    ).textContent =
        alerts.length;


    document.getElementById(
        'alertsVisits'
    ).textContent =
        alerts.filter(alert =>
            alert.type === 'Visita'
        ).length;


    document.getElementById(
        'alertsFollowups'
    ).textContent =
        alerts.filter(alert =>
            alert.type === 'Seguimiento'
        ).length;


    document.getElementById(
        'alertsInfo'
    ).textContent =
        alerts.filter(alert =>
            alert.level === 'info'
        ).length;

}



/*
|--------------------------------------------------------------------------
| RENDER
|--------------------------------------------------------------------------
*/

function renderAlerts() {

    const alerts =
        buildTeacherAlerts();


    renderAlertStats(
        alerts
    );


    const search =
        document
            .getElementById(
                'alertSearch'
            )
            .value
            .trim()
            .toLowerCase();


    const type =
        document.getElementById(
            'alertTypeFilter'
        ).value;


    const filtered =
        alerts.filter(alert => {

            const text =
                `
                    ${alert.student}
                    ${alert.title}
                    ${alert.description}
                `
                .toLowerCase();


            return (
                text.includes(search) &&
                (
                    !type ||
                    alert.type === type
                )
            );

        });


    const container =
        document.getElementById(
            'teacherAlertsList'
        );


    if (!filtered.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-check-circle"></i>

                <strong>
                    No hay alertas pendientes
                </strong>

                <span>
                    No existen situaciones que coincidan
                    con los filtros seleccionados.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(alert => `

            <article
                class="teacher-alert-row ${alert.level}"
            >

                <div class="teacher-alert-icon">

                    <i class="bi ${alert.icon}"></i>

                </div>


                <div class="teacher-alert-content">

                    <div class="teacher-alert-meta">

                        <span>
                            ${alert.type}
                        </span>

                        <small>
                            ${alert.student}
                        </small>

                    </div>


                    <strong>
                        ${alert.title}
                    </strong>


                    <p>
                        ${alert.description}
                    </p>

                </div>


                <a
                    href="${alert.action}"
                    class="coord-teacher-view"
                >

                    ${alert.actionText}

                    <i class="bi bi-chevron-right"></i>

                </a>

            </article>

        `).join('');

}


renderAlerts();

</script>

@endpush