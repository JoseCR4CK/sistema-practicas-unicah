@extends('layouts.app')

@section('title', 'Documentos | Coordinación Académica')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            CONTROL DOCUMENTAL
        </span>

        <h1>
            Documentos
        </h1>

        <p>
            Revisa los documentos cargados durante
            los procesos de práctica profesional.
        </p>

    </div>

</div>



<section class="row g-3 mb-4">

    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-files"></i>
            </div>

            <div>
                <span>Total</span>
                <strong id="docStatTotal">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <span>Pendientes</span>
                <strong id="docStatPending">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Aprobados</span>
                <strong id="docStatApproved">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>

            <div>
                <span>Corrección requerida</span>
                <strong id="docStatCorrection">0</strong>
            </div>

        </div>

    </div>

</section>



<section class="content-card">

    <div class="coord-doc-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="docSearch"
                placeholder="Buscar estudiante, cuenta o documento..."
                oninput="renderDocuments()"
            >

        </div>


        <select
            id="docCategory"
            class="form-select"
            onchange="renderDocuments()"
        >

            <option value="">
                Todas las categorías
            </option>

            <option value="Inicial">
                Documentos iniciales
            </option>

            <option value="Proceso">
                Documentos del proceso
            </option>

            <option value="Final">
                Documentos finales
            </option>

        </select>


        <select
            id="docStatus"
            class="form-select"
            onchange="renderDocuments()"
        >

            <option value="">
                Todos los estados
            </option>

            <option value="Pendiente">
                Pendientes
            </option>

            <option value="Aprobado">
                Aprobados
            </option>

            <option value="Corrección requerida">
                Corrección requerida
            </option>

            <option value="Rechazado">
                Rechazados
            </option>

        </select>

    </div>


    <div id="documentsList"></div>

</section>

@endsection



@push('scripts')

<script src="{{ asset('js/documents-store.js') }}"></script>

<script>

function formatDocumentDate(date) {

    if (!date) return '—';


    return new Date(date)
        .toLocaleDateString(
            'es-HN',
            {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }
        );

}


function renderDocumentStats(documents) {

    document.getElementById(
        'docStatTotal'
    ).textContent =
        documents.length;


    document.getElementById(
        'docStatPending'
    ).textContent =
        documents.filter(item =>
            item.estado === 'Pendiente'
        ).length;


    document.getElementById(
        'docStatApproved'
    ).textContent =
        documents.filter(item =>
            item.estado === 'Aprobado'
        ).length;


    document.getElementById(
        'docStatCorrection'
    ).textContent =
        documents.filter(item =>
            item.estado ===
            'Corrección requerida'
        ).length;

}


function getDocumentStatusClass(status) {

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


function renderDocuments() {

    const documents =
        DocumentStore.getAll();


    renderDocumentStats(documents);


    const search =
        document
            .getElementById('docSearch')
            .value
            .toLowerCase()
            .trim();


    const category =
        document.getElementById(
            'docCategory'
        ).value;


    const status =
        document.getElementById(
            'docStatus'
        ).value;


    const filtered =
        documents.filter(document => {

            const searchable =
                `
                    ${document.estudiante}
                    ${document.cuenta}
                    ${document.nombre}
                `
                .toLowerCase();


            return (
                searchable.includes(search) &&
                (!category ||
                    document.categoria === category) &&
                (!status ||
                    document.estado === status)
            );

        });


    const container =
        document.getElementById(
            'documentsList'
        );


    if (!filtered.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-folder2-open"></i>

                <strong>
                    No se encontraron documentos
                </strong>

                <span>
                    Cambia los criterios de búsqueda o filtrado.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(document => {

            return `

                <article class="coord-doc-row">

                    <div class="coord-doc-icon">

                        <i class="bi bi-file-earmark-text"></i>

                    </div>


                    <div class="coord-doc-main">

                        <span>
                            ${document.categoria.toUpperCase()}
                        </span>

                        <strong>
                            ${document.nombre}
                        </strong>

                        <small>
                            Versión ${document.version_actual}
                        </small>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Estudiante
                        </span>

                        <strong class="coord-request-value">
                            ${document.estudiante}
                        </strong>

                        <small class="coord-doc-small">
                            ${document.cuenta}
                        </small>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Fecha de carga
                        </span>

                        <strong class="coord-request-value">
                            ${formatDocumentDate(
                                document.fecha_carga
                            )}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Archivo
                        </span>

                        <strong class="coord-doc-file">
                            ${document.archivo}
                        </strong>

                    </div>


                    <span
                        class="coord-doc-status ${getDocumentStatusClass(document.estado)}"
                    >
                        ${document.estado}
                    </span>


                    <a
                        href="/coordinacion/documentos/${document.id}"
                        class="coord-teacher-view"
                    >
                        <i class="bi bi-eye"></i>
                        Revisar
                    </a>

                </article>

            `;

        }).join('');

}


renderDocuments();

</script>

@endpush