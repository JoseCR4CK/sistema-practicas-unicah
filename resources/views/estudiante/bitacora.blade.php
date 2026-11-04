@extends('layouts.student')

@section('title', 'Mi bitácora | UNICAH')

@section('content')

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            MI ESPACIO
        </span>

        <h1>
            Mi bitácora
        </h1>

        <p>
            Registra las actividades, experiencias y aprendizajes
            obtenidos durante tu práctica profesional.
        </p>

    </div>

    <button
        type="button"
        class="log-new-button"
        data-bs-toggle="modal"
        data-bs-target="#logModal"
        onclick="prepareNewLog()"
    >
        <i class="bi bi-plus-lg"></i>
        Registrar actividad
    </button>

</div>


<!-- INFORMACIÓN -->

<div class="log-info-box">

    <div class="log-info-icon">
        <i class="bi bi-journal-check"></i>
    </div>

    <div>

        <strong>
            Tu registro personal de práctica
        </strong>

        <p>
            La bitácora te permite recordar qué actividades realizaste,
            qué aprendiste y qué situaciones importantes ocurrieron
            durante tu experiencia profesional.
        </p>

    </div>

</div>


<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="log-stat-card">

            <div class="log-stat-icon blue">
                <i class="bi bi-journal-text"></i>
            </div>

            <div>
                <span>Actividades registradas</span>
                <strong id="totalLogs">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="log-stat-card">

            <div class="log-stat-icon green">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>
                <span>Días con registro</span>
                <strong id="registeredDays">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="log-stat-card">

            <div class="log-stat-icon gold">
                <i class="bi bi-clock-history"></i>
            </div>

            <div>
                <span>Último registro</span>

                <strong
                    id="lastLogDate"
                    class="log-stat-date"
                >
                    Sin actividad
                </strong>
            </div>

        </div>

    </div>

</section>


<!-- BUSCADOR -->

<section class="log-toolbar">

    <div class="log-search">

        <i class="bi bi-search"></i>

        <input
            type="text"
            id="logSearch"
            placeholder="Buscar en mi bitácora..."
            oninput="renderLogs()"
        >

    </div>

    <select
        id="logCategoryFilter"
        class="log-filter"
        onchange="renderLogs()"
    >

        <option value="all">
            Todas las actividades
        </option>

        <option value="Trabajo">
            Trabajo
        </option>

        <option value="Reunión">
            Reunión
        </option>

        <option value="Capacitación">
            Capacitación
        </option>

        <option value="Investigación">
            Investigación
        </option>

        <option value="Otro">
            Otro
        </option>

    </select>

</section>


<!-- LISTADO -->

<section
    id="logsContainer"
    class="log-timeline"
>
</section>


<!-- VACÍO -->

<section
    id="logsEmpty"
    class="log-empty"
    style="display:none;"
>

    <div class="log-empty-icon">
        <i class="bi bi-journal-text"></i>
    </div>

    <h2>
        Tu bitácora está vacía
    </h2>

    <p>
        Empieza registrando alguna actividad realizada
        durante tu práctica profesional.
    </p>

    <button
        type="button"
        class="log-new-button"
        data-bs-toggle="modal"
        data-bs-target="#logModal"
        onclick="prepareNewLog()"
    >
        <i class="bi bi-plus-lg"></i>
        Registrar primera actividad
    </button>

</section>


<!-- MODAL -->

<div
    class="modal fade"
    id="logModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content log-modal">

            <div class="modal-header">

                <div>

                    <span class="section-label">
                        MI BITÁCORA
                    </span>

                    <h2
                        class="modal-title"
                        id="logModalTitle"
                    >
                        Registrar actividad
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
                    id="logId"
                >


                <div class="row g-3">

                    <div class="col-12 col-md-6">

                        <div class="log-form-group">

                            <label for="logDate">
                                Fecha
                            </label>

                            <input
                                type="date"
                                id="logDate"
                            >

                        </div>

                    </div>


                    <div class="col-12 col-md-6">

                        <div class="log-form-group">

                            <label for="logCategory">
                                Tipo de actividad
                            </label>

                            <select id="logCategory">

                                <option value="Trabajo">
                                    Trabajo
                                </option>

                                <option value="Reunión">
                                    Reunión
                                </option>

                                <option value="Capacitación">
                                    Capacitación
                                </option>

                                <option value="Investigación">
                                    Investigación
                                </option>

                                <option value="Otro">
                                    Otro
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="log-form-group">

                    <label for="logTitle">
                        Actividad realizada
                    </label>

                    <input
                        type="text"
                        id="logTitle"
                        maxlength="120"
                        placeholder="Ej. Revisión del módulo de inventario"
                    >

                </div>


                <div class="log-form-group">

                    <label for="logDescription">
                        Descripción
                    </label>

                    <textarea
                        id="logDescription"
                        rows="5"
                        maxlength="1200"
                        placeholder="Describe brevemente lo que realizaste..."
                    ></textarea>

                </div>


                <div class="log-form-group">

                    <label for="logLearning">
                        Aprendizaje u observación
                    </label>

                    <textarea
                        id="logLearning"
                        rows="3"
                        maxlength="700"
                        placeholder="¿Qué aprendiste o qué consideras importante recordar?"
                    ></textarea>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="log-secondary-button"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="log-save-button"
                    onclick="saveLog()"
                >
                    <i class="bi bi-check2"></i>
                    Guardar registro
                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

const LOG_STORAGE_KEY =
    'unicah_practica_0801202200458_logs';

let logs =
    JSON.parse(
        localStorage.getItem(LOG_STORAGE_KEY)
    ) || [];


/* PERSISTENCIA */

function persistLogs() {

    localStorage.setItem(
        LOG_STORAGE_KEY,
        JSON.stringify(logs)
    );

}


/* NUEVO */

function prepareNewLog() {

    document.getElementById('logId').value = '';

    document.getElementById('logDate').value =
        new Date().toISOString().split('T')[0];

    document.getElementById('logCategory').value =
        'Trabajo';

    document.getElementById('logTitle').value = '';

    document.getElementById('logDescription').value = '';

    document.getElementById('logLearning').value = '';

    document.getElementById('logModalTitle').textContent =
        'Registrar actividad';

}


/* GUARDAR */

function saveLog() {

    const id =
        document.getElementById('logId').value;

    const date =
        document.getElementById('logDate').value;

    const category =
        document.getElementById('logCategory').value;

    const title =
        document.getElementById('logTitle')
            .value
            .trim();

    const description =
        document.getElementById('logDescription')
            .value
            .trim();

    const learning =
        document.getElementById('logLearning')
            .value
            .trim();


    if (!date) {

        alert('Selecciona la fecha de la actividad.');

        return;

    }


    if (!title) {

        alert('Escribe el nombre de la actividad.');

        return;

    }


    if (!description) {

        alert('Describe la actividad realizada.');

        return;

    }


    const now =
        new Date().toISOString();


    if (id) {

        const index =
            logs.findIndex(
                log => log.id === id
            );


        if (index !== -1) {

            logs[index] = {

                ...logs[index],

                date,

                category,

                title,

                description,

                learning,

                updatedAt: now

            };

        }

    }
    else {

        logs.push({

            id:
                'log_' +
                Date.now(),

            date,

            category,

            title,

            description,

            learning,

            createdAt: now,

            updatedAt: now

        });

    }


    persistLogs();

    renderLogs();


    bootstrap.Modal
        .getInstance(
            document.getElementById('logModal')
        )
        .hide();

}


/* EDITAR */

function editLog(id) {

    const log =
        logs.find(
            log => log.id === id
        );


    if (!log) {
        return;
    }


    document.getElementById('logId').value =
        log.id;

    document.getElementById('logDate').value =
        log.date;

    document.getElementById('logCategory').value =
        log.category;

    document.getElementById('logTitle').value =
        log.title;

    document.getElementById('logDescription').value =
        log.description;

    document.getElementById('logLearning').value =
        log.learning || '';


    document.getElementById(
        'logModalTitle'
    ).textContent =
        'Editar actividad';


    new bootstrap.Modal(
        document.getElementById('logModal')
    ).show();

}


/* ELIMINAR */

function deleteLog(id) {

    const log =
        logs.find(
            log => log.id === id
        );


    if (!log) {
        return;
    }


    const confirmed =
        confirm(
            '¿Deseas eliminar el registro "' +
            log.title +
            '"?'
        );


    if (!confirmed) {
        return;
    }


    logs =
        logs.filter(
            log => log.id !== id
        );


    persistLogs();

    renderLogs();

}


/* SEGURIDAD */

function escapeLogHTML(value) {

    const element =
        document.createElement('div');

    element.textContent =
        value || '';

    return element.innerHTML;

}


/* FECHAS */

function formatLogDate(value) {

    const date =
        new Date(
            value + 'T00:00:00'
        );


    return date.toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }
    );

}


/* RENDER */

function renderLogs() {

    const container =
        document.getElementById(
            'logsContainer'
        );

    const empty =
        document.getElementById(
            'logsEmpty'
        );

    const search =
        document.getElementById(
            'logSearch'
        )
        .value
        .toLowerCase()
        .trim();

    const category =
        document.getElementById(
            'logCategoryFilter'
        )
        .value;


    let filtered =
        [...logs];


    if (search) {

        filtered =
            filtered.filter(
                log =>

                    log.title
                        .toLowerCase()
                        .includes(search)

                    ||

                    log.description
                        .toLowerCase()
                        .includes(search)

                    ||

                    (
                        log.learning || ''
                    )
                    .toLowerCase()
                    .includes(search)
            );

    }


    if (category !== 'all') {

        filtered =
            filtered.filter(
                log =>
                    log.category === category
            );

    }


    filtered.sort(
        (a, b) =>
            b.date.localeCompare(a.date)
    );


    container.innerHTML = '';


    empty.style.display =
        logs.length === 0
        &&
        !search
        &&
        category === 'all'
        ?
        'flex'
        :
        'none';


    filtered.forEach(log => {

        const article =
            document.createElement(
                'article'
            );


        article.className =
            'personal-log-entry';


        article.innerHTML = `

            <div class="log-date-column">

                <div class="log-date-circle">

                    <i class="bi bi-calendar3"></i>

                </div>

            </div>


            <div class="log-entry-card">

                <div class="log-entry-header">

                    <div>

                        <span class="log-entry-date">
                            ${formatLogDate(log.date)}
                        </span>

                        <h3>
                            ${escapeLogHTML(log.title)}
                        </h3>

                        <span class="log-category">
                            ${escapeLogHTML(log.category)}
                        </span>

                    </div>


                    <div class="log-actions">

                        <button
                            type="button"
                            onclick="editLog('${log.id}')"
                            title="Editar"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>


                        <button
                            type="button"
                            onclick="deleteLog('${log.id}')"
                            class="danger"
                            title="Eliminar"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>

                    </div>

                </div>


                <div class="log-description">

                    <span>
                        Actividad realizada
                    </span>

                    <p>
                        ${escapeLogHTML(log.description)}
                    </p>

                </div>


                ${
                    log.learning
                    ?
                    `
                    <div class="log-learning">

                        <i class="bi bi-lightbulb"></i>

                        <div>

                            <span>
                                Aprendizaje / observación
                            </span>

                            <p>
                                ${escapeLogHTML(log.learning)}
                            </p>

                        </div>

                    </div>
                    `
                    :
                    ''
                }

            </div>

        `;


        container.appendChild(
            article
        );

    });


    updateLogSummary();

}


/* RESUMEN */

function updateLogSummary() {

    document.getElementById(
        'totalLogs'
    ).textContent =
        logs.length;


    const dates =
        new Set(
            logs.map(
                log => log.date
            )
        );


    document.getElementById(
        'registeredDays'
    ).textContent =
        dates.size;


    const latest =
        [...logs]
        .sort(
            (a, b) =>
                b.date.localeCompare(a.date)
        )[0];


    document.getElementById(
        'lastLogDate'
    ).textContent =
        latest
        ?
        formatLogDate(latest.date)
        :
        'Sin actividad';

}


document.addEventListener(
    'DOMContentLoaded',
    renderLogs
);

</script>

@endpush