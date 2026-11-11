@extends('layouts.teacher')

@section('title', 'Panel del docente | UNICAH')

@section('content')


<!-- CABECERA -->

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            DOCENTE DE SEGUIMIENTO
        </span>

        <h1>
            Panel del docente
        </h1>

        <p>
            Consulta tus practicantes asignados y las actividades
            que requieren seguimiento.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-calendar3"></i>

        III Período 2026

    </div>

</div>



<!-- BIENVENIDA -->

<section class="teacher-dashboard-welcome">

    <div>

        <span class="section-label">
            BIENVENIDO
        </span>

        <h2>
            Msc. Carlos Martínez
        </h2>

        <p>
            Actualmente tienes estudiantes asignados
            para seguimiento de práctica profesional.
        </p>

    </div>


    <div class="teacher-dashboard-welcome-icon">

        <i class="bi bi-person-video3"></i>

    </div>

</section>



<!-- INDICADORES -->

<section class="row g-3 mb-4">


    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-dashboard-stat h-100">

            <div class="teacher-dashboard-stat-icon blue">

                <i class="bi bi-people"></i>

            </div>

            <div>

                <span>
                    Practicantes asignados
                </span>

                <strong id="dashboardAssignedStudents">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-dashboard-stat h-100">

            <div class="teacher-dashboard-stat-icon green">

                <i class="bi bi-briefcase"></i>

            </div>

            <div>

                <span>
                    Prácticas activas
                </span>

                <strong id="dashboardActivePractices">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-dashboard-stat h-100">

            <div class="teacher-dashboard-stat-icon gold">

                <i class="bi bi-calendar-check"></i>

            </div>

            <div>

                <span>
                    Visitas por atender
                </span>

                <strong id="dashboardPendingVisits">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-6 col-xl-3">

        <div class="teacher-dashboard-stat h-100">

            <div class="teacher-dashboard-stat-icon red">

                <i class="bi bi-exclamation-circle"></i>

            </div>

            <div>

                <span>
                    Alertas activas
                </span>

                <strong id="dashboardActiveAlerts">
                    0
                </strong>

            </div>

        </div>

    </div>


</section>



<!-- ALERTA -->

<!-- PRÓXIMA ACTIVIDAD -->

<section
    class="teacher-dashboard-alert"
    id="dashboardNextActivity"
>
</section>


<!-- PRACTICANTES -->

<section class="content-card">


    <div class="card-header">

        <div>

            <h2>
                Mis practicantes
            </h2>

            <p>
                Estudiantes asignados para seguimiento académico.
            </p>

        </div>


        <a href="{{ route('docente.practicantes') }}">
            Ver todos
            <i class="bi bi-arrow-right"></i>
        </a>

    </div>

    <div
        class="teacher-students-list"
        id="dashboardStudentsList"
    ></div>

</section>



<!-- PRÓXIMOS SEGUIMIENTOS -->

<section class="row g-4 mt-1">


    <div class="col-12 col-lg-6">

        <div class="content-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Visitas por atender
                    </h2>

                    <p>
                        Visitas habilitadas o programadas que requieren seguimiento.
                    </p>

                </div>

            </div>

            <div
                class="teacher-activity-list"
                id="dashboardVisitsList"
            ></div>

        </div>

    </div>



    <div class="col-12 col-lg-6">

        <div class="content-card h-100">

            <div class="card-header">

                <div>

                    <h2>
                        Actividad y seguimiento
                    </h2>

                    <p>
                        Situaciones actuales relacionadas con tus practicantes.
                    </p>

                </div>

            </div>

            <div
                class="teacher-attention-list"
                id="dashboardAttentionList"
            ></div>

        </div>

    </div>


</section>

@endsection

@push('scripts')


<script>
const teacherPractices = {{ Illuminate\Support\Js::from($practiceData) }};
const teacherVisits = {{ Illuminate\Support\Js::from($visitData) }};
</script>
<script src="{{ asset('js/followups-store.js') }}"></script>

<script>

const CURRENT_TEACHER_NAME = {{ Illuminate\Support\Js::from(e(auth()->user()->name)) }};


function dashboardStudentId(practice) {

    return (
        practice.student_id ??
        practice.estudiante_id ??
        practice.id
    );

}


function dashboardCalculateDay(start) {

    if (!start) {
        return 0;
    }

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


function dashboardVisitStatus(visit) {

    if (!visit) {
        return 'Sin registro';
    }

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
        dashboardCalculateDay(
            visit.inicio_practica
        );

    return day >= 45
        ? 'Habilitada'
        : 'No habilitada';

}

function renderDashboardNextActivity(
    practices,
    visits,
    followups
) {

    const container =
        document.getElementById(
            'dashboardNextActivity'
        );


    if (!container) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | PRIORIDAD 1: VISITA HABILITADA O PROGRAMADA
    |--------------------------------------------------------------------------
    */

    const visit =
        visits.find(item => {

            const status =
                dashboardVisitStatus(item);

            return (
                status === 'Habilitada' ||
                status === 'Programada'
            );

        });


    if (visit) {

        const status =
            dashboardVisitStatus(visit);


        if (status === 'Programada') {

            container.innerHTML = `

                <div class="teacher-dashboard-alert-icon">

                    <i class="bi bi-calendar-check"></i>

                </div>


                <div>

                    <span>
                        PRÓXIMA ACTIVIDAD
                    </span>

                    <strong>
                        ${visit.estudiante} tiene una visita programada
                    </strong>

                    <p>
                        La visita de seguimiento ya cuenta
                        con una programación registrada.
                    </p>

                </div>


                <a
                    href="/docente/visitas/${visit.id}"
                    class="teacher-alert-action"
                >
                    Revisar
                </a>

            `;

        } else {

            container.innerHTML = `

                <div class="teacher-dashboard-alert-icon">

                    <i class="bi bi-calendar-check"></i>

                </div>


                <div>

                    <span>
                        PRÓXIMA ACTIVIDAD
                    </span>

                    <strong>
                        ${visit.estudiante} tiene la visita habilitada
                    </strong>

                    <p>
                        El practicante ya cumple la condición
                        para continuar con la visita de seguimiento.
                    </p>

                </div>


                <a
                    href="/docente/visitas/${visit.id}"
                    class="teacher-alert-action"
                >
                    Revisar
                </a>

            `;

        }

        return;

    }



    /*
    |--------------------------------------------------------------------------
    | PRIORIDAD 2: SEGUIMIENTO PENDIENTE
    |--------------------------------------------------------------------------
    */

    const followup =
        followups.find(item =>
            item.estado === 'Pendiente'
        );


    if (followup) {

        container.innerHTML = `

            <div class="teacher-dashboard-alert-icon">

                <i class="bi bi-clipboard-check"></i>

            </div>


            <div>

                <span>
                    PRÓXIMA ACTIVIDAD
                </span>

                <strong>
                    ${followup.titulo}
                </strong>

                <p>
                    ${followup.estudiante}
                    tiene un seguimiento académico pendiente.
                </p>

            </div>


            <a
                href="/docente/seguimientos"
                class="teacher-alert-action"
            >
                Revisar
            </a>

        `;

        return;

    }



    /*
    |--------------------------------------------------------------------------
    | PRIORIDAD 3: MONOGRAFÍA
    |--------------------------------------------------------------------------
    */

    const monograph =
        practices.find(item =>
            item.etapa === 'Monografía'
        );


    if (monograph) {

        container.innerHTML = `

            <div class="teacher-dashboard-alert-icon">

                <i class="bi bi-journal-text"></i>

            </div>


            <div>

                <span>
                    PRÓXIMA ACTIVIDAD
                </span>

                <strong>
                    Seguimiento de monografía
                </strong>

                <p>
                    ${monograph.estudiante}
                    se encuentra actualmente en la etapa
                    de monografía.
                </p>

            </div>


            <a
                href="/docente/practicantes/${dashboardStudentId(monograph)}"
                class="teacher-alert-action"
            >
                Revisar
            </a>

        `;

        return;

    }



    /*
    |--------------------------------------------------------------------------
    | SIN ACTIVIDADES
    |--------------------------------------------------------------------------
    */

    container.innerHTML = `

        <div class="teacher-dashboard-alert-icon">

            <i class="bi bi-check-circle"></i>

        </div>


        <div>

            <span>
                ESTADO ACTUAL
            </span>

            <strong>
                Sin actividades pendientes
            </strong>

            <p>
                No existen actividades inmediatas
                que requieran atención.
            </p>

        </div>

    `;

}



function loadTeacherDashboard() {

    const practices =
        teacherPractices;


    const visits =
        teacherVisits;


    const followups =
        FollowupStore
            .getAll()
            .filter(item =>
                item.docente === CURRENT_TEACHER_NAME
            );


    const activePractices =
        practices.filter(item =>
            Number(item.progreso) < 100 &&
            item.etapa !== 'Finalización'
        );


    const pendingVisits =
        visits.filter(item => {

            const status =
                dashboardVisitStatus(item);

            return (
                status === 'Habilitada' ||
                status === 'Programada'
            );

        });


    const pendingFollowups =
        followups.filter(item =>
            item.estado === 'Pendiente'
        );


    const processAlerts =
        practices.filter(item =>
            item.etapa === 'Monografía'
        );


    const totalAlerts =
        pendingVisits.length +
        pendingFollowups.length +
        processAlerts.length;


    document.getElementById(
        'dashboardAssignedStudents'
    ).textContent =
        practices.length;


    document.getElementById(
        'dashboardActivePractices'
    ).textContent =
        activePractices.length;


    document.getElementById(
        'dashboardPendingVisits'
    ).textContent =
        pendingVisits.length;


    document.getElementById(
        'dashboardActiveAlerts'
    ).textContent =
        totalAlerts;


    renderDashboardStudents(
        practices,
        visits
    );


    renderDashboardVisits(
        pendingVisits
    );


    renderDashboardAttention(
        pendingVisits,
        pendingFollowups,
        processAlerts
    );

    renderDashboardStudents(
    practices,
    visits
    );

    renderDashboardVisits(
        pendingVisits
    );

    renderDashboardAttention(
        pendingVisits,
        pendingFollowups,
        processAlerts
    );

    renderDashboardNextActivity(
        practices,
        visits,
        followups
    );

}


function renderDashboardStudents(
    practices,
    visits
) {

    const container =
        document.getElementById(
            'dashboardStudentsList'
        );


    container.innerHTML =
        practices.map(practice => {

            const studentId =
                dashboardStudentId(practice);


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


            return `

                <article class="teacher-student-row">

                    <div class="teacher-student-avatar">
                        ${initials}
                    </div>


                    <div class="teacher-student-info">

                        <span>
                            ESTUDIANTE
                        </span>

                        <strong>
                            ${practice.estudiante}
                        </strong>

                        <small>
                            ${practice.carrera ?? ''}
                        </small>

                    </div>


                    <div class="teacher-student-company">

                        <span>
                            Empresa
                        </span>

                        <strong>
                            ${practice.empresa}
                        </strong>

                    </div>


                    <div class="teacher-student-stage">

                        <span>
                            Etapa actual
                        </span>

                        <strong>
                            ${practice.etapa}
                        </strong>

                    </div>


                    <div class="teacher-student-progress">

                        <span>
                            Progreso
                        </span>

                        <strong>
                            ${practice.progreso}%
                        </strong>

                        <div>

                            <span
                                style="width:${practice.progreso}%;"
                            ></span>

                        </div>

                    </div>


                    <a
                        href="/docente/practicantes/${studentId}"
                        class="teacher-student-button"
                    >

                        <i class="bi bi-eye"></i>

                        Ver seguimiento

                    </a>

                </article>

            `;

        }).join('');

}


function renderDashboardVisits(visits) {

    const container =
        document.getElementById(
            'dashboardVisitsList'
        );


    if (!visits.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-calendar-check"></i>

                <strong>
                    Sin visitas por atender
                </strong>

            </div>

        `;

        return;

    }


    container.innerHTML =
        visits.map(visit => {

            const day =
                dashboardCalculateDay(
                    visit.inicio_practica
                );


            return `

                <div class="teacher-activity-item">

                    <div class="teacher-activity-date">

                        <strong>
                            ${day}
                        </strong>

                        <span>
                            DÍA
                        </span>

                    </div>


                    <div>

                        <strong>
                            ${visit.estudiante}
                        </strong>

                        <span>
                            ${visit.empresa ?? ''}
                        </span>

                    </div>

                </div>

            `;

        }).join('');

}


function renderDashboardAttention(
    visits,
    followups,
    processAlerts
) {

    const container =
        document.getElementById(
            'dashboardAttentionList'
        );


    const items = [];


    visits.forEach(visit => {

        items.push({

            student:
                visit.estudiante,

            text:
                'Visita de seguimiento habilitada',

            badge:
                'Visita'

        });

    });


    followups.forEach(item => {

        items.push({

            student:
                item.estudiante,

            text:
                item.titulo,

            badge:
                'Pendiente'

        });

    });


    processAlerts.forEach(item => {

        items.push({

            student:
                item.estudiante,

            text:
                'Monografía en seguimiento',

            badge:
                'Seguimiento'

        });

    });


    if (!items.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-check-circle"></i>

                <strong>
                    Sin procesos pendientes
                </strong>

            </div>

        `;

        return;

    }


    container.innerHTML =
        items.map(item => `

            <div class="teacher-attention-item">

                <div>

                    <strong>
                        ${item.student}
                    </strong>

                    <span>
                        ${item.text}
                    </span>

                </div>


                <span class="badge warning">
                    ${item.badge}
                </span>

            </div>

        `).join('');

}


loadTeacherDashboard();

</script>

@endpush

