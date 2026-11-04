@extends('layouts.app')

@section('title', 'Detalle de solicitud | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            SOLICITUD DE PRÁCTICA
        </span>

        <h1 id="requestStudentName">
            Solicitud
        </h1>

        <p>
            Revisa la información antes de tomar una decisión.
        </p>

    </div>


    <a
        href="{{ route('coordinacion.solicitudes') }}"
        class="btn btn-light coord-back-button"
    >
        <i class="bi bi-arrow-left"></i>
        Volver a solicitudes
    </a>

</div>



<div id="requestNotFound" class="d-none">

    <section class="content-card">

        <div class="teacher-load-empty">

            <i class="bi bi-exclamation-circle"></i>

            <strong>
                Solicitud no encontrada
            </strong>

        </div>

    </section>

</div>



<div id="requestDetailContent">


    {{-- ESTADO --}}

    <section class="coord-request-detail-header mb-4">

        <div class="coord-request-detail-icon">

            <i class="bi bi-file-earmark-text"></i>

        </div>


        <div>

            <span>
                ESTADO DE LA SOLICITUD
            </span>

            <strong id="requestDetailStatus"></strong>

            <small id="requestDetailDate"></small>

        </div>


        <div
            class="coord-request-detail-actions"
            id="requestDecisionActions"
        >

            <button
                type="button"
                class="btn coord-request-reject"
                data-bs-toggle="modal"
                data-bs-target="#rejectRequestModal"
            >
                <i class="bi bi-x-lg"></i>
                Rechazar
            </button>


            <button
                type="button"
                class="btn btn-success"
                onclick="approveRequest()"
            >
                <i class="bi bi-check-lg"></i>
                Aprobar solicitud
            </button>

        </div>

    </section>



    {{-- INFORMACIÓN ESTUDIANTE --}}

    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Información del estudiante
                </h2>

                <p>
                    Datos asociados a la solicitud.
                </p>

            </div>

        </div>


        <div class="coord-request-data-grid">

            <div>
                <span>Nombre</span>
                <strong id="requestName"></strong>
            </div>

            <div>
                <span>Número de cuenta</span>
                <strong id="requestAccount"></strong>
            </div>

            <div>
                <span>Correo institucional</span>
                <strong id="requestEmail"></strong>
            </div>

            <div>
                <span>Carrera</span>
                <strong id="requestCareer"></strong>
            </div>

        </div>

    </section>



    {{-- DATOS DE SOLICITUD --}}

    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Información de la solicitud
                </h2>

                <p>
                    Datos proporcionados para iniciar
                    el proceso de práctica profesional.
                </p>

            </div>

        </div>


        <div class="coord-request-data-grid">

            <div>
                <span>Empresa propuesta</span>
                <strong id="requestCompany"></strong>
            </div>

            <div>
                <span>Fecha de solicitud</span>
                <strong id="requestSubmitted"></strong>
            </div>

            <div>
                <span>Inicio propuesto</span>
                <strong id="requestProposedStart"></strong>
            </div>

            <div>
                <span>Estado</span>
                <strong id="requestStatusText"></strong>
            </div>

        </div>


        <div class="coord-request-observation">

            <span>
                Observación del estudiante
            </span>

            <p id="requestStudentObservation"></p>

        </div>

    </section>



    {{-- AVISO --}}

    <div class="coord-request-info-note">

        <i class="bi bi-info-circle"></i>

        <div>

            <strong>
                Alcance de la aprobación
            </strong>

            <span>
                Aprobar esta solicitud autoriza la continuación
                del proceso. La validación de documentos se
                realizará posteriormente en el módulo correspondiente.
            </span>

        </div>

    </div>



    {{-- RESULTADO RECHAZO --}}

    <section
        class="content-card mt-4 d-none"
        id="rejectionSection"
    >

        <div class="card-header">

            <div>
                <h2>Motivo de rechazo</h2>
            </div>

        </div>


        <div class="coord-request-rejection-text">

            <i class="bi bi-exclamation-triangle"></i>

            <span id="rejectionReason"></span>

        </div>

    </section>



    {{-- HISTORIAL --}}

    <section class="content-card mt-4">

        <div class="card-header">

            <div>

                <h2>
                    Historial de la solicitud
                </h2>

                <p>
                    Eventos registrados durante el proceso.
                </p>

            </div>

        </div>


        <div id="requestHistory"></div>

    </section>

</div>



{{-- MODAL RECHAZO --}}

<div
    class="modal fade"
    id="rejectRequestModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content coord-company-modal">

            <div class="modal-header">

                <div>

                    <span class="student-home-eyebrow">
                        RECHAZAR SOLICITUD
                    </span>

                    <h5 class="modal-title">
                        Indicar motivo
                    </h5>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="coord-correction-warning">

                    <i class="bi bi-exclamation-triangle"></i>

                    <div>

                        <strong>
                            El motivo es obligatorio
                        </strong>

                        <span>
                            El estudiante deberá conocer por qué
                            su solicitud no puede continuar.
                        </span>

                    </div>

                </div>


                <label class="form-label">
                    Motivo del rechazo
                </label>

                <textarea
                    id="rejectionReasonInput"
                    class="form-control"
                    rows="4"
                    placeholder="Describe brevemente qué debe corregirse..."
                ></textarea>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>


                <button
                    type="button"
                    class="btn btn-danger"
                    onclick="rejectRequest()"
                >
                    <i class="bi bi-x-lg"></i>
                    Rechazar solicitud
                </button>

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/requests-store.js') }}"></script>

<script>

const REQUEST_ID =
    {{ $solicitudId }};


let currentRequest = null;


function formatDate(date) {

    if (!date) {
        return '—';
    }


    const parsed =
        date.includes('T')
            ? new Date(date)
            : new Date(date + 'T12:00:00');


    return parsed.toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );

}


function renderRequestDetail() {

    currentRequest =
        RequestStore.getById(
            REQUEST_ID
        );


    if (!currentRequest) {

        document
            .getElementById(
                'requestDetailContent'
            )
            .classList
            .add('d-none');


        document
            .getElementById(
                'requestNotFound'
            )
            .classList
            .remove('d-none');

        return;

    }


    document.getElementById(
        'requestStudentName'
    ).textContent =
        currentRequest.estudiante;


    document.getElementById(
        'requestName'
    ).textContent =
        currentRequest.estudiante;


    document.getElementById(
        'requestAccount'
    ).textContent =
        currentRequest.cuenta;


    document.getElementById(
        'requestEmail'
    ).textContent =
        currentRequest.correo;


    document.getElementById(
        'requestCareer'
    ).textContent =
        currentRequest.carrera;


    document.getElementById(
        'requestCompany'
    ).textContent =
        currentRequest.empresa;


    document.getElementById(
        'requestSubmitted'
    ).textContent =
        formatDate(
            currentRequest.fecha_solicitud
        );


    document.getElementById(
        'requestProposedStart'
    ).textContent =
        formatDate(
            currentRequest.fecha_inicio_propuesta
        );


    document.getElementById(
        'requestStudentObservation'
    ).textContent =
        currentRequest.observacion_estudiante ||
        'Sin observaciones.';


    const statusElements = [

        document.getElementById(
            'requestDetailStatus'
        ),

        document.getElementById(
            'requestStatusText'
        )

    ];


    statusElements.forEach(element => {

        element.textContent =
            currentRequest.estado;


        element.className =
            currentRequest.estado === 'Pendiente'
                ? 'request-status-text pending'
                : currentRequest.estado === 'Aprobada'
                    ? 'request-status-text approved'
                    : 'request-status-text rejected';

    });


    document.getElementById(
        'requestDetailDate'
    ).textContent =
        currentRequest.fecha_revision
            ? `Revisada el ${formatDate(currentRequest.fecha_revision)}`
            : `Enviada el ${formatDate(currentRequest.fecha_solicitud)}`;


    const actions =
        document.getElementById(
            'requestDecisionActions'
        );


    actions.classList.toggle(
        'd-none',
        currentRequest.estado !== 'Pendiente'
    );


    const rejectionSection =
        document.getElementById(
            'rejectionSection'
        );


    if (
        currentRequest.estado === 'Rechazada' &&
        currentRequest.motivo_rechazo
    ) {

        rejectionSection.classList.remove(
            'd-none'
        );


        document.getElementById(
            'rejectionReason'
        ).textContent =
            currentRequest.motivo_rechazo;

    } else {

        rejectionSection.classList.add(
            'd-none'
        );

    }


    renderHistory();

}


function approveRequest() {

    if (
        !currentRequest ||
        currentRequest.estado !== 'Pendiente'
    ) {
        return;
    }


    const confirmed =
        confirm(
            '¿Deseas aprobar esta solicitud de práctica profesional?'
        );


    if (!confirmed) {
        return;
    }


    const now =
        new Date().toISOString();


    currentRequest.estado =
        'Aprobada';


    currentRequest.revisado_por =
        'Coordinación Académica';


    currentRequest.fecha_revision =
        now;


    currentRequest.motivo_rechazo =
        null;


    currentRequest.historial.push({

        tipo: 'Aprobación',

        descripcion:
            'Solicitud aprobada por Coordinación Académica.',

        fecha: now

    });


    RequestStore.saveRequest(
        currentRequest
    );


    renderRequestDetail();

}


function rejectRequest() {

    if (
        !currentRequest ||
        currentRequest.estado !== 'Pendiente'
    ) {
        return;
    }


    const reason =
        document
            .getElementById(
                'rejectionReasonInput'
            )
            .value
            .trim();


    if (!reason) {

        alert(
            'Debes indicar el motivo del rechazo.'
        );

        return;

    }


    const now =
        new Date().toISOString();


    currentRequest.estado =
        'Rechazada';


    currentRequest.motivo_rechazo =
        reason;


    currentRequest.revisado_por =
        'Coordinación Académica';


    currentRequest.fecha_revision =
        now;


    currentRequest.historial.push({

        tipo: 'Rechazo',

        descripcion:
            `Solicitud rechazada: ${reason}`,

        fecha: now

    });


    RequestStore.saveRequest(
        currentRequest
    );


    bootstrap.Modal
        .getInstance(
            document.getElementById(
                'rejectRequestModal'
            )
        )
        .hide();


    renderRequestDetail();

}


function renderHistory() {

    const container =
        document.getElementById(
            'requestHistory'
        );


    const history =
        [...(currentRequest.historial || [])]
            .reverse();


    container.innerHTML =
        history.map(event => {

            const icon =
                event.tipo === 'Aprobación'
                    ? 'bi-check-circle'
                    : event.tipo === 'Rechazo'
                        ? 'bi-x-circle'
                        : 'bi-file-earmark-plus';


            return `

                <article class="coord-request-history-row">

                    <div class="coord-request-history-icon">
                        <i class="bi ${icon}"></i>
                    </div>


                    <div>

                        <strong>
                            ${event.tipo}
                        </strong>

                        <span>
                            ${event.descripcion}
                        </span>

                    </div>


                    <div class="coord-request-history-date">

                        ${formatDate(event.fecha)}

                    </div>

                </article>

            `;

        }).join('');

}


renderRequestDetail();

</script>

@endpush