@extends('layouts.student')

@section('title', 'Defensa | UNICAH')

@section('content')


<!-- CABECERA -->

<div class="page-header">

    <div>

        <h1>
            Defensa
        </h1>

        <p>
            Consulta el estado de tu defensa y selecciona
            una fecha disponible cuando el proceso esté habilitado.
        </p>

    </div>


    <div class="page-date">

        <i class="bi bi-mortarboard"></i>

        Pendiente de calendarización

    </div>

</div>



<!-- ESTADO GENERAL -->

<section class="defense-overview-card">

    <div class="defense-overview-content">

        <span class="section-label">
            ESTADO DE DEFENSA
        </span>

        <h2>
            Calendarización pendiente
        </h2>

        <p>
            El sistema habilitará las opciones de fecha una vez
            que tu monografía haya sido aprobada en todas sus etapas.
        </p>

    </div>


    <div class="defense-overview-status">

        <span class="status-pill defense-pending">
            Pendiente
        </span>

    </div>

</section>



<!-- REQUISITOS -->

<section class="content-card defense-requirements-card">

    <div class="card-header">

        <div>

            <h2>
                Requisitos para calendarizar defensa
            </h2>

            <p>
                Todos los pasos deben estar aprobados antes de seleccionar una fecha.
            </p>

        </div>

    </div>


    <div class="defense-requirements-list">


        <div class="defense-requirement pending">

            <div class="requirement-icon">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <strong>
                    Revisión técnica
                </strong>

                <span>
                    En revisión
                </span>
            </div>

        </div>

        <div class="defense-requirement pending">

                <div class="requirement-icon">
                    <i class="bi bi-lock"></i>
                </div>

                <div>
                    <strong>
                        Revisión de estilo
                    </strong>

                    <span>
                        Pendiente
                    </span>
                </div>

            </div>
            

        <div class="defense-requirement pending">

            <div class="requirement-icon">

                <i class="bi bi-clock"></i>

            </div>

            <div>

                <strong>
                    Revisión del coordinador
                </strong>

                <span>
                    Pendiente
                </span>

            </div>

        </div>

    </div>

</section>



<!-- TERNA -->

<section class="content-card defense-panel-card">

    <div class="card-header">

        <div>

            <h2>
                Terna de defensa
            </h2>

            <p>
                Docentes asignados para tu proceso de defensa.
            </p>

        </div>

    </div>


    <div class="defense-jury-grid">


        <article class="jury-card">

            <div class="jury-avatar">
                CM
            </div>

            <div>

                <span>
                    Coordinador
                </span>

                <strong>
                    Msc. Carlos Martínez
                </strong>

                <p>
                    Coordinador de terna
                </p>

            </div>

        </article>



        <article class="jury-card">

            <div class="jury-avatar">
                AR
            </div>

            <div>

                <span>
                    Docente
                </span>

                <strong>
                    Ing. Ana Rodríguez
                </strong>

                <p>
                    Miembro de terna
                </p>

            </div>

        </article>



        <article class="jury-card">

            <div class="jury-avatar">
                JL
            </div>

            <div>

                <span>
                    Docente
                </span>

                <strong>
                    Msc. José López
                </strong>

                <p>
                    Miembro de terna
                </p>

            </div>

        </article>


    </div>

</section>



<!-- DISPONIBILIDAD BLOQUEADA -->

<section class="content-card defense-availability-card">

    <div class="defense-locked-state">

        <div class="defense-lock-large">

            <i class="bi bi-lock"></i>

        </div>

        <h3>
            Opciones de defensa aún no disponibles
        </h3>

        <p>
            Cuando la monografía sea aprobada por el coordinador,
            el sistema analizará la disponibilidad de los docentes
            de la terna y mostrará horarios compatibles.
        </p>


        <div class="defense-process-note">

            <i class="bi bi-info-circle"></i>

            <span>
                La disponibilidad se calculará a partir de la carga
                académica registrada por los docentes.
            </span>

        </div>

    </div>

</section>



<!-- EJEMPLO DE DISPONIBILIDAD -->

<section class="content-card defense-options-card demo-defense-options">

    <div class="card-header">

        <div>

            <h2>
                Ejemplo de opciones disponibles
            </h2>

            <p>
                Esta sección representa cómo se mostrarán los horarios
                cuando la defensa esté habilitada.
            </p>

        </div>

        <span class="demo-label">
            Vista demostrativa
        </span>

    </div>


    <div class="defense-options-list">


        <!-- OPCIÓN 1 -->

        <article class="defense-option">

            <div class="defense-date-box">
                <strong>22</strong>
                <span>NOV</span>
            </div>

            <div class="defense-option-info">

                <h3>
                    22 de noviembre de 2026
                </h3>

                <span>
                    <i class="bi bi-clock"></i>
                    10:00 a. m. - 11:00 a. m.
                </span>

                <span>
                    <i class="bi bi-people"></i>
                    Todos los miembros disponibles
                </span>

            </div>

            <button
                type="button"
                class="secondary-button defense-select-button"
                data-date="22 de noviembre de 2026"
                data-time="10:00 a. m. - 11:00 a. m."
            >
                Seleccionar
            </button>

        </article>



        <!-- OPCIÓN 2 -->

        <article class="defense-option recommended">

            <div class="defense-date-box">

                <strong>
                    23
                </strong>

                <span>
                    NOV
                </span>

            </div>


            <div class="defense-option-info">

                <div class="defense-option-title">

                    <h3>
                        23 de noviembre de 2026
                    </h3>

                    <span class="recommended-label">
                        Recomendada
                    </span>

                </div>


                <span>
                    <i class="bi bi-clock"></i>
                    2:00 p. m. - 3:00 p. m.
                </span>

                <span>
                    <i class="bi bi-people"></i>
                    Todos los miembros disponibles
                </span>

            </div>


            <button
                type="button"
                class="primary-button defense-select-button"
                data-date="23 de noviembre de 2026"
                data-time="2:00 p. m. - 3:00 p. m."
            >
                Seleccionar
            </button>

        </article>



        <!-- OPCIÓN 3 -->

        <article class="defense-option">

            <div class="defense-date-box">

                <strong>
                    25
                </strong>

                <span>
                    NOV
                </span>

            </div>


            <div class="defense-option-info">

                <h3>
                    25 de noviembre de 2026
                </h3>

                <span>
                    <i class="bi bi-clock"></i>
                    9:00 a. m. - 10:00 a. m.
                </span>

                <span>
                    <i class="bi bi-people"></i>
                    Todos los miembros disponibles
                </span>

            </div>


            <button
                type="button"
                class="secondary-button defense-select-button"
                data-date="25 de noviembre de 2026"
                data-time="9:00 a. m. - 10:00 a. m."
            >
                Seleccionar
            </button>

        </article>


    </div>

</section>



<!-- NOTA -->

<section class="defense-info-box">

    <div class="defense-info-box-icon">

        <i class="bi bi-calendar-week"></i>

    </div>

    <div>

        <strong>
            ¿Cómo se generan estas opciones?
        </strong>

        <p>
            El sistema cruza los horarios disponibles de los docentes
            que integran la terna con su carga académica registrada
            para el período correspondiente y genera opciones compatibles.
        </p>

    </div>

</section>



<!-- MODAL DE CONFIRMACIÓN -->

<div
    class="modal-overlay"
    id="defenseConfirmModal"
>

    <div class="modal-card defense-confirm-modal">


        <div class="modal-header">

            <div>

                <h2>
                    Confirmar defensa
                </h2>

                <p>
                    Verifica la fecha y hora seleccionadas.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                id="closeDefenseModal"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>


        <div class="modal-body">

            <div class="defense-confirm-summary">

                <div class="confirm-icon">

                    <i class="bi bi-mortarboard"></i>

                </div>


                <div>

                    <span>
                        Fecha seleccionada
                    </span>

                    <strong id="selectedDefenseDate">
                        -
                    </strong>


                    <span class="confirm-time-label">
                        Horario
                    </span>

                    <strong id="selectedDefenseTime">
                        -
                    </strong>

                </div>

            </div>


            <div class="confirm-warning">

                <i class="bi bi-exclamation-circle"></i>

                <p>
                    Al confirmar, esta opción quedará registrada
                    como tu horario de defensa.
                </p>

            </div>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="secondary-button"
                id="cancelDefenseModal"
            >
                Cancelar
            </button>


            <button
                type="button"
                class="primary-button"
            >
                <i class="bi bi-check-lg"></i>

                Confirmar horario
            </button>

        </div>


    </div>

</div>


@endsection


@push('scripts')

<script>

    const defenseButtons =
        document.querySelectorAll('.defense-select-button');

    const defenseModal =
        document.getElementById('defenseConfirmModal');

    const closeDefenseModal =
        document.getElementById('closeDefenseModal');

    const cancelDefenseModal =
        document.getElementById('cancelDefenseModal');

    const selectedDefenseDate =
        document.getElementById('selectedDefenseDate');

    const selectedDefenseTime =
        document.getElementById('selectedDefenseTime');


    function openDefenseModal(date, time) {

        selectedDefenseDate.textContent = date;

        selectedDefenseTime.textContent = time;

        defenseModal.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    function closeDefenseConfirmModal() {

        defenseModal.classList.remove('show');

        document.body.style.overflow = '';

    }


    defenseButtons.forEach(button => {

        button.addEventListener('click', function () {

            openDefenseModal(
                this.dataset.date,
                this.dataset.time
            );

        });

    });


    closeDefenseModal.addEventListener(
        'click',
        closeDefenseConfirmModal
    );


    cancelDefenseModal.addEventListener(
        'click',
        closeDefenseConfirmModal
    );


    defenseModal.addEventListener(
        'click',
        function(event) {

            if (event.target === defenseModal) {

                closeDefenseConfirmModal();

            }

        }
    );

</script>

@endpush