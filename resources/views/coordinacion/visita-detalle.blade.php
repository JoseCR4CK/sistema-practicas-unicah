@extends('layouts.app')

@section('title', 'Detalle de visita | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            VISITA DE SEGUIMIENTO
        </span>

        <h1 id="visitStudentName">
            Visita
        </h1>

        <p>
            Consulta el estado de la visita correspondiente
            al proceso de práctica profesional.
        </p>

    </div>


    <div class="d-flex gap-2">

        <a
            href="{{ route('coordinacion.visitas') }}"
            class="btn btn-light coord-back-button"
        >
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>


        <a
            href="#"
            id="visitStudentRecord"
            class="btn btn-primary coord-back-button"
        >
            <i class="bi bi-folder2-open"></i>
            Ver expediente
        </a>

    </div>

</div>



<div id="visitNotFound" class="d-none">

    <section class="content-card">

        <div class="teacher-load-empty">

            <i class="bi bi-exclamation-circle"></i>

            <strong>
                Visita no encontrada
            </strong>

        </div>

    </section>

</div>



<div id="visitDetailContent">


    <section class="coord-visit-detail-profile mb-4">

        <div
            class="coord-practice-detail-avatar"
            id="visitInitials"
        ></div>


        <div>

            <span>
                ESTUDIANTE
            </span>

            <strong id="visitStudent"></strong>

            <small id="visitAccount"></small>

        </div>


        <div class="coord-visit-detail-summary">

            <div>
                <span>Día actual</span>
                <strong id="visitCurrentDay"></strong>
            </div>

            <div>
                <span>Estado</span>
                <strong id="visitCurrentStatus"></strong>
            </div>

        </div>

    </section>



    <div
        class="coord-visit-message"
        id="visitMessage"
    >

        <i class="bi bi-info-circle"></i>

        <div>

            <strong id="visitMessageTitle"></strong>

            <span id="visitMessageText"></span>

        </div>

    </div>



    <section class="row g-3 mb-4">

        <div class="col-12 col-xl-4">

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

                        <strong id="visitCompany"></strong>

                        <small id="visitResponsible"></small>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Docente</h2>
                    </div>
                </div>

                <div class="coord-practice-info-card">

                    <i class="bi bi-person-video3"></i>

                    <div>

                        <span>Docente asignado</span>

                        <strong id="visitTeacher"></strong>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Habilitación</h2>
                    </div>
                </div>

                <div class="coord-practice-info-card">

                    <i class="bi bi-calendar-check"></i>

                    <div>

                        <span>
                            Disponible desde el día 45
                        </span>

                        <strong id="visitEnableDate"></strong>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Información de la visita
                </h2>

                <p>
                    Datos registrados actualmente
                    para este seguimiento.
                </p>

            </div>

        </div>


        <div class="coord-request-data-grid">

            <div>
                <span>Estado</span>
                <strong id="visitStatusData"></strong>
            </div>

            <div>
                <span>Fecha programada</span>
                <strong id="visitScheduledDate"></strong>
            </div>

            <div>
                <span>Fecha realizada</span>
                <strong id="visitCompletedDate"></strong>
            </div>

            <div>
                <span>Día de práctica</span>
                <strong id="visitPracticeDay"></strong>
            </div>

        </div>

    </section>



    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Requisitos y evidencias
                </h2>

                <p>
                    Este apartado será completado cuando
                    se confirme el procedimiento institucional.
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
                    Todavía no se han establecido en el prototipo
                    los documentos, evidencias, formularios o
                    evaluaciones requeridas para validar la visita.
                </span>

            </div>

        </div>

    </section>



    <section
        class="content-card"
        id="visitObservationCard"
    >

        <div class="card-header">

            <div>
                <h2>Observaciones</h2>
            </div>

        </div>


        <div class="coord-request-observation">

            <p id="visitObservation"></p>

        </div>

    </section>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/visits-store.js') }}"></script>

<script>

const VISIT_ID =
    {{ $visitaId }};


let currentVisit = null;


function calculateDay(start) {

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

function getEffectiveVisitStatus(visit) {

    if (visit.estado === 'Realizada') {
        return 'Realizada';
    }

    if (visit.estado === 'Programada') {
        return 'Programada';
    }

    return calculateDay(visit.inicio_practica) >= 45
        ? 'Habilitada'
        : 'No habilitada';
}

function enableDate(start) {

    const date =
        new Date(start + 'T00:00:00');


    date.setDate(
        date.getDate() + 44
    );


    return date;

}


function formatDate(date) {

    if (!date) {
        return 'No registrada';
    }


    const parsed =
        typeof date === 'string'
            ? new Date(date + 'T12:00:00')
            : date;


    return parsed.toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );

}


function renderVisitDetail() {

    currentVisit =
        VisitStore.getById(
            VISIT_ID
        );

    const effectiveStatus =
        getEffectiveVisitStatus(currentVisit);

    if (!currentVisit) {

        document
            .getElementById(
                'visitDetailContent'
            )
            .classList
            .add('d-none');


        document
            .getElementById(
                'visitNotFound'
            )
            .classList
            .remove('d-none');

        return;

    }


    const day =
        calculateDay(
            currentVisit.inicio_practica
        );


    const enabledDate =
        enableDate(
            currentVisit.inicio_practica
        );


    const initials =
        currentVisit.estudiante
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(word =>
                word.charAt(0)
            )
            .join('')
            .toUpperCase();


    document.getElementById(
        'visitStudentName'
    ).textContent =
        currentVisit.estudiante;


    document.getElementById(
        'visitInitials'
    ).textContent =
        initials;


    document.getElementById(
        'visitStudent'
    ).textContent =
        currentVisit.estudiante;


    document.getElementById(
        'visitAccount'
    ).textContent =
        currentVisit.cuenta;


    document.getElementById(
        'visitCurrentDay'
    ).textContent =
        day;


    document.getElementById(
        'visitCurrentStatus'
    ).textContent =
        effectiveStatus;


    document.getElementById(
        'visitCompany'
    ).textContent =
        currentVisit.empresa;


    document.getElementById(
        'visitResponsible'
    ).textContent =
        `Responsable: ${currentVisit.responsable}`;


    document.getElementById(
        'visitTeacher'
    ).textContent =
        currentVisit.docente;


    document.getElementById(
        'visitEnableDate'
    ).textContent =
        formatDate(enabledDate);


    document.getElementById(
        'visitStatusData'
    ).textContent =
        effectiveStatus;


    document.getElementById(
        'visitScheduledDate'
    ).textContent =
        formatDate(
            currentVisit.fecha_programada
        );


    document.getElementById(
        'visitCompletedDate'
    ).textContent =
        formatDate(
            currentVisit.fecha_realizada
        );


    document.getElementById(
        'visitPracticeDay'
    ).textContent =
        day;


    document.getElementById(
        'visitObservation'
    ).textContent =
        currentVisit.observacion ||
        'Sin observaciones registradas.';


    document.getElementById(
        'visitStudentRecord'
    ).href =
        `/coordinacion/estudiantes/${currentVisit.student_id}`;


    const message =
        document.getElementById(
            'visitMessage'
        );


    if (currentVisit.estado === 'Realizada') {

        message.className =
            'coord-visit-message completed';


        document.getElementById(
            'visitMessageTitle'
        ).textContent =
            'Visita realizada';


        document.getElementById(
            'visitMessageText'
        ).textContent =
            'La visita se encuentra registrada como realizada.';

    }

    else if (day >= 45) {

        message.className =
            'coord-visit-message enabled';


        document.getElementById(
            'visitMessageTitle'
        ).textContent =
            'Visita habilitada';


        document.getElementById(
            'visitMessageText'
        ).textContent =
            `El estudiante ha alcanzado el día ${day} de práctica.`;

    }

    else {

        message.className =
            'coord-visit-message disabled';


        document.getElementById(
            'visitMessageTitle'
        ).textContent =
            'Visita aún no habilitada';


        document.getElementById(
            'visitMessageText'
        ).textContent =
            `La visita estará disponible a partir del día 45. Actualmente el estudiante se encuentra en el día ${day}.`;

    }

}


renderVisitDetail();

</script>

@endpush