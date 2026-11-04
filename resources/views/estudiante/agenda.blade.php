@extends('layouts.student')

@section('title', 'Mi agenda | UNICAH')

@section('content')

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            MI ESPACIO
        </span>

        <h1>
            Mi agenda
        </h1>

        <p>
            Consulta las fechas importantes de tu práctica
            y organiza tus propios recordatorios.
        </p>

    </div>

    <button
        type="button"
        class="agenda-new-button"
        data-bs-toggle="modal"
        data-bs-target="#agendaModal"
        onclick="prepareNewAgendaEvent()"
    >
        <i class="bi bi-plus-lg"></i>
        Nuevo evento
    </button>

</div>


<!-- INFO -->

<div class="agenda-info-box">

    <div class="agenda-info-icon">
        <i class="bi bi-calendar-heart"></i>
    </div>

    <div>

        <strong>
            Tu práctica en un solo calendario
        </strong>

        <p>
            Las fechas institucionales se muestran automáticamente.
            También puedes agregar actividades y recordatorios personales.
        </p>

    </div>

</div>


<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="agenda-stat-card">

            <div class="agenda-stat-icon blue">
                <i class="bi bi-calendar-event"></i>
            </div>

            <div>
                <span>Próximos eventos</span>
                <strong id="agendaUpcoming">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="agenda-stat-card">

            <div class="agenda-stat-icon gold">
                <i class="bi bi-building-check"></i>
            </div>

            <div>
                <span>Fechas del proceso</span>
                <strong id="agendaOfficial">4</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="agenda-stat-card">

            <div class="agenda-stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Realizados</span>
                <strong id="agendaCompleted">0</strong>
            </div>

        </div>

    </div>

</section>


<!-- VISTA PRINCIPAL -->

<section class="row g-4">

    <!-- CALENDARIO -->

    <div class="col-12 col-xl-7">

        <div class="content-card agenda-calendar-card">

            <div class="agenda-calendar-header">

                <button
                    type="button"
                    onclick="changeMonth(-1)"
                >
                    <i class="bi bi-chevron-left"></i>
                </button>

                <div>
                    <span>CALENDARIO</span>

                    <h2 id="calendarMonthTitle">
                    </h2>
                </div>

                <button
                    type="button"
                    onclick="changeMonth(1)"
                >
                    <i class="bi bi-chevron-right"></i>
                </button>

            </div>


            <div class="agenda-weekdays">

                <span>Dom</span>
                <span>Lun</span>
                <span>Mar</span>
                <span>Mié</span>
                <span>Jue</span>
                <span>Vie</span>
                <span>Sáb</span>

            </div>


            <div
                id="calendarGrid"
                class="agenda-calendar-grid"
            >
            </div>


            <div class="agenda-calendar-legend">

                <span>
                    <i class="agenda-dot official"></i>
                    Institucional
                </span>

                <span>
                    <i class="agenda-dot personal"></i>
                    Personal
                </span>

            </div>

        </div>

    </div>


    <!-- PRÓXIMOS -->

    <div class="col-12 col-xl-5">

        <div class="content-card agenda-upcoming-card">

            <div class="card-header">

                <div>

                    <h2>
                        Próximos eventos
                    </h2>

                    <p>
                        Actividades y fechas importantes.
                    </p>

                </div>

            </div>


            <div
                id="agendaUpcomingList"
                class="agenda-upcoming-list"
            >
            </div>

        </div>

    </div>

</section>


<!-- TODOS LOS EVENTOS -->

<section class="student-section agenda-all-section">

    <div class="student-section-heading">

        <div>

            <h2>
                Todos mis eventos
            </h2>

            <p>
                Fechas institucionales y actividades personales.
            </p>

        </div>

    </div>


    <div class="agenda-toolbar">

        <div class="agenda-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="agendaSearch"
                placeholder="Buscar evento..."
                oninput="renderAgenda()"
            >

        </div>


        <select
            id="agendaFilter"
            onchange="renderAgenda()"
        >

            <option value="all">
                Todos
            </option>

            <option value="official">
                Institucionales
            </option>

            <option value="personal">
                Personales
            </option>

            <option value="completed">
                Realizados
            </option>

        </select>

    </div>


    <div
        id="agendaEventsList"
        class="agenda-events-list"
    >
    </div>

</section>


<!-- MODAL -->

<div
    class="modal fade"
    id="agendaModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content agenda-modal">

            <div class="modal-header">

                <div>

                    <span class="section-label">
                        MI AGENDA
                    </span>

                    <h2
                        id="agendaModalTitle"
                        class="modal-title"
                    >
                        Nuevo evento
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
                    id="agendaEventId"
                >


                <div class="agenda-form-group">

                    <label for="agendaTitle">
                        Nombre del evento
                    </label>

                    <input
                        type="text"
                        id="agendaTitle"
                        maxlength="100"
                        placeholder="Ej. Reunión con jefe inmediato"
                    >

                </div>


                <div class="row g-3">

                    <div class="col-12 col-md-6">

                        <div class="agenda-form-group">

                            <label for="agendaDate">
                                Fecha
                            </label>

                            <input
                                type="date"
                                id="agendaDate"
                            >

                        </div>

                    </div>


                    <div class="col-12 col-md-6">

                        <div class="agenda-form-group">

                            <label for="agendaTime">
                                Hora
                            </label>

                            <input
                                type="time"
                                id="agendaTime"
                            >

                        </div>

                    </div>

                </div>


                <div class="agenda-form-group">

                    <label for="agendaType">
                        Tipo
                    </label>

                    <select id="agendaType">

                        <option value="Recordatorio">
                            Recordatorio
                        </option>

                        <option value="Reunión">
                            Reunión
                        </option>

                        <option value="Entrega">
                            Entrega
                        </option>

                        <option value="Personal">
                            Personal
                        </option>

                        <option value="Otro">
                            Otro
                        </option>

                    </select>

                </div>


                <div class="agenda-form-group">

                    <label for="agendaDescription">
                        Descripción
                    </label>

                    <textarea
                        id="agendaDescription"
                        rows="4"
                        maxlength="600"
                        placeholder="Añade información sobre este evento..."
                    ></textarea>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="agenda-secondary-button"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>

                <button
                    type="button"
                    class="agenda-save-button"
                    onclick="saveAgendaEvent()"
                >
                    <i class="bi bi-check2"></i>
                    Guardar evento
                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

const AGENDA_STORAGE_KEY =
    'unicah_practica_0801202200458_agenda';


let personalAgendaEvents =
    JSON.parse(
        localStorage.getItem(AGENDA_STORAGE_KEY)
    ) || [];


/* =========================================
   EVENTOS OFICIALES
========================================= */

const officialAgendaEvents = [

    {
        id: 'official_start',
        title: 'Inicio de práctica profesional',
        date: '2026-09-01',
        type: 'Institucional',
        description: 'Fecha oficial de inicio de la práctica profesional.',
        official: true
    },

    {
        id: 'official_visit',
        title: 'Habilitación de visita de seguimiento',
        date: '2026-10-15',
        type: 'Institucional',
        description: 'La visita puede realizarse a partir del día 45.',
        official: true
    },

    {
        id: 'official_monograph',
        title: 'Entrega límite de monografía',
        date: '2026-11-23',
        type: 'Institucional',
        description: 'La monografía debe entregarse una semana antes de finalizar la práctica.',
        official: true
    },

    {
        id: 'official_end',
        title: 'Finalización de práctica',
        date: '2026-11-30',
        type: 'Institucional',
        description: 'Fecha programada de finalización de la práctica profesional.',
        official: true
    }

];


/* Mes mostrado inicialmente */
let calendarDate = new Date();



/* =========================================
   FECHA ACTUAL
========================================= */

function getTodayDateString() {

    const today = new Date();

    const year =
        today.getFullYear();

    const month =
        String(
            today.getMonth() + 1
        ).padStart(2, '0');

    const day =
        String(
            today.getDate()
        ).padStart(2, '0');


    return `${year}-${month}-${day}`;

}



/* =========================================
   PERSISTENCIA
========================================= */

function persistAgenda() {

    localStorage.setItem(
        AGENDA_STORAGE_KEY,
        JSON.stringify(personalAgendaEvents)
    );

}



/* =========================================
   NUEVO EVENTO
========================================= */

function prepareNewAgendaEvent(date = '') {

    document.getElementById(
        'agendaEventId'
    ).value = '';


    document.getElementById(
        'agendaTitle'
    ).value = '';


    document.getElementById(
        'agendaDate'
    ).value =
        date ||
        getTodayDateString();


    document.getElementById(
        'agendaTime'
    ).value = '';


    document.getElementById(
        'agendaType'
    ).value =
        'Recordatorio';


    document.getElementById(
        'agendaDescription'
    ).value = '';


    document.getElementById(
        'agendaModalTitle'
    ).textContent =
        'Nuevo evento';

}



/* =========================================
   GUARDAR EVENTO
========================================= */

function saveAgendaEvent() {

    const id =
        document.getElementById(
            'agendaEventId'
        ).value;


    const title =
        document.getElementById(
            'agendaTitle'
        )
        .value
        .trim();


    const date =
        document.getElementById(
            'agendaDate'
        ).value;


    const time =
        document.getElementById(
            'agendaTime'
        ).value;


    const type =
        document.getElementById(
            'agendaType'
        ).value;


    const description =
        document.getElementById(
            'agendaDescription'
        )
        .value
        .trim();


    if (!title) {

        alert(
            'Escribe el nombre del evento.'
        );

        return;

    }


    if (!date) {

        alert(
            'Selecciona una fecha.'
        );

        return;

    }


    const now =
        new Date().toISOString();


    if (id) {

        const index =
            personalAgendaEvents.findIndex(
                event =>
                    event.id === id
            );


        if (index !== -1) {

            personalAgendaEvents[index] = {

                ...personalAgendaEvents[index],

                title,
                date,
                time,
                type,
                description,
                updatedAt: now

            };

        }

    }
    else {

        personalAgendaEvents.push({

            id:
                'agenda_' +
                Date.now(),

            title,
            date,
            time,
            type,
            description,

            completed: false,
            official: false,

            createdAt: now,
            updatedAt: now

        });

    }


    persistAgenda();

    renderAgenda();


    const modal =
        bootstrap.Modal.getInstance(
            document.getElementById(
                'agendaModal'
            )
        );


    if (modal) {
        modal.hide();
    }

}



/* =========================================
   EDITAR
========================================= */

function editAgendaEvent(id) {

    const event =
        personalAgendaEvents.find(
            event =>
                event.id === id
        );


    if (!event) {
        return;
    }


    document.getElementById(
        'agendaEventId'
    ).value =
        event.id;


    document.getElementById(
        'agendaTitle'
    ).value =
        event.title;


    document.getElementById(
        'agendaDate'
    ).value =
        event.date;


    document.getElementById(
        'agendaTime'
    ).value =
        event.time || '';


    document.getElementById(
        'agendaType'
    ).value =
        event.type;


    document.getElementById(
        'agendaDescription'
    ).value =
        event.description || '';


    document.getElementById(
        'agendaModalTitle'
    ).textContent =
        'Editar evento';


    new bootstrap.Modal(
        document.getElementById(
            'agendaModal'
        )
    ).show();

}



/* =========================================
   COMPLETAR
========================================= */

function toggleAgendaEvent(id) {

    const index =
        personalAgendaEvents.findIndex(
            event =>
                event.id === id
        );


    if (index === -1) {
        return;
    }


    personalAgendaEvents[index].completed =
        !personalAgendaEvents[index].completed;


    personalAgendaEvents[index].updatedAt =
        new Date().toISOString();


    persistAgenda();

    renderAgenda();

}



/* =========================================
   ELIMINAR
========================================= */

function deleteAgendaEvent(id) {

    const event =
        personalAgendaEvents.find(
            event =>
                event.id === id
        );


    if (!event) {
        return;
    }


    const confirmed =
        confirm(
            '¿Deseas eliminar el evento "' +
            event.title +
            '"?'
        );


    if (!confirmed) {
        return;
    }


    personalAgendaEvents =
        personalAgendaEvents.filter(
            event =>
                event.id !== id
        );


    persistAgenda();

    renderAgenda();

}



/* =========================================
   UTILIDADES
========================================= */

function escapeAgendaHTML(value) {

    const element =
        document.createElement('div');

    element.textContent =
        value || '';

    return element.innerHTML;

}


function formatAgendaDate(value) {

    const date =
        new Date(
            value + 'T00:00:00'
        );


    return date.toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

}


function allAgendaEvents() {

    return [
        ...officialAgendaEvents,
        ...personalAgendaEvents
    ];

}



/* =========================================
   CAMBIAR MES
========================================= */

function changeMonth(amount) {

    calendarDate =
        new Date(
            calendarDate.getFullYear(),
            calendarDate.getMonth() + amount,
            1
        );


    renderCalendar();

}



/* =========================================
   CALENDARIO
========================================= */

function renderCalendar() {

    const year =
        calendarDate.getFullYear();

    const month =
        calendarDate.getMonth();


    const title =
        calendarDate.toLocaleDateString(
            'es-HN',
            {
                month: 'long',
                year: 'numeric'
            }
        );


    document.getElementById(
        'calendarMonthTitle'
    ).textContent =
        title.charAt(0).toUpperCase()
        +
        title.slice(1);


    const firstDay =
        new Date(
            year,
            month,
            1
        ).getDay();


    const daysInMonth =
        new Date(
            year,
            month + 1,
            0
        ).getDate();


    const grid =
        document.getElementById(
            'calendarGrid'
        );


    grid.innerHTML = '';


    for (
        let i = 0;
        i < firstDay;
        i++
    ) {

        const empty =
            document.createElement('div');

        empty.className =
            'agenda-calendar-day empty';

        grid.appendChild(empty);

    }


    for (
        let day = 1;
        day <= daysInMonth;
        day++
    ) {

        const dateString =
            year
            +
            '-'
            +
            String(
                month + 1
            ).padStart(2, '0')
            +
            '-'
            +
            String(day)
                .padStart(2, '0');


        const events =
            allAgendaEvents()
            .filter(
                event =>
                    event.date ===
                    dateString
            );


        const cell =
            document.createElement(
                'button'
            );


        cell.type =
            'button';


        cell.className =
            'agenda-calendar-day';


        cell.onclick =
            () => {

                prepareNewAgendaEvent(
                    dateString
                );


                new bootstrap.Modal(
                    document.getElementById(
                        'agendaModal'
                    )
                ).show();

            };


        const number =
            document.createElement(
                'span'
            );


        number.className =
            'agenda-day-number';


        number.textContent =
            day;


        cell.appendChild(
            number
        );


        if (events.length > 0) {

            const dots =
                document.createElement(
                    'div'
                );


            dots.className =
                'agenda-day-dots';


            events
                .slice(0, 3)
                .forEach(
                    event => {

                        const dot =
                            document.createElement(
                                'i'
                            );


                        dot.className =
                            event.official
                            ?
                            'official'
                            :
                            'personal';


                        dots.appendChild(
                            dot
                        );

                    }
                );


            cell.appendChild(
                dots
            );

        }


        grid.appendChild(
            cell
        );

    }

}



/* =========================================
   PRÓXIMOS EVENTOS
========================================= */

function renderUpcoming() {

    const container =
        document.getElementById(
            'agendaUpcomingList'
        );


    container.innerHTML = '';


    const today =
        getTodayDateString();


    const events =
        allAgendaEvents()
        .filter(
            event =>
                !event.completed
                &&
                event.date >= today
        )
        .sort(
            (a, b) =>
                a.date.localeCompare(
                    b.date
                )
        )
        .slice(0, 5);


    if (!events.length) {

        container.innerHTML = `

            <div class="agenda-no-events">

                No hay eventos próximos.

            </div>

        `;

        return;

    }


    events.forEach(event => {

        const item =
            document.createElement(
                'div'
            );


        item.className =
            'agenda-upcoming-item';


        item.innerHTML = `

            <div
                class="agenda-upcoming-marker ${
                    event.official
                    ?
                    'official'
                    :
                    'personal'
                }"
            ></div>


            <div>

                <span>
                    ${formatAgendaDate(event.date)}

                    ${
                        event.time
                        ?
                        ' · ' + event.time
                        :
                        ''
                    }
                </span>

                <strong>
                    ${escapeAgendaHTML(event.title)}
                </strong>

                <small>

                    ${
                        event.official
                        ?
                        'Institucional'
                        :
                        escapeAgendaHTML(
                            event.type
                        )
                    }

                </small>

            </div>

        `;


        container.appendChild(
            item
        );

    });

}



/* =========================================
   TODOS LOS EVENTOS
========================================= */

function renderAgendaEvents() {

    const container =
        document.getElementById(
            'agendaEventsList'
        );


    const search =
        document.getElementById(
            'agendaSearch'
        )
        .value
        .toLowerCase()
        .trim();


    const filter =
        document.getElementById(
            'agendaFilter'
        ).value;


    let events =
        allAgendaEvents();


    if (search) {

        events =
            events.filter(
                event =>

                    event.title
                        .toLowerCase()
                        .includes(search)

                    ||

                    (
                        event.description || ''
                    )
                    .toLowerCase()
                    .includes(search)
            );

    }


    if (filter === 'official') {

        events =
            events.filter(
                event =>
                    event.official
            );

    }


    if (filter === 'personal') {

        events =
            events.filter(
                event =>
                    !event.official
            );

    }


    if (filter === 'completed') {

        events =
            events.filter(
                event =>
                    event.completed
            );

    }


    events.sort(
        (a, b) =>
            a.date.localeCompare(
                b.date
            )
    );


    container.innerHTML = '';


    events.forEach(event => {

        const item =
            document.createElement(
                'article'
            );


        item.className =
            'agenda-event-row'
            +
            (
                event.completed
                ?
                ' completed'
                :
                ''
            );


        item.innerHTML = `

            <div
                class="agenda-event-indicator ${
                    event.official
                    ?
                    'official'
                    :
                    'personal'
                }"
            >

                <i
                    class="bi ${
                        event.official
                        ?
                        'bi-building-check'
                        :
                        'bi-calendar-event'
                    }"
                ></i>

            </div>


            <div class="agenda-event-main">

                <div>

                    <span>

                        ${formatAgendaDate(event.date)}

                        ${
                            event.time
                            ?
                            ' · ' + event.time
                            :
                            ''
                        }

                    </span>


                    <strong>
                        ${escapeAgendaHTML(event.title)}
                    </strong>


                    <small>

                        ${
                            event.official
                            ?
                            'Fecha institucional'
                            :
                            escapeAgendaHTML(
                                event.type
                            )
                        }

                    </small>

                </div>


                ${
                    !event.official
                    ?
                    `

                    <div class="agenda-event-actions">

                        <button
                            type="button"
                            onclick="toggleAgendaEvent('${event.id}')"
                            title="${
                                event.completed
                                ?
                                'Marcar pendiente'
                                :
                                'Marcar realizado'
                            }"
                        >

                            <i
                                class="bi ${
                                    event.completed
                                    ?
                                    'bi-arrow-counterclockwise'
                                    :
                                    'bi-check2'
                                }"
                            ></i>

                        </button>


                        <button
                            type="button"
                            onclick="editAgendaEvent('${event.id}')"
                            title="Editar"
                        >

                            <i class="bi bi-pencil"></i>

                        </button>


                        <button
                            type="button"
                            class="danger"
                            onclick="deleteAgendaEvent('${event.id}')"
                            title="Eliminar"
                        >

                            <i class="bi bi-trash3"></i>

                        </button>

                    </div>

                    `
                    :
                    `

                    <span class="agenda-locked">

                        <i class="bi bi-lock"></i>

                        Institucional

                    </span>

                    `
                }

            </div>

        `;


        container.appendChild(
            item
        );

    });

}



/* =========================================
   RESUMEN
========================================= */

function updateAgendaSummary() {

    const all =
        allAgendaEvents();


    const today =
        getTodayDateString();


    document.getElementById(
        'agendaUpcoming'
    ).textContent =
        all.filter(
            event =>
                !event.completed
                &&
                event.date >= today
        ).length;


    document.getElementById(
        'agendaCompleted'
    ).textContent =
        personalAgendaEvents.filter(
            event =>
                event.completed
        ).length;

}



/* =========================================
   RENDER GENERAL
========================================= */

function renderAgenda() {

    renderCalendar();

    renderUpcoming();

    renderAgendaEvents();

    updateAgendaSummary();

}



/* =========================================
   INICIO
========================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        renderAgenda();

    }
);

</script>

@endpush