@extends('layouts.app')

@section('title', 'Detalle de práctica | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SEGUIMIENTO DE PRÁCTICA
        </span>

        <h1 id="practiceStudentName">
            Práctica profesional
        </h1>

        <p>
            Consulta el estado general y avance
            del proceso de práctica profesional.
        </p>

    </div>


    <div class="d-flex gap-2">

        <a
            href="{{ route('coordinacion.practicas') }}"
            class="btn btn-light coord-back-button"
        >
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>


        <a
            href="#"
            id="practiceStudentRecord"
            class="btn btn-primary coord-back-button"
        >
            <i class="bi bi-folder2-open"></i>
            Ver expediente
        </a>

    </div>

</div>



<div id="practiceNotFound" class="d-none">

    <section class="content-card">

        <div class="teacher-load-empty">

            <i class="bi bi-exclamation-circle"></i>

            <strong>
                Práctica no encontrada
            </strong>

        </div>

    </section>

</div>



<div id="practiceDetailContent">


    {{-- RESUMEN PRINCIPAL --}}

    <section class="coord-practice-detail-profile mb-4">

        <div class="coord-practice-detail-avatar" id="practiceInitials">
        </div>


        <div>

            <span>ESTUDIANTE</span>

            <strong id="practiceName"></strong>

            <small id="practiceAccount"></small>

        </div>


        <div class="coord-practice-detail-summary">

            <div>
                <span>Etapa actual</span>
                <strong id="practiceCurrentStage"></strong>
            </div>

            <div>
                <span>Día de práctica</span>
                <strong id="practiceCurrentDay"></strong>
            </div>

            <div>
                <span>Progreso</span>
                <strong id="practiceProgressText"></strong>
            </div>

        </div>

    </section>



    {{-- ALERTA --}}

    <div
        class="coord-practice-detail-alert d-none"
        id="practiceAlert"
    >

        <i
            class="bi bi-exclamation-circle"
            id="practiceAlertIcon"
        ></i>

        <div>

            <strong id="practiceAlertTitle"></strong>

            <span id="practiceAlertText"></span>

        </div>

    </div>



    {{-- INFORMACIÓN PRINCIPAL --}}

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

                        <span>
                            Empresa
                        </span>

                        <strong id="practiceCompany"></strong>

                        <small id="practiceResponsible"></small>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="content-card h-100">

                <div class="card-header">

                    <div>
                        <h2>Docente asignado</h2>
                    </div>

                </div>


                <div class="coord-practice-info-card">

                    <i class="bi bi-person-video3"></i>

                    <div>

                        <span>
                            Docente de seguimiento
                        </span>

                        <strong id="practiceTeacher"></strong>

                        <small>
                            Seguimiento académico
                        </small>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-4">

            <div class="content-card h-100">

                <div class="card-header">

                    <div>
                        <h2>Duración</h2>
                    </div>

                </div>


                <div class="coord-practice-dates">

                    <div>
                        <span>Inicio</span>
                        <strong id="practiceStart"></strong>
                    </div>

                    <i class="bi bi-arrow-right"></i>

                    <div>
                        <span>Finalización</span>
                        <strong id="practiceEnd"></strong>
                    </div>

                </div>

            </div>

        </div>

    </section>



    {{-- PROGRESO --}}

    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Avance general
                </h2>

                <p>
                    Vista general de las etapas conocidas
                    del proceso de práctica profesional.
                </p>

            </div>

        </div>


        <div class="coord-practice-main-progress">

            <div class="coord-practice-main-progress-header">

                <span>
                    Progreso del proceso
                </span>

                <strong id="practiceProgressPercent"></strong>

            </div>


            <div class="coord-practice-main-progress-bar">

                <span id="practiceProgressBar"></span>

            </div>

        </div>


        <div
            class="coord-practice-timeline"
            id="practiceTimeline"
        ></div>

    </section>



    {{-- ESTADOS DE SEGUIMIENTO --}}

    <section class="row g-3 mb-4">

        <div class="col-12 col-md-6">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Visita</h2>
                    </div>
                </div>

                <div class="coord-practice-status-box">

                    <i class="bi bi-calendar-check"></i>

                    <div>
                        <span>Estado</span>
                        <strong id="practiceVisitStatus"></strong>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-md-6">

            <div class="content-card h-100">

                <div class="card-header">
                    <div>
                        <h2>Monografía</h2>
                    </div>
                </div>

                <div class="coord-practice-status-box">

                    <i class="bi bi-journal-text"></i>

                    <div>
                        <span>Estado</span>
                        <strong id="practiceMonographStatus"></strong>
                    </div>

                </div>

            </div>

        </div>

    </section>



    {{-- OBSERVACIÓN --}}

    <section class="content-card">

        <div class="card-header">

            <div>
                <h2>Observaciones</h2>
            </div>

        </div>


        <div class="coord-request-observation">

            <p id="practiceObservations"></p>

        </div>

    </section>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/practices-store.js') }}"></script>

<script>

const PRACTICE_ID =
    {{ $practicaId }};


let currentPractice = null;


const processStages = [

    'Solicitud',
    'Documentación',
    'Empresa',
    'Práctica',
    'Visita',
    'Monografía',
    'Defensa',
    'Finalización'

];


function formatDate(date) {

    if (!date) return '—';


    return new Date(
        date + 'T12:00:00'
    ).toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );

}


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


function getAlert(practice) {

    const day =
        calculateDay(
            practice.inicio
        );


    if (
        day >= 45 &&
        practice.visita_estado === 'Pendiente'
    ) {

        return {
            type: 'warning',

            title:
                'Visita de seguimiento habilitada',

            text:
                `El estudiante ha alcanzado el día ${day} de práctica y ya puede continuar con el proceso correspondiente a la visita.`
        };

    }


    if (!practice.docente) {

        return {
            type: 'danger',

            title:
                'Docente pendiente de asignación',

            text:
                'La práctica todavía no cuenta con un docente de seguimiento asignado.'
        };

    }


    return null;

}


function renderTimeline() {

    const container =
        document.getElementById(
            'practiceTimeline'
        );


    const currentIndex =
        processStages.indexOf(
            currentPractice.etapa
        );


    container.innerHTML =
        processStages.map(
            (stage, index) => {

                let status =
                    'future';


                if (index < currentIndex) {
                    status = 'completed';
                }


                if (index === currentIndex) {
                    status = 'active';
                }


                return `

                    <div class="coord-practice-step ${status}">

                        <div class="coord-practice-step-marker">

                            ${
                                status === 'completed'
                                    ? '<i class="bi bi-check-lg"></i>'
                                    : index + 1
                            }

                        </div>


                        <span>
                            ${stage}
                        </span>

                    </div>

                `;

            }
        ).join('');

}


function renderPracticeDetail() {

    currentPractice =
        PracticeStore.getById(
            PRACTICE_ID
        );


    if (!currentPractice) {

        document
            .getElementById(
                'practiceDetailContent'
            )
            .classList
            .add('d-none');


        document
            .getElementById(
                'practiceNotFound'
            )
            .classList
            .remove('d-none');

        return;

    }


    const initials =
        currentPractice.estudiante
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(word =>
                word.charAt(0)
            )
            .join('')
            .toUpperCase();


    const day =
        calculateDay(
            currentPractice.inicio
        );


    document.getElementById(
        'practiceStudentName'
    ).textContent =
        currentPractice.estudiante;


    document.getElementById(
        'practiceInitials'
    ).textContent =
        initials;


    document.getElementById(
        'practiceName'
    ).textContent =
        currentPractice.estudiante;


    document.getElementById(
        'practiceAccount'
    ).textContent =
        currentPractice.cuenta;


    document.getElementById(
        'practiceCurrentStage'
    ).textContent =
        currentPractice.etapa;


    document.getElementById(
        'practiceCurrentDay'
    ).textContent =
        day;


    document.getElementById(
        'practiceProgressText'
    ).textContent =
        `${currentPractice.progreso}%`;


    document.getElementById(
        'practiceCompany'
    ).textContent =
        currentPractice.empresa;


    document.getElementById(
        'practiceResponsible'
    ).textContent =
        `Responsable: ${currentPractice.responsable}`;


    document.getElementById(
        'practiceTeacher'
    ).textContent =
        currentPractice.docente ||
        'Pendiente de asignación';


    document.getElementById(
        'practiceStart'
    ).textContent =
        formatDate(
            currentPractice.inicio
        );


    document.getElementById(
        'practiceEnd'
    ).textContent =
        formatDate(
            currentPractice.finalizacion
        );


    document.getElementById(
        'practiceProgressPercent'
    ).textContent =
        `${currentPractice.progreso}%`;


    document.getElementById(
        'practiceProgressBar'
    ).style.width =
        `${currentPractice.progreso}%`;


    document.getElementById(
        'practiceVisitStatus'
    ).textContent =
        currentPractice.visita_estado;


    document.getElementById(
        'practiceMonographStatus'
    ).textContent =
        currentPractice.monografia_estado;


    document.getElementById(
        'practiceObservations'
    ).textContent =
        currentPractice.observaciones ||
        'Sin observaciones registradas.';


    document.getElementById(
        'practiceStudentRecord'
    ).href =
        `/coordinacion/estudiantes/${currentPractice.student_id}`;


    const alert =
        getAlert(
            currentPractice
        );


    const alertBox =
        document.getElementById(
            'practiceAlert'
        );


    if (alert) {

        alertBox.classList.remove(
            'd-none'
        );


        alertBox.classList.remove(
            'warning',
            'danger'
        );


        alertBox.classList.add(
            alert.type
        );


        document.getElementById(
            'practiceAlertTitle'
        ).textContent =
            alert.title;


        document.getElementById(
            'practiceAlertText'
        ).textContent =
            alert.text;

    } else {

        alertBox.classList.add(
            'd-none'
        );

    }


    renderTimeline();

}


renderPracticeDetail();

</script>

@endpush