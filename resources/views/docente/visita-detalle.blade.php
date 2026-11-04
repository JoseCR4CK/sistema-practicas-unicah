@extends('layouts.teacher')

@section('title', 'Detalle de visita | Portal del docente')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            VISITA DE SEGUIMIENTO
        </span>

        <h1 id="teacherVisitStudentTitle">
            Visita
        </h1>

        <p>
            Consulta la información disponible
            de la visita de tu practicante.
        </p>

    </div>


    <div class="d-flex gap-2">

        <a
            href="{{ route('docente.visitas') }}"
            class="btn btn-light coord-back-button"
        >
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>


        <a
            href="#"
            id="teacherVisitStudentLink"
            class="btn btn-primary coord-back-button"
        >
            <i class="bi bi-person"></i>
            Ver practicante
        </a>

    </div>

</div>



<div
    id="teacherVisitUnavailable"
    class="d-none"
>

    <section class="content-card">

        <div class="teacher-load-empty">

            <i class="bi bi-shield-exclamation"></i>

            <strong>
                Visita no disponible
            </strong>

            <span>
                Esta visita no pertenece a uno
                de tus practicantes asignados.
            </span>

        </div>

    </section>

</div>



<div id="teacherVisitDetailContent">


    <section class="teacher-visit-detail-profile mb-4">

        <div
            class="coord-practice-detail-avatar"
            id="teacherVisitInitials"
        ></div>


        <div>

            <span>
                PRACTICANTE
            </span>

            <strong id="teacherVisitStudent"></strong>

            <small id="teacherVisitAccount"></small>

        </div>


        <div class="teacher-visit-detail-summary">

            <div>
                <span>Día actual</span>
                <strong id="teacherVisitDay"></strong>
            </div>

            <div>
                <span>Estado</span>
                <strong id="teacherVisitStatusText"></strong>
            </div>

        </div>

    </section>



    <div
        class="coord-visit-message"
        id="teacherVisitMessage"
    >

        <i class="bi bi-info-circle"></i>

        <div>

            <strong id="teacherVisitMessageTitle"></strong>

            <span id="teacherVisitMessageText"></span>

        </div>

    </div>



    <section class="row g-3 mb-4">

        <div class="col-12 col-xl-6">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Empresa</h2>
                    </div>
                </div>


                <div class="coord-practice-info-card">

                    <i class="bi bi-buildings"></i>

                    <div>

                        <span>Empresa</span>

                        <strong id="teacherVisitCompany"></strong>

                        <small id="teacherVisitResponsible"></small>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-6">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Visita</h2>
                    </div>
                </div>


                <div class="coord-request-data-grid teacher-visit-data-grid">

                    <div>

                        <span>
                            Habilitada desde
                        </span>

                        <strong id="teacherVisitEnableDate"></strong>

                    </div>


                    <div>

                        <span>
                            Fecha programada
                        </span>

                        <strong id="teacherVisitScheduledDate"></strong>

                    </div>


                    <div>

                        <span>
                            Fecha realizada
                        </span>

                        <strong id="teacherVisitCompletedDate"></strong>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Registro de la visita
                </h2>

                <p>
                    Las acciones disponibles se completarán
                    cuando se confirme el procedimiento institucional.
                </p>

            </div>

        </div>


        <div class="coord-visit-pending-definition">

            <i class="bi bi-hourglass-split"></i>

            <div>

                <strong>
                    Pendiente de definición institucional
                </strong>

                <span>
                    Todavía debemos confirmar qué información,
                    evaluación, evidencia o documento debe registrar
                    el docente durante la visita de seguimiento.
                </span>

            </div>

        </div>

    </section>



    <section class="content-card">

        <div class="card-header">

            <div>
                <h2>Observaciones registradas</h2>
            </div>

        </div>


        <div class="coord-request-observation">

            <p id="teacherVisitObservation"></p>

        </div>

    </section>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/visits-store.js') }}"></script>

<script>

const TEACHER_VISIT_ID =
    {{ $visitaId }};


const CURRENT_TEACHER =
    'Msc. Carlos Martínez';


let currentTeacherVisit = null;


function calculateTeacherDay(start) {

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


function getTeacherEffectiveStatus(visit) {

    if (visit.estado === 'Realizada') {
        return 'Realizada';
    }

    if (visit.estado === 'Programada') {
        return 'Programada';
    }


    return calculateTeacherDay(
        visit.inicio_practica
    ) >= 45
        ? 'Habilitada'
        : 'No habilitada';

}


function calculateTeacherEnableDate(start) {

    const date =
        new Date(start + 'T00:00:00');


    date.setDate(
        date.getDate() + 44
    );


    return date;

}


function formatTeacherVisitDate(date) {

    if (!date) {
        return 'No registrada';
    }


    const parsed =
        date instanceof Date
            ? date
            : new Date(
                date + 'T12:00:00'
            );


    return parsed.toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );

}


function renderTeacherVisitDetail() {

    currentTeacherVisit =
        VisitStore.getById(
            TEACHER_VISIT_ID
        );


    /*
    |--------------------------------------------------------------------------
    | CONTROL DE ACCESO DEL PROTOTIPO
    |--------------------------------------------------------------------------
    */

    if (
        !currentTeacherVisit ||
        currentTeacherVisit.docente !== CURRENT_TEACHER
    ) {

        document
            .getElementById(
                'teacherVisitDetailContent'
            )
            .classList
            .add('d-none');


        document
            .getElementById(
                'teacherVisitUnavailable'
            )
            .classList
            .remove('d-none');


        return;

    }


    const day =
        calculateTeacherDay(
            currentTeacherVisit.inicio_practica
        );


    const status =
        getTeacherEffectiveStatus(
            currentTeacherVisit
        );


    const initials =
        currentTeacherVisit.estudiante
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(word =>
                word.charAt(0)
            )
            .join('')
            .toUpperCase();


    document.getElementById(
        'teacherVisitStudentTitle'
    ).textContent =
        currentTeacherVisit.estudiante;


    document.getElementById(
        'teacherVisitInitials'
    ).textContent =
        initials;


    document.getElementById(
        'teacherVisitStudent'
    ).textContent =
        currentTeacherVisit.estudiante;


    document.getElementById(
        'teacherVisitAccount'
    ).textContent =
        currentTeacherVisit.cuenta;


    document.getElementById(
        'teacherVisitDay'
    ).textContent =
        day;


    document.getElementById(
        'teacherVisitStatusText'
    ).textContent =
        status;


    document.getElementById(
        'teacherVisitCompany'
    ).textContent =
        currentTeacherVisit.empresa;


    document.getElementById(
        'teacherVisitResponsible'
    ).textContent =
        `Responsable: ${currentTeacherVisit.responsable}`;


    document.getElementById(
        'teacherVisitEnableDate'
    ).textContent =
        formatTeacherVisitDate(
            calculateTeacherEnableDate(
                currentTeacherVisit.inicio_practica
            )
        );


    document.getElementById(
        'teacherVisitScheduledDate'
    ).textContent =
        formatTeacherVisitDate(
            currentTeacherVisit.fecha_programada
        );


    document.getElementById(
        'teacherVisitCompletedDate'
    ).textContent =
        formatTeacherVisitDate(
            currentTeacherVisit.fecha_realizada
        );


    document.getElementById(
        'teacherVisitObservation'
    ).textContent =
        currentTeacherVisit.observacion ||
        'Sin observaciones registradas.';


    document.getElementById(
        'teacherVisitStudentLink'
    ).href =
        `/docente/practicantes/${currentTeacherVisit.student_id}`;


    const message =
        document.getElementById(
            'teacherVisitMessage'
        );


    if (status === 'Realizada') {

        message.className =
            'coord-visit-message completed';


        document.getElementById(
            'teacherVisitMessageTitle'
        ).textContent =
            'Visita realizada';


        document.getElementById(
            'teacherVisitMessageText'
        ).textContent =
            'Esta visita ya se encuentra registrada como realizada.';

    }

    else if (status === 'Programada') {

        message.className =
            'coord-visit-message scheduled';


        document.getElementById(
            'teacherVisitMessageTitle'
        ).textContent =
            'Visita programada';


        document.getElementById(
            'teacherVisitMessageText'
        ).textContent =
            'La visita ya cuenta con una fecha programada.';

    }

    else if (status === 'Habilitada') {

        message.className =
            'coord-visit-message enabled';


        document.getElementById(
            'teacherVisitMessageTitle'
        ).textContent =
            'Visita habilitada';


        document.getElementById(
            'teacherVisitMessageText'
        ).textContent =
            `El practicante se encuentra en el día ${day} y ya cumple la condición de habilitación.`;

    }

    else {

        message.className =
            'coord-visit-message disabled';


        document.getElementById(
            'teacherVisitMessageTitle'
        ).textContent =
            'Visita todavía no habilitada';


        document.getElementById(
            'teacherVisitMessageText'
        ).textContent =
            `El practicante se encuentra en el día ${day}. La visita se habilita a partir del día 45.`;

    }

}


renderTeacherVisitDetail();

</script>

@endpush