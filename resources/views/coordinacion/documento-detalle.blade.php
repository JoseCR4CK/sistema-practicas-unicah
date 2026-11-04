@extends('layouts.app')

@section('title', 'Detalle de documento | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            REVISIÓN DOCUMENTAL
        </span>

        <h1 id="documentTitle">
            Documento
        </h1>

        <p>
            Revisa la versión actual y el historial
            del documento presentado.
        </p>

    </div>


    <a
        href="{{ route('coordinacion.documentos') }}"
        class="btn btn-light coord-back-button"
    >
        <i class="bi bi-arrow-left"></i>
        Volver a documentos
    </a>

</div>



<div id="documentNotFound" class="d-none">

    <section class="content-card">

        <div class="teacher-load-empty">

            <i class="bi bi-exclamation-circle"></i>

            <strong>
                Documento no encontrado
            </strong>

        </div>

    </section>

</div>



<div id="documentDetailContent">


    <section class="coord-doc-detail-header mb-4">

        <div class="coord-doc-detail-icon">

            <i class="bi bi-file-earmark-text"></i>

        </div>


        <div>

            <span id="documentCategory"></span>

            <strong id="documentName"></strong>

            <small id="documentVersion"></small>

        </div>


        <div
            class="coord-doc-detail-actions"
            id="documentReviewActions"
        >

            <button
                type="button"
                class="btn coord-doc-correction-btn"
                data-bs-toggle="modal"
                data-bs-target="#correctionModal"
            >
                <i class="bi bi-exclamation-circle"></i>

                Solicitar corrección
            </button>


            <button
                type="button"
                class="btn btn-success"
                onclick="approveDocument()"
            >
                <i class="bi bi-check-lg"></i>

                Aprobar documento
            </button>

        </div>

    </section>



    <section class="row g-3 mb-4">

        <div class="col-12 col-xl-6">

            <div class="content-card h-100">

                <div class="card-header">

                    <div>
                        <h2>Estudiante</h2>
                    </div>

                </div>


                <div class="coord-doc-student">

                    <div class="coord-doc-student-avatar" id="documentInitials">
                    </div>

                    <div>

                        <span>ESTUDIANTE</span>

                        <strong id="documentStudent"></strong>

                        <small id="documentAccount"></small>

                        <small id="documentCareer"></small>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-12 col-xl-6">

            <div class="content-card h-100">

                <div class="card-header">

                    <div>
                        <h2>Versión actual</h2>
                    </div>

                </div>


                <div class="coord-doc-current-file">

                    <i class="bi bi-file-earmark-pdf"></i>

                    <div>

                        <span>Archivo registrado</span>

                        <strong id="documentFile"></strong>

                        <small id="documentUploadDate"></small>

                    </div>


                    <span
                        id="documentCurrentStatus"
                        class="coord-doc-status"
                    ></span>

                </div>

            </div>

        </div>

    </section>



    <section
        class="content-card mb-4 d-none"
        id="documentObservationSection"
    >

        <div class="card-header">

            <div>
                <h2>Observación actual</h2>
            </div>

        </div>


        <div class="coord-doc-observation">

            <i class="bi bi-chat-left-text"></i>

            <span id="documentObservation"></span>

        </div>

    </section>



    <section class="content-card">

        <div class="card-header">

            <div>

                <h2>
                    Historial de versiones
                </h2>

                <p>
                    Conserva la trazabilidad de correcciones
                    y documentos presentados.
                </p>

            </div>

        </div>


        <div id="documentVersions"></div>

    </section>

</div>



<div
    class="modal fade"
    id="correctionModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content coord-company-modal">

            <div class="modal-header">

                <div>

                    <span class="student-home-eyebrow">
                        CORRECCIÓN
                    </span>

                    <h5 class="modal-title">
                        Solicitar corrección
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
                            El estudiante deberá presentar una nueva versión
                        </strong>

                        <span>
                            La versión actual permanecerá registrada
                            en el historial.
                        </span>

                    </div>

                </div>


                <label class="form-label">
                    Observación de Coordinación
                </label>

                <textarea
                    id="documentCorrectionReason"
                    class="form-control"
                    rows="4"
                    placeholder="Indica qué debe corregir el estudiante..."
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
                    class="btn btn-warning"
                    onclick="requestDocumentCorrection()"
                >
                    <i class="bi bi-exclamation-circle"></i>
                    Solicitar corrección
                </button>

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/documents-store.js') }}"></script>

<script>

const DOCUMENT_ID =
    {{ $documentoId }};


let currentDocument = null;


function formatDocumentDate(date) {

    if (!date) return '—';


    return new Date(date)
        .toLocaleString(
            'es-HN',
            {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }
        );

}


function getStatusClass(status) {

    if (status === 'Aprobado') {
        return 'approved';
    }

    if (status === 'Corrección requerida') {
        return 'correction';
    }

    if (status === 'Rechazado') {
        return 'rejected';
    }

    return 'pending';

}


function renderDocumentDetail() {

    currentDocument =
        DocumentStore.getById(
            DOCUMENT_ID
        );


    if (!currentDocument) {

        document.getElementById(
            'documentDetailContent'
        ).classList.add('d-none');


        document.getElementById(
            'documentNotFound'
        ).classList.remove('d-none');

        return;

    }


    const initials =
        currentDocument.estudiante
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(word =>
                word.charAt(0)
            )
            .join('')
            .toUpperCase();


    document.getElementById(
        'documentTitle'
    ).textContent =
        currentDocument.nombre;


    document.getElementById(
        'documentCategory'
    ).textContent =
        `${currentDocument.categoria.toUpperCase()} · DOCUMENTO`;


    document.getElementById(
        'documentName'
    ).textContent =
        currentDocument.nombre;


    document.getElementById(
        'documentVersion'
    ).textContent =
        `Versión ${currentDocument.version_actual}`;


    document.getElementById(
        'documentInitials'
    ).textContent =
        initials;


    document.getElementById(
        'documentStudent'
    ).textContent =
        currentDocument.estudiante;


    document.getElementById(
        'documentAccount'
    ).textContent =
        currentDocument.cuenta;


    document.getElementById(
        'documentCareer'
    ).textContent =
        currentDocument.carrera;


    document.getElementById(
        'documentFile'
    ).textContent =
        currentDocument.archivo;


    document.getElementById(
        'documentUploadDate'
    ).textContent =
        `Cargado: ${formatDocumentDate(currentDocument.fecha_carga)}`;


    const status =
        document.getElementById(
            'documentCurrentStatus'
        );


    status.textContent =
        currentDocument.estado;


    status.className =
        `coord-doc-status ${getStatusClass(currentDocument.estado)}`;


    const observationSection =
        document.getElementById(
            'documentObservationSection'
        );


    if (currentDocument.observacion_actual) {

        observationSection.classList.remove(
            'd-none'
        );


        document.getElementById(
            'documentObservation'
        ).textContent =
            currentDocument.observacion_actual;

    } else {

        observationSection.classList.add(
            'd-none'
        );

    }


    document.getElementById(
        'documentReviewActions'
    ).classList.toggle(
        'd-none',
        currentDocument.estado !== 'Pendiente'
    );


    renderVersions();

}


function approveDocument() {

    if (
        !currentDocument ||
        currentDocument.estado !== 'Pendiente'
    ) {
        return;
    }


    const confirmed =
        confirm(
            '¿Deseas aprobar esta versión del documento?'
        );


    if (!confirmed) {
        return;
    }


    currentDocument.estado =
        'Aprobado';


    currentDocument.observacion_actual =
        'Documento validado por Coordinación Académica.';


    const version =
        currentDocument.versiones.find(item =>
            item.version ===
            currentDocument.version_actual
        );


    if (version) {

        version.estado =
            'Aprobado';

        version.observacion =
            'Documento validado por Coordinación Académica.';

    }


    DocumentStore.saveDocument(
        currentDocument
    );


    renderDocumentDetail();

}


function requestDocumentCorrection() {

    if (
        !currentDocument ||
        currentDocument.estado !== 'Pendiente'
    ) {
        return;
    }


    const reason =
        document
            .getElementById(
                'documentCorrectionReason'
            )
            .value
            .trim();


    if (!reason) {

        alert(
            'Debes indicar qué debe corregir el estudiante.'
        );

        return;
    }


    currentDocument.estado =
        'Corrección requerida';


    currentDocument.observacion_actual =
        reason;


    const version =
        currentDocument.versiones.find(item =>
            item.version ===
            currentDocument.version_actual
        );


    if (version) {

        version.estado =
            'Corrección requerida';

        version.observacion =
            reason;

    }


    DocumentStore.saveDocument(
        currentDocument
    );


    bootstrap.Modal
        .getInstance(
            document.getElementById(
                'correctionModal'
            )
        )
        .hide();


    renderDocumentDetail();

}


function renderVersions() {

    const container =
        document.getElementById(
            'documentVersions'
        );


    const versions =
        [...currentDocument.versiones]
            .sort(
                (a, b) =>
                    b.version - a.version
            );


    container.innerHTML =
        versions.map(version => `

            <article class="coord-doc-version-row">

                <div class="coord-doc-version-number">
                    V${version.version}
                </div>


                <div>

                    <span>ARCHIVO</span>

                    <strong>
                        ${version.archivo}
                    </strong>

                    <small>
                        ${formatDocumentDate(version.fecha)}
                    </small>

                </div>


                <span
                    class="coord-doc-status ${getStatusClass(version.estado)}"
                >
                    ${version.estado}
                </span>


                <div class="coord-doc-version-observation">

                    <span>Observación</span>

                    <strong>
                        ${version.observacion || 'Sin observaciones'}
                    </strong>

                </div>

            </article>

        `).join('');

}


renderDocumentDetail();

</script>

@endpush