@extends('layouts.student')

@section('title', 'Monografía | UNICAH')

@section('content')


<!-- CABECERA -->

<div class="page-header">

    <div>

        <h1>
            Monografía
        </h1>

        <p>
            Consulta el estado de entrega y revisión
            de tu monografía de práctica profesional.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-journal-check"></i>

        En revisión

    </div>

</div>



<!-- RESUMEN -->

<section class="monograph-overview">

    <div>

        <span class="section-label">
            DOCUMENTO ACTUAL
        </span>

        <h2>
            Monografia_Andrea_v2.pdf
        </h2>

        <div class="monograph-meta">

            <span>
                <i class="bi bi-files"></i>
                Versión 2
            </span>

            <span>
                <i class="bi bi-calendar3"></i>
                Enviada 20/11/2026
            </span>

            <span>
                <i class="bi bi-file-earmark-pdf"></i>
                PDF
            </span>

        </div>

    </div>


    <div class="monograph-overview-actions">

        <span class="status-pill monograph-review">
            En revisión técnica
        </span>

        <button class="secondary-button">

            <i class="bi bi-eye"></i>

            Ver documento

        </button>

    </div>

</section>



<!-- PROCESO DE REVISIÓN -->

<section class="content-card monograph-process-card">

    <div class="card-header">

        <div>

            <h2>
                Proceso de revisión
            </h2>

            <p>
                Seguimiento de las etapas necesarias
                antes de calendarizar tu defensa.
            </p>

        </div>

    </div>


    <div class="monograph-review-flow">


        <!-- ENTREGA -->

        <article class="review-step completed">

            <div class="review-step-indicator">

                <div class="review-circle">
                    <i class="bi bi-check-lg"></i>
                </div>

                <div class="review-line"></div>

            </div>


            <div class="review-step-content">

                <div class="review-step-header">

                    <div>

                        <span class="review-number">
                            ETAPA 1
                        </span>

                        <h3>
                            Entrega de monografía
                        </h3>

                    </div>

                    <span class="badge success">
                        Completada
                    </span>

                </div>


                <p>
                    La versión actual fue enviada correctamente.
                </p>


                <div class="review-details">

                    <span>
                        <i class="bi bi-calendar3"></i>
                        20/11/2026
                    </span>

                    <span>
                        <i class="bi bi-file-earmark"></i>
                        Versión 2
                    </span>

                </div>

            </div>

        </article>



        <!-- REVISIÓN TÉCNICA -->

        <article class="review-step active">

            <div class="review-step-indicator">

                <div class="review-circle">
                    <i class="bi bi-tools"></i>
                </div>

                <div class="review-line"></div>

            </div>


            <div class="review-step-content">

                <div class="review-step-header">

                    <div>

                        <span class="review-number">
                            ETAPA 2
                        </span>

                        <h3>
                            Revisión técnica
                        </h3>

                    </div>

                    <span class="badge warning">
                        En revisión
                    </span>

                </div>


                <p>
                    El documento está siendo revisado
                    en aspectos técnicos y académicos.
                </p>


                <div class="review-time-box">

                    <div>

                        <span>
                            Tiempo máximo de revisión
                        </span>

                        <strong>
                            10 días
                        </strong>

                    </div>


                    <div>

                        <span>
                            Tiempo restante
                        </span>

                        <strong class="review-time-current">
                            7 días
                        </strong>

                    </div>

                </div>

            </div>

        </article>



        <!-- ESTILO -->

        <article class="review-step pending">

            <div class="review-step-indicator">

                <div class="review-circle">
                    <i class="bi bi-brush"></i>
                </div>

                <div class="review-line"></div>

            </div>


            <div class="review-step-content">

                <div class="review-step-header">

                    <div>

                        <span class="review-number">
                            ETAPA 3
                        </span>

                        <h3>
                            Revisión de estilo
                        </h3>

                    </div>

                    <span class="badge info">
                        Pendiente
                    </span>

                </div>


                <p>
                    Se habilitará cuando la revisión técnica
                    haya sido aprobada.
                </p>


                <div class="review-details">

                    <span>
                        <i class="bi bi-clock"></i>
                        Plazo máximo: 3 días
                    </span>

                </div>

            </div>

        </article>



        <!-- COORDINADOR -->

        <article class="review-step pending last">

            <div class="review-step-indicator">

                <div class="review-circle">
                    <i class="bi bi-person-check"></i>
                </div>

            </div>


            <div class="review-step-content">

                <div class="review-step-header">

                    <div>

                        <span class="review-number">
                            ETAPA 4
                        </span>

                        <h3>
                            Revisión del coordinador
                        </h3>

                    </div>

                    <span class="badge info">
                        Pendiente
                    </span>

                </div>


                <p>
                    Revisión final realizada por el coordinador
                    de la terna de defensa.
                </p>


                <div class="review-details">

                    <span>
                        <i class="bi bi-clock"></i>
                        Plazo máximo: 1 día
                    </span>

                </div>

            </div>

        </article>


    </div>

</section>



<!-- REGLAS -->

<section class="monograph-info-grid">


    <article class="content-card monograph-info-card">

        <div class="monograph-info-icon gold">

            <i class="bi bi-calendar-event"></i>

        </div>


        <div>

            <span>
                Fecha límite de entrega
            </span>

            <strong>
                23 de noviembre de 2026
            </strong>

            <p>
                La monografía debe entregarse una semana
                antes de finalizar la práctica.
            </p>

        </div>

    </article>



    <article class="content-card monograph-info-card">

        <div class="monograph-info-icon blue">

            <i class="bi bi-arrow-repeat"></i>

        </div>


        <div>

            <span>
                En caso de rechazo
            </span>

            <strong>
                Debes enviar una nueva versión
            </strong>

            <p>
                El plazo de revisión correspondiente
                comenzará nuevamente.
            </p>

        </div>

    </article>


</section>



<!-- HISTORIAL DE VERSIONES -->

<section class="content-card monograph-history-card">

    <div class="card-header">

        <div>

            <h2>
                Historial de versiones
            </h2>

            <p>
                Registro de documentos enviados y
                observaciones recibidas.
            </p>

        </div>


        <button
            type="button"
            class="primary-button"
            id="openMonographUpload"
        >

            <i class="bi bi-cloud-arrow-up"></i>

            Nueva versión

        </button>

    </div>



    <div class="monograph-version-list">


        <!-- VERSION 2 -->

        <article class="monograph-version current">

            <div class="version-number">
                V2
            </div>


            <div class="version-content">

                <div class="version-header">

                    <div>

                        <strong>
                            Monografia_Andrea_v2.pdf
                        </strong>

                        <span>
                            Enviada 20/11/2026
                        </span>

                    </div>


                    <span class="badge warning">
                        En revisión
                    </span>

                </div>


                <p>
                    Versión corregida posterior a las
                    observaciones de la primera revisión.
                </p>


                <button class="version-link">
                    <i class="bi bi-eye"></i>
                    Ver archivo
                </button>

            </div>

        </article>



        <!-- VERSION 1 -->

        <article class="monograph-version rejected">

            <div class="version-number">
                V1
            </div>


            <div class="version-content">

                <div class="version-header">

                    <div>

                        <strong>
                            Monografia_Andrea_v1.pdf
                        </strong>

                        <span>
                            Enviada 15/11/2026
                        </span>

                    </div>


                    <span class="badge danger">
                        Rechazada
                    </span>

                </div>


                <div class="version-observation">

                    <i class="bi bi-exclamation-triangle"></i>

                    <div>

                        <strong>
                            Observación de revisión técnica
                        </strong>

                        <p>
                            Corregir la metodología y revisar
                            el formato de las referencias bibliográficas.
                        </p>

                    </div>

                </div>


                <button class="version-link">
                    <i class="bi bi-eye"></i>
                    Ver archivo anterior
                </button>

            </div>

        </article>


    </div>

</section>



<!-- MENSAJE DEFENSA -->

<section class="defense-lock-card">

    <div class="defense-lock-icon">

        <i class="bi bi-lock"></i>

    </div>


    <div>

        <strong>
            Calendarización de defensa no disponible
        </strong>

        <p>
            La selección de fecha para tu defensa se habilitará
            cuando la revisión técnica, de estilo y del coordinador
            hayan sido aprobadas.
        </p>

    </div>

</section>



<!-- MODAL NUEVA VERSIÓN -->

<div
    class="modal-overlay"
    id="monographUploadModal"
>

    <div class="modal-card document-upload-modal">


        <div class="modal-header">

            <div>

                <h2>
                    Subir nueva versión
                </h2>

                <p>
                    Selecciona el documento corregido.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                id="closeMonographUpload"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>



        <div class="modal-body">

            <div class="upload-area">

                <div class="upload-area-icon">

                    <i class="bi bi-journal-arrow-up"></i>

                </div>


                <h3>
                    Selecciona tu monografía
                </h3>

                <p>
                    Documento en formato PDF
                </p>


                <input
                    type="file"
                    id="monographFile"
                    accept=".pdf"
                    hidden
                >


                <button
                    type="button"
                    class="secondary-button"
                    id="selectMonographFile"
                >
                    <i class="bi bi-folder2-open"></i>

                    Examinar archivo
                </button>


                <span
                    class="selected-file-name"
                    id="monographFileName"
                >
                    Ningún archivo seleccionado
                </span>

            </div>

        </div>



        <div class="modal-footer">

            <button
                type="button"
                class="secondary-button"
                id="cancelMonographUpload"
            >
                Cancelar
            </button>


            <button
                type="button"
                class="primary-button"
            >
                <i class="bi bi-cloud-arrow-up"></i>

                Enviar versión
            </button>

        </div>

    </div>

</div>


@endsection


@push('scripts')

<script>

    const monographModal =
        document.getElementById('monographUploadModal');

    const openMonographUpload =
        document.getElementById('openMonographUpload');

    const closeMonographUpload =
        document.getElementById('closeMonographUpload');

    const cancelMonographUpload =
        document.getElementById('cancelMonographUpload');

    const selectMonographFile =
        document.getElementById('selectMonographFile');

    const monographFile =
        document.getElementById('monographFile');

    const monographFileName =
        document.getElementById('monographFileName');


    function openMonographModal() {

        monographModal.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    function closeMonographModal() {

        monographModal.classList.remove('show');

        document.body.style.overflow = '';

        monographFile.value = '';

        monographFileName.textContent =
            'Ningún archivo seleccionado';

    }


    openMonographUpload.addEventListener(
        'click',
        openMonographModal
    );


    closeMonographUpload.addEventListener(
        'click',
        closeMonographModal
    );


    cancelMonographUpload.addEventListener(
        'click',
        closeMonographModal
    );


    monographModal.addEventListener(
        'click',
        function(event) {

            if (event.target === monographModal) {

                closeMonographModal();

            }

        }
    );


    selectMonographFile.addEventListener(
        'click',
        function() {

            monographFile.click();

        }
    );


    monographFile.addEventListener(
        'change',
        function() {

            if (this.files.length > 0) {

                monographFileName.textContent =
                    this.files[0].name;

            }

        }
    );

</script>

@endpush