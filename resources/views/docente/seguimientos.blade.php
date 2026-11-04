@extends('layouts.teacher')

@section('title', 'Seguimientos | Portal del docente')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            ACOMPAÑAMIENTO ACADÉMICO
        </span>

        <h1>
            Seguimientos
        </h1>

        <p>
            Registra y consulta las actividades de acompañamiento
            realizadas con tus practicantes.
        </p>

    </div>


    <button
        type="button"
        class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#followupModal"
        onclick="openNewFollowup()"
    >
        <i class="bi bi-plus-lg"></i>
        Nuevo seguimiento
    </button>

</div>



<section class="row g-3 mb-4">

    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>

            <div>
                <span>Total</span>
                <strong id="followupTotal">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Realizados</span>
                <strong id="followupCompleted">0</strong>
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
                <strong id="followupPending">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <span>Practicantes</span>
                <strong id="followupStudents">0</strong>
            </div>

        </div>

    </div>

</section>



<section class="content-card">

    <div class="teacher-followup-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="followupSearch"
                placeholder="Buscar practicante o seguimiento..."
                oninput="renderFollowups()"
            >

        </div>


        <select
            id="followupStudentFilter"
            class="form-select"
            onchange="renderFollowups()"
        >
            <option value="">
                Todos los practicantes
            </option>

            <option value="Andrea Martínez">
                Andrea Martínez
            </option>

            <option value="María López">
                María López
            </option>

        </select>


        <select
            id="followupStatusFilter"
            class="form-select"
            onchange="renderFollowups()"
        >
            <option value="">
                Todos los estados
            </option>

            <option value="Pendiente">
                Pendientes
            </option>

            <option value="Realizado">
                Realizados
            </option>

        </select>

    </div>


    <div id="followupList"></div>

</section>



<!-- MODAL -->

<div
    class="modal fade"
    id="followupModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <span class="student-home-eyebrow">
                        SEGUIMIENTO
                    </span>

                    <h2
                        class="modal-title"
                        id="followupModalTitle"
                    >
                        Nuevo seguimiento
                    </h2>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <input
                    type="hidden"
                    id="followupId"
                >


                <div class="mb-3">

                    <label class="form-label">
                        Practicante *
                    </label>

                    <select
                        id="followupStudent"
                        class="form-select"
                    >

                        <option value="">
                            Selecciona un practicante
                        </option>

                        <option
                            value="Andrea Martínez"
                            data-id="1"
                            data-account="0801-2022-00458"
                        >
                            Andrea Martínez
                        </option>

                        <option
                            value="María López"
                            data-id="3"
                            data-account="0801-2022-00671"
                        >
                            María López
                        </option>

                    </select>

                </div>


                <div class="row g-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Fecha *
                        </label>

                        <input
                            type="date"
                            id="followupDate"
                            class="form-control"
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Tipo *
                        </label>

                        <select
                            id="followupType"
                            class="form-select"
                        >
                            <option value="Seguimiento">
                                Seguimiento
                            </option>

                            <option value="Revisión">
                                Revisión
                            </option>

                            <option value="Orientación">
                                Orientación
                            </option>

                            <option value="Comunicación">
                                Comunicación
                            </option>

                            <option value="Otro">
                                Otro
                            </option>
                        </select>

                    </div>

                </div>


                <div class="mt-3">

                    <label class="form-label">
                        Título *
                    </label>

                    <input
                        type="text"
                        id="followupTitle"
                        class="form-control"
                        placeholder="Ej. Revisión general del avance"
                    >

                </div>


                <div class="mt-3">

                    <label class="form-label">
                        Observación
                    </label>

                    <textarea
                        id="followupObservation"
                        class="form-control"
                        rows="4"
                        placeholder="Describe brevemente el seguimiento realizado..."
                    ></textarea>

                </div>


                <div class="mt-3">

                    <label class="form-label">
                        Estado *
                    </label>

                    <select
                        id="followupStatus"
                        class="form-select"
                    >

                        <option value="Pendiente">
                            Pendiente
                        </option>

                        <option value="Realizado">
                            Realizado
                        </option>

                    </select>

                </div>

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
                    class="btn btn-primary"
                    onclick="saveFollowup()"
                >
                    <i class="bi bi-check-lg"></i>
                    Guardar seguimiento
                </button>

            </div>

        </div>

    </div>

</div>


@endsection



@push('scripts')

<script src="{{ asset('js/followups-store.js') }}"></script>

<script>

const CURRENT_TEACHER =
    'Msc. Carlos Martínez';


function formatFollowupDate(date) {

    if (!date) {
        return 'Sin fecha';
    }


    return new Date(
        date + 'T12:00:00'
    ).toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

}


function getMyFollowups() {

    return FollowupStore
        .getAll()
        .filter(item =>
            item.docente === CURRENT_TEACHER
        );

}


function renderFollowupStats(records) {

    document.getElementById(
        'followupTotal'
    ).textContent =
        records.length;


    document.getElementById(
        'followupCompleted'
    ).textContent =
        records.filter(item =>
            item.estado === 'Realizado'
        ).length;


    document.getElementById(
        'followupPending'
    ).textContent =
        records.filter(item =>
            item.estado === 'Pendiente'
        ).length;


    document.getElementById(
        'followupStudents'
    ).textContent =
        new Set(
            records.map(item =>
                item.student_id
            )
        ).size;

}


function renderFollowups() {

    const records =
        getMyFollowups();


    renderFollowupStats(records);


    const search =
        document
            .getElementById(
                'followupSearch'
            )
            .value
            .trim()
            .toLowerCase();


    const student =
        document.getElementById(
            'followupStudentFilter'
        ).value;


    const status =
        document.getElementById(
            'followupStatusFilter'
        ).value;


    const filtered =
        records
            .filter(item => {

                const text =
                    `
                        ${item.estudiante}
                        ${item.cuenta}
                        ${item.tipo}
                        ${item.titulo}
                        ${item.observacion || ''}
                    `
                    .toLowerCase();


                return (
                    text.includes(search) &&
                    (!student ||
                        item.estudiante === student) &&
                    (!status ||
                        item.estado === status)
                );

            })
            .sort((a, b) =>
                b.fecha.localeCompare(a.fecha)
            );


    const container =
        document.getElementById(
            'followupList'
        );


    if (!filtered.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-clipboard-check"></i>

                <strong>
                    No se encontraron seguimientos
                </strong>

                <span>
                    No existen registros que coincidan
                    con los filtros seleccionados.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(item => {

            const initials =
                item.estudiante
                    .split(' ')
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(word =>
                        word.charAt(0)
                    )
                    .join('')
                    .toUpperCase();


            const statusClass =
                item.estado === 'Realizado'
                    ? 'completed'
                    : 'pending';


            return `

                <article class="teacher-followup-row">

                    <div class="coord-request-avatar">
                        ${initials}
                    </div>


                    <div class="teacher-followup-student">

                        <span>
                            PRACTICANTE
                        </span>

                        <strong>
                            ${item.estudiante}
                        </strong>

                        <small>
                            ${item.cuenta}
                        </small>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Seguimiento
                        </span>

                        <strong class="coord-request-value">
                            ${item.titulo}
                        </strong>

                        <small class="teacher-followup-type">
                            ${item.tipo}
                        </small>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Fecha
                        </span>

                        <strong class="coord-request-value">
                            ${formatFollowupDate(item.fecha)}
                        </strong>

                    </div>


                    <span
                        class="teacher-followup-status ${statusClass}"
                    >
                        ${item.estado}
                    </span>


                    <div class="teacher-followup-actions">

                        ${
                            item.estado === 'Pendiente'
                            ? `
                                <button
                                    type="button"
                                    title="Marcar como realizado"
                                    onclick="completeFollowup(${item.id})"
                                >
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            `
                            : ''
                        }


                        <button
                            type="button"
                            title="Editar"
                            onclick="editFollowup(${item.id})"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>


                        <button
                            type="button"
                            title="Eliminar"
                            onclick="deleteFollowup(${item.id})"
                        >
                            <i class="bi bi-trash"></i>
                        </button>

                    </div>

                </article>

                <div class="teacher-followup-observation">

                    <i class="bi bi-chat-left-text"></i>

                    <span>
                        ${item.observacion || 'Sin observaciones registradas.'}
                    </span>

                </div>

            `;

        }).join('');

}


function openNewFollowup() {

    document.getElementById(
        'followupModalTitle'
    ).textContent =
        'Nuevo seguimiento';


    document.getElementById(
        'followupId'
    ).value = '';


    document.getElementById(
        'followupStudent'
    ).value = '';


    document.getElementById(
        'followupDate'
    ).value =
        new Date()
            .toISOString()
            .slice(0, 10);


    document.getElementById(
        'followupType'
    ).value =
        'Seguimiento';


    document.getElementById(
        'followupTitle'
    ).value = '';


    document.getElementById(
        'followupObservation'
    ).value = '';


    document.getElementById(
        'followupStatus'
    ).value =
        'Pendiente';

}


function saveFollowup() {

    const id =
        document.getElementById(
            'followupId'
        ).value;


    const studentSelect =
        document.getElementById(
            'followupStudent'
        );


    const selectedOption =
        studentSelect.options[
            studentSelect.selectedIndex
        ];


    const estudiante =
        studentSelect.value;


    const fecha =
        document.getElementById(
            'followupDate'
        ).value;


    const tipo =
        document.getElementById(
            'followupType'
        ).value;


    const titulo =
        document.getElementById(
            'followupTitle'
        ).value.trim();


    const observacion =
        document.getElementById(
            'followupObservation'
        ).value.trim();


    const estado =
        document.getElementById(
            'followupStatus'
        ).value;


    if (
        !estudiante ||
        !fecha ||
        !titulo
    ) {

        alert(
            'Completa practicante, fecha y título.'
        );

        return;

    }


    const record = {

        student_id:
            Number(
                selectedOption.dataset.id
            ),

        estudiante,

        cuenta:
            selectedOption.dataset.account,

        docente:
            CURRENT_TEACHER,

        fecha,
        tipo,
        titulo,
        observacion,
        estado

    };


    if (id) {

        FollowupStore.update(
            Number(id),
            record
        );

    } else {

        FollowupStore.create(
            record
        );

    }


    bootstrap.Modal
        .getInstance(
            document.getElementById(
                'followupModal'
            )
        )
        .hide();


    renderFollowups();

}


function editFollowup(id) {

    const record =
        FollowupStore
            .getAll()
            .find(item =>
                String(item.id) ===
                String(id)
            );


    if (!record) {
        return;
    }


    document.getElementById(
        'followupModalTitle'
    ).textContent =
        'Editar seguimiento';


    document.getElementById(
        'followupId'
    ).value =
        record.id;


    document.getElementById(
        'followupStudent'
    ).value =
        record.estudiante;


    document.getElementById(
        'followupDate'
    ).value =
        record.fecha;


    document.getElementById(
        'followupType'
    ).value =
        record.tipo;


    document.getElementById(
        'followupTitle'
    ).value =
        record.titulo;


    document.getElementById(
        'followupObservation'
    ).value =
        record.observacion || '';


    document.getElementById(
        'followupStatus'
    ).value =
        record.estado;


    new bootstrap.Modal(
        document.getElementById(
            'followupModal'
        )
    ).show();

}


function completeFollowup(id) {

    FollowupStore.update(
        id,
        {
            estado: 'Realizado'
        }
    );


    renderFollowups();

}


function deleteFollowup(id) {

    const confirmation =
        confirm(
            '¿Deseas eliminar este seguimiento?'
        );


    if (!confirmation) {
        return;
    }


    FollowupStore.remove(id);

    renderFollowups();

}


renderFollowups();

</script>

@endpush