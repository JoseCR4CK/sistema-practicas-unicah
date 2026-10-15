@extends('layouts.student')

@section('title', 'Documentos | UNICAH')

@section('content')


<!-- ===============================
     CABECERA
================================ -->

<div class="page-header">

    <div>

        <h1>
            Documentos
        </h1>

        <p>
            Consulta, carga y da seguimiento a los documentos
            requeridos para tu práctica profesional.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-folder-check"></i>

        3 de 4 completados

    </div>

</div>



<!-- ===============================
     RESUMEN
================================ -->

<section class="documents-summary-grid">


    <article class="documents-summary-card">

        <div class="documents-summary-icon green">

            <i class="bi bi-check-circle"></i>

        </div>

        <div>

            <span>
                Aprobados
            </span>

            <strong>
                2
            </strong>

        </div>

    </article>



    <article class="documents-summary-card">

        <div class="documents-summary-icon gold">

            <i class="bi bi-clock-history"></i>

        </div>

        <div>

            <span>
                Pendientes
            </span>

            <strong>
                1
            </strong>

        </div>

    </article>



    <article class="documents-summary-card">

        <div class="documents-summary-icon red">

            <i class="bi bi-x-circle"></i>

        </div>

        <div>

            <span>
                Rechazados
            </span>

            <strong>
                1
            </strong>

        </div>

    </article>



    <article class="documents-summary-card">

        <div class="documents-summary-icon blue">

            <i class="bi bi-files"></i>

        </div>

        <div>

            <span>
                Total
            </span>

            <strong>
                4
            </strong>

        </div>

    </article>


</section>



<!-- ===============================
     PROGRESO DOCUMENTAL
================================ -->

<section class="content-card documents-progress-card">

    <div class="documents-progress-header">

        <div>

            <h2>
                Progreso documental
            </h2>

            <p>
                Completa los documentos requeridos para continuar
                con tu proceso.
            </p>

        </div>


        <strong>
            75%
        </strong>

    </div>


    <div class="documents-progress-bar">

        <div
            class="documents-progress-fill"
            style="width: 75%;"
        ></div>

    </div>

</section>



<!-- ===============================
     LISTADO DE DOCUMENTOS
================================ -->

<section class="content-card documents-list-card">


    <div class="card-header">

        <div>

            <h2>
                Documentos requeridos
            </h2>

            <p>
                Revisa el estado de cada documento presentado.
            </p>

        </div>

    </div>



    <div class="documents-management-list">


        <!-- DOCUMENTO APROBADO -->

        <article class="document-management-card approved">


            <div class="document-main-info">

                <div class="document-status-icon">

                    <i class="bi bi-file-earmark-check"></i>

                </div>


                <div class="document-text">

                    <div class="document-title-row">

                        <h3>
                            Documento de identidad
                        </h3>

                        <span class="badge success">
                            Aprobado
                        </span>

                    </div>


                    <p>
                        Documento de identidad del estudiante.
                    </p>


                    <div class="document-file-info">

                        <span>
                            <i class="bi bi-file-earmark-pdf"></i>
                            identidad_andrea.pdf
                        </span>

                        <span>
                            <i class="bi bi-calendar3"></i>
                            Enviado 03/09/2026
                        </span>

                    </div>

                </div>

            </div>


            <div class="document-actions">

                <button
                    type="button"
                    class="secondary-button"
                >
                    <i class="bi bi-eye"></i>
                    Ver archivo
                </button>

            </div>


        </article>



        <!-- DOCUMENTO APROBADO -->

        <article class="document-management-card approved">


            <div class="document-main-info">

                <div class="document-status-icon">

                    <i class="bi bi-file-earmark-check"></i>

                </div>


                <div class="document-text">

                    <div class="document-title-row">

                        <h3>
                            Documento testigo 1
                        </h3>

                        <span class="badge success">
                            Aprobado
                        </span>

                    </div>


                    <p>
                        Documento de identidad correspondiente
                        al primer testigo.
                    </p>


                    <div class="document-file-info">

                        <span>
                            <i class="bi bi-file-earmark-pdf"></i>
                            testigo_1.pdf
                        </span>

                        <span>
                            <i class="bi bi-calendar3"></i>
                            Enviado 03/09/2026
                        </span>

                    </div>

                </div>

            </div>


            <div class="document-actions">

                <button
                    type="button"
                    class="secondary-button"
                >
                    <i class="bi bi-eye"></i>
                    Ver archivo
                </button>

            </div>


        </article>



        <!-- DOCUMENTO PENDIENTE -->

        <article class="document-management-card pending">


            <div class="document-main-info">

                <div class="document-status-icon">

                    <i class="bi bi-file-earmark-arrow-up"></i>

                </div>


                <div class="document-text">

                    <div class="document-title-row">

                        <h3>
                            Documento testigo 2
                        </h3>

                        <span class="badge warning">
                            Pendiente
                        </span>

                    </div>


                    <p>
                        Aún no has cargado este documento.
                    </p>


                    <div class="document-help-text">

                        Formatos permitidos:
                        PDF, JPG o PNG.
                    </div>

                </div>

            </div>


            <div class="document-actions">

                <button
                    type="button"
                    class="primary-button"
                    data-upload="testigo2"
                >
                    <i class="bi bi-cloud-arrow-up"></i>
                    Subir documento
                </button>

            </div>


        </article>



        <!-- DOCUMENTO RECHAZADO -->

        <article class="document-management-card rejected">


            <div class="document-main-info">

                <div class="document-status-icon">

                    <i class="bi bi-file-earmark-x"></i>

                </div>


                <div class="document-text">

                    <div class="document-title-row">

                        <h3>
                            Solvencia / documento adicional
                        </h3>

                        <span class="badge danger">
                            Rechazado
                        </span>

                    </div>


                    <p>
                        El documento necesita ser corregido
                        antes de continuar.
                    </p>


                    <div class="document-file-info">

                        <span>
                            <i class="bi bi-file-earmark-pdf"></i>
                            solvencia.pdf
                        </span>

                        <span>
                            <i class="bi bi-calendar3"></i>
                            Revisado 10/10/2026
                        </span>

                    </div>


                    <div class="document-observation">

                        <i class="bi bi-exclamation-triangle"></i>

                        <div>

                            <strong>
                                Observación de Coordinación
                            </strong>

                            <p>
                                El documento presenta baja calidad
                                de imagen. Favor cargar una versión
                                más legible.
                            </p>

                        </div>

                    </div>

                </div>

            </div>


            <div class="document-actions">

                <button
                    type="button"
                    class="secondary-button"
                >
                    <i class="bi bi-eye"></i>
                    Ver archivo
                </button>


                <button
                    type="button"
                    class="primary-button"
                    data-upload="solvencia"
                >
                    <i class="bi bi-arrow-repeat"></i>
                    Reemplazar
                </button>

            </div>


        </article>


    </div>


</section>



<!-- ===============================
     INFORMACIÓN IMPORTANTE
================================ -->

<section class="documents-info-box">

    <div class="documents-info-icon">

        <i class="bi bi-info-circle"></i>

    </div>


    <div>

        <strong>
            Información importante
        </strong>

        <p>
            Los documentos enviados serán revisados por Coordinación
            Académica. Si alguno es rechazado, podrás consultar la
            observación correspondiente y enviar una nueva versión.
        </p>

    </div>

</section>



<!-- ===============================
     MODAL DE CARGA
================================ -->

<div
    class="modal-overlay"
    id="uploadDocumentModal"
>

    <div class="modal-card document-upload-modal">


        <div class="modal-header">

            <div>

                <h2>
                    Subir documento
                </h2>

                <p>
                    Selecciona el archivo que deseas enviar.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                id="closeUploadModal"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>



        <div class="modal-body">


            <div class="upload-area">


                <div class="upload-area-icon">

                    <i class="bi bi-cloud-arrow-up"></i>

                </div>


                <h3>
                    Selecciona un archivo
                </h3>


                <p>
                    PDF, JPG o PNG
                </p>


                <input
                    type="file"
                    id="documentFile"
                    accept=".pdf,.jpg,.jpeg,.png"
                    hidden
                >


                <button
                    type="button"
                    class="secondary-button"
                    id="selectFileButton"
                >
                    <i class="bi bi-folder2-open"></i>
                    Examinar archivo
                </button>


                <span
                    class="selected-file-name"
                    id="selectedFileName"
                >
                    Ningún archivo seleccionado
                </span>


            </div>


        </div>



        <div class="modal-footer">


            <button
                type="button"
                class="secondary-button"
                id="cancelUploadModal"
            >
                Cancelar
            </button>


            <button
                type="button"
                class="primary-button"
            >
                <i class="bi bi-cloud-arrow-up"></i>
                Enviar documento
            </button>


        </div>


    </div>

</div>


@endsection



@push('scripts')

<script>

    const uploadModal =
        document.getElementById('uploadDocumentModal');

    const uploadButtons =
        document.querySelectorAll('[data-upload]');

    const closeUploadModal =
        document.getElementById('closeUploadModal');

    const cancelUploadModal =
        document.getElementById('cancelUploadModal');

    const selectFileButton =
        document.getElementById('selectFileButton');

    const documentFile =
        document.getElementById('documentFile');

    const selectedFileName =
        document.getElementById('selectedFileName');


    function openDocumentModal() {

        uploadModal.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    function closeDocumentModal() {

        uploadModal.classList.remove('show');

        document.body.style.overflow = '';

        documentFile.value = '';

        selectedFileName.textContent =
            'Ningún archivo seleccionado';

    }


    uploadButtons.forEach(button => {

        button.addEventListener(
            'click',
            openDocumentModal
        );

    });


    closeUploadModal.addEventListener(
        'click',
        closeDocumentModal
    );


    cancelUploadModal.addEventListener(
        'click',
        closeDocumentModal
    );


    uploadModal.addEventListener(
        'click',
        function(event) {

            if (event.target === uploadModal) {

                closeDocumentModal();

            }

        }
    );


    selectFileButton.addEventListener(
        'click',
        function() {

            documentFile.click();

        }
    );


    documentFile.addEventListener(
        'change',
        function() {

            if (this.files.length > 0) {

                selectedFileName.textContent =
                    this.files[0].name;

            }

        }
    );

</script>

@endpush