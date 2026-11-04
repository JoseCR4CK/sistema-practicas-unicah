@extends('layouts.teacher')

@section('title', 'Carga académica | UNICAH')

@section('content')

<div class="page-header">

    <div>
        <span class="student-home-eyebrow">
            GESTIÓN ACADÉMICA
        </span>

        <h1>
            Carga académica
        </h1>

        <p>
            Registra las asignaturas y horarios que impartes durante cada período académico.
        </p>
    </div>

    <button
        type="button"
        class="btn btn-primary teacher-load-add"
        data-bs-toggle="modal"
        data-bs-target="#academicLoadModal"
        onclick="prepareNewLoad()"
    >
        <i class="bi bi-plus-lg"></i>
        Agregar clase
    </button>

</div>


<!-- INFORMACIÓN DEL DOCENTE -->

<section class="teacher-load-profile">

    <div class="teacher-load-profile-icon">
        <i class="bi bi-person-badge"></i>
    </div>

    <div>
        <span>DOCENTE AUTENTICADO</span>
        <strong>Msc. Carlos Martínez</strong>
        <small>
            La carga académica se registrará automáticamente a tu usuario.
        </small>
    </div>

</section>


<!-- FILTROS DEL PERÍODO -->

<section class="content-card mb-4">

    <div class="teacher-load-period">

        <div>
            <label for="filterYear">
                Año académico
            </label>

            <select
                id="filterYear"
                class="form-select"
                onchange="renderAcademicLoad()"
            >
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
            </select>
        </div>


        <div>
            <label for="filterPeriod">
                Período
            </label>

            <select
                id="filterPeriod"
                class="form-select"
                onchange="renderAcademicLoad()"
            >
                <option value="I">I Período</option>
                <option value="II">II Período</option>
                <option value="III" selected>III Período</option>
            </select>
        </div>


        <div class="teacher-load-period-info">

            <i class="bi bi-info-circle"></i>

            <span>
                Cada período conserva su propia carga académica.
                Los registros anteriores no serán reemplazados.
            </span>

        </div>

    </div>

</section>


<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-book"></i>
            </div>

            <div>
                <span>Clases registradas</span>
                <strong id="statClasses">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-calendar3"></i>
            </div>

            <div>
                <span>Días con carga</span>
                <strong id="statDays">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <span>Bloques semanales</span>
                <strong id="statBlocks">0</strong>
            </div>

        </div>

    </div>

</section>


<!-- HORARIO SEMANAL -->

<section class="content-card mb-4">

    <div class="card-header">

        <div>
            <h2>Horario semanal</h2>

            <p>
                Distribución de las clases registradas durante el período seleccionado.
            </p>
        </div>

    </div>


    <div class="teacher-week-grid" id="weeklySchedule">

        <div class="teacher-week-day" data-day="Lunes">
            <div class="teacher-week-title">Lunes</div>
            <div class="teacher-week-content"></div>
        </div>

        <div class="teacher-week-day" data-day="Martes">
            <div class="teacher-week-title">Martes</div>
            <div class="teacher-week-content"></div>
        </div>

        <div class="teacher-week-day" data-day="Miércoles">
            <div class="teacher-week-title">Miércoles</div>
            <div class="teacher-week-content"></div>
        </div>

        <div class="teacher-week-day" data-day="Jueves">
            <div class="teacher-week-title">Jueves</div>
            <div class="teacher-week-content"></div>
        </div>

        <div class="teacher-week-day" data-day="Viernes">
            <div class="teacher-week-title">Viernes</div>
            <div class="teacher-week-content"></div>
        </div>

        <div class="teacher-week-day" data-day="Sábado">
            <div class="teacher-week-title">Sábado</div>
            <div class="teacher-week-content"></div>
        </div>

    </div>

</section>


<!-- LISTADO -->

<section class="content-card">

    <div class="card-header">

        <div>
            <h2>Clases registradas</h2>

            <p>
                Puedes modificar o eliminar tus registros mientras sean necesarios.
            </p>
        </div>

    </div>


    <div id="academicLoadList"></div>

</section>


<!-- MODAL -->

<div
    class="modal fade"
    id="academicLoadModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content teacher-load-modal">

            <div class="modal-header">

                <div>
                    <span class="student-home-eyebrow">
                        CARGA ACADÉMICA
                    </span>

                    <h5
                        class="modal-title"
                        id="academicLoadModalTitle"
                    >
                        Agregar clase
                    </h5>
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
                    id="loadId"
                >


                <div class="row g-3">

                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Año
                        </label>

                        <select
                            id="loadYear"
                            class="form-select"
                        >
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                        </select>

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Período
                        </label>

                        <select
                            id="loadPeriod"
                            class="form-select"
                        >
                            <option value="I">I Período</option>
                            <option value="II">II Período</option>
                            <option value="III">III Período</option>
                        </select>

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Carrera
                        </label>

                        <select
                            id="loadCareer"
                            class="form-select"
                            onchange="loadSubjects()"
                        >

                            <option value="">
                                Selecciona una carrera
                            </option>

                            <option value="Ingeniería en Ciencias de la Computación">
                                Ingeniería en Ciencias de la Computación
                            </option>

                            <option value="Ingeniería Industrial">
                                Ingeniería Industrial
                            </option>

                        </select>

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Asignatura
                        </label>

                        <select
                            id="loadSubject"
                            class="form-select"
                            disabled
                        >

                            <option value="">
                                Primero selecciona una carrera
                            </option>

                        </select>

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Sección
                        </label>

                        <input
                            type="text"
                            id="loadSection"
                            class="form-control"
                            placeholder="Ej. 0801"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Días en que se imparte
                        </label>


                        <div class="teacher-load-days">

                            @foreach(
                                ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                                as $day
                            )

                                <label>

                                    <input
                                        type="checkbox"
                                        class="load-day-checkbox"
                                        value="{{ $day }}"
                                    >

                                    <span>
                                        {{ $day }}
                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Hora de inicio
                        </label>

                        <input
                            type="time"
                            id="loadStart"
                            class="form-control"
                            min="07:00"
                            max="22:00"
                            step="300"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Hora de finalización
                        </label>

                        <input
                            type="time"
                            id="loadEnd"
                            class="form-control"
                            min="07:00"
                            max="22:00"
                            step="300"
                        >

                    </div>


                    <div class="col-12">

                        <div class="teacher-load-rule">

                            <i class="bi bi-info-circle"></i>

                            <span>
                                El horario permitido es de lunes a sábado,
                                entre las 7:00 a. m. y las 10:00 p. m.
                            </span>

                        </div>

                    </div>

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
                    onclick="saveAcademicLoad()"
                >
                    <i class="bi bi-check-lg"></i>
                    Guardar clase
                </button>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

const STORAGE_KEY = 'unicah_docente_1_carga_academica';

let academicLoads =
    JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];


/*
|--------------------------------------------------------------------------
| CATÁLOGO TEMPORAL
|--------------------------------------------------------------------------
|
| Estas asignaturas son únicamente para probar la interfaz.
| Cuando tengamos el catálogo oficial serán sustituidas por los
| registros reales de la base de datos.
|
*/

const subjectCatalog = {

    'Ingeniería en Ciencias de la Computación': [
        'Programación Estructurada I',
        'Programación Estructurada II',
        'Circuitos Lógicos',
        'Redes II',
        'Sistemas Operativos II'
    ],

    'Ingeniería Industrial': [
        'Ingeniería de la Producción'
    ]

};


function loadSubjects(selectedSubject = '') {

    const career =
        document.getElementById('loadCareer').value;

    const subjectSelect =
        document.getElementById('loadSubject');


    subjectSelect.innerHTML = '';


    if (!career || !subjectCatalog[career]) {

        subjectSelect.disabled = true;

        subjectSelect.innerHTML =
            '<option value="">Primero selecciona una carrera</option>';

        return;
    }


    subjectSelect.disabled = false;


    const defaultOption =
        document.createElement('option');

    defaultOption.value = '';

    defaultOption.textContent =
        'Selecciona una asignatura';

    subjectSelect.appendChild(defaultOption);


    subjectCatalog[career].forEach(subject => {

        const option =
            document.createElement('option');

        option.value = subject;

        option.textContent = subject;

        if (subject === selectedSubject) {
            option.selected = true;
        }

        subjectSelect.appendChild(option);

    });

}


function prepareNewLoad() {

    document.getElementById('loadId').value = '';

    document.getElementById('academicLoadModalTitle')
        .textContent = 'Agregar clase';


    document.getElementById('loadYear').value =
        document.getElementById('filterYear').value;


    document.getElementById('loadPeriod').value =
        document.getElementById('filterPeriod').value;


    document.getElementById('loadCareer').value = '';

    document.getElementById('loadSection').value = '';

    document.getElementById('loadStart').value = '';

    document.getElementById('loadEnd').value = '';


    document
        .querySelectorAll('.load-day-checkbox')
        .forEach(check => check.checked = false);


    loadSubjects();

}


function saveAcademicLoad() {

    const id =
        document.getElementById('loadId').value;

    const year =
        document.getElementById('loadYear').value;

    const period =
        document.getElementById('loadPeriod').value;

    const career =
        document.getElementById('loadCareer').value;

    const subject =
        document.getElementById('loadSubject').value;

    const section =
        document.getElementById('loadSection').value.trim();

    const start =
        document.getElementById('loadStart').value;

    const end =
        document.getElementById('loadEnd').value;


    const days = Array
        .from(
            document.querySelectorAll(
                '.load-day-checkbox:checked'
            )
        )
        .map(check => check.value);


    if (
        !year ||
        !period ||
        !career ||
        !subject ||
        days.length === 0 ||
        !start ||
        !end
    ) {

        alert(
            'Completa todos los campos obligatorios y selecciona al menos un día.'
        );

        return;
    }


    if (start < '07:00' || end > '22:00') {

        alert(
            'El horario debe estar comprendido entre las 7:00 a. m. y las 10:00 p. m.'
        );

        return;
    }


    if (start >= end) {

        alert(
            'La hora de finalización debe ser posterior a la hora de inicio.'
        );

        return;
    }


    const hasOverlap =
        academicLoads.some(item => {

            if (
                String(item.id) === String(id)
            ) {
                return false;
            }


            if (
                item.year !== year ||
                item.period !== period
            ) {
                return false;
            }


            const commonDay =
                item.days.some(day =>
                    days.includes(day)
                );


            if (!commonDay) {
                return false;
            }


            return (
                start < item.end &&
                end > item.start
            );

        });


    if (hasOverlap) {

        alert(
            'Existe otra clase registrada que coincide con este horario.'
        );

        return;
    }


    const now =
        new Date().toISOString();


    if (id) {

        const index =
            academicLoads.findIndex(
                item => String(item.id) === String(id)
            );


        if (index !== -1) {

            academicLoads[index] = {

                ...academicLoads[index],

                year,
                period,
                career,
                subject,
                section,
                days,
                start,
                end,

                updatedAt: now

            };

        }

    } else {

        academicLoads.push({

            id: Date.now(),

            year,
            period,
            career,
            subject,
            section,
            days,
            start,
            end,

            createdAt: now,
            updatedAt: now

        });

    }


    saveAcademicLoads();


    document.getElementById('filterYear').value =
        year;

    document.getElementById('filterPeriod').value =
        period;


    renderAcademicLoad();


    bootstrap.Modal
        .getInstance(
            document.getElementById('academicLoadModal')
        )
        .hide();

}


function editAcademicLoad(id) {

    const item =
        academicLoads.find(
            load => String(load.id) === String(id)
        );


    if (!item) {
        return;
    }


    document.getElementById('loadId').value =
        item.id;

    document.getElementById('loadYear').value =
        item.year;

    document.getElementById('loadPeriod').value =
        item.period;

    document.getElementById('loadCareer').value =
        item.career;

    document.getElementById('loadSection').value =
        item.section || '';

    document.getElementById('loadStart').value =
        item.start;

    document.getElementById('loadEnd').value =
        item.end;


    loadSubjects(item.subject);


    document
        .querySelectorAll('.load-day-checkbox')
        .forEach(check => {

            check.checked =
                item.days.includes(check.value);

        });


    document.getElementById('academicLoadModalTitle')
        .textContent = 'Editar clase';


    const modal =
        new bootstrap.Modal(
            document.getElementById('academicLoadModal')
        );

    modal.show();

}


function deleteAcademicLoad(id) {

    const item =
        academicLoads.find(
            load => String(load.id) === String(id)
        );


    if (!item) {
        return;
    }


    const confirmed =
        confirm(
            `¿Deseas eliminar "${item.subject}" de tu carga académica?`
        );


    if (!confirmed) {
        return;
    }


    academicLoads =
        academicLoads.filter(
            load => String(load.id) !== String(id)
        );


    saveAcademicLoads();

    renderAcademicLoad();

}


function saveAcademicLoads() {

    localStorage.setItem(
        STORAGE_KEY,
        JSON.stringify(academicLoads)
    );

}


function getFilteredLoads() {

    const year =
        document.getElementById('filterYear').value;

    const period =
        document.getElementById('filterPeriod').value;


    return academicLoads.filter(item =>
        item.year === year &&
        item.period === period
    );

}


function formatTime(time) {

    if (!time) {
        return '';
    }


    const [hour, minute] =
        time.split(':').map(Number);


    const suffix =
        hour >= 12 ? 'p. m.' : 'a. m.';


    const normalizedHour =
        hour % 12 || 12;


    return `${normalizedHour}:${String(minute).padStart(2, '0')} ${suffix}`;

}


function renderAcademicLoad() {

    const loads =
        getFilteredLoads();


    renderStats(loads);

    renderWeeklySchedule(loads);

    renderList(loads);

}


function renderStats(loads) {

    document.getElementById('statClasses')
        .textContent = loads.length;


    const uniqueDays =
        new Set(
            loads.flatMap(item => item.days)
        );


    document.getElementById('statDays')
        .textContent = uniqueDays.size;


    const blocks =
        loads.reduce(
            (total, item) =>
                total + item.days.length,
            0
        );


    document.getElementById('statBlocks')
        .textContent = blocks;

}


function renderWeeklySchedule(loads) {

    document
        .querySelectorAll('.teacher-week-day')
        .forEach(dayColumn => {

            const day =
                dayColumn.dataset.day;

            const content =
                dayColumn.querySelector(
                    '.teacher-week-content'
                );


            const dayLoads =
                loads
                    .filter(item =>
                        item.days.includes(day)
                    )
                    .sort((a, b) =>
                        a.start.localeCompare(b.start)
                    );


            if (dayLoads.length === 0) {

                content.innerHTML = `
                    <div class="teacher-week-empty">
                        Sin clases
                    </div>
                `;

                return;
            }


            content.innerHTML =
                dayLoads.map(item => `

                    <div class="teacher-week-class">

                        <strong>
                            ${item.subject}
                        </strong>

                        <span>
                            ${formatTime(item.start)}
                            –
                            ${formatTime(item.end)}
                        </span>

                        ${
                            item.section
                                ? `<small>Sección ${item.section}</small>`
                                : ''
                        }

                    </div>

                `).join('');

        });

}


function renderList(loads) {

    const container =
        document.getElementById('academicLoadList');


    if (loads.length === 0) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-calendar2-week"></i>

                <strong>
                    No hay carga académica registrada
                </strong>

                <span>
                    Agrega las clases que impartes durante este período.
                </span>

            </div>

        `;

        return;
    }


    const sorted =
        [...loads].sort((a, b) =>
            a.start.localeCompare(b.start)
        );


    container.innerHTML =
        sorted.map(item => `

            <article class="teacher-load-row">

                <div class="teacher-load-row-icon">
                    <i class="bi bi-book"></i>
                </div>


                <div class="teacher-load-row-main">

                    <span>
                        ${item.career}
                    </span>

                    <strong>
                        ${item.subject}
                    </strong>

                    ${
                        item.section
                            ? `<small>Sección ${item.section}</small>`
                            : ''
                    }

                </div>


                <div class="teacher-load-row-days">

                    <span>Días</span>

                    <strong>
                        ${item.days.join(' · ')}
                    </strong>

                </div>


                <div class="teacher-load-row-time">

                    <span>Horario</span>

                    <strong>
                        ${formatTime(item.start)}
                        –
                        ${formatTime(item.end)}
                    </strong>

                </div>


                <div class="teacher-load-row-actions">

                    <button
                        type="button"
                        onclick="editAcademicLoad(${item.id})"
                        title="Editar"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                    <button
                        type="button"
                        onclick="deleteAcademicLoad(${item.id})"
                        title="Eliminar"
                    >
                        <i class="bi bi-trash"></i>
                    </button>

                </div>

            </article>

        `).join('');

}


renderAcademicLoad();

</script>

@endpush