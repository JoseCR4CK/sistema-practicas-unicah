@extends('layouts.app')

@section('title', 'Detalle del docente | UNICAH')

@section('content')

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            CONTROL DE DOCENTES
        </span>

        <h1>
            {{ $docente['nombre'] }}
        </h1>

        <p>
            Consulta la información de seguimiento y carga académica
            del docente seleccionado.
        </p>

    </div>

    <a
        href="{{ route('coordinacion.docentes') }}"
        class="btn btn-light coord-back-button"
    >
        <i class="bi bi-arrow-left"></i>
        Volver a docentes
    </a>

</div>


{{-- PERFIL --}}

<section class="coord-teacher-detail-profile mb-4">

    <div class="coord-teacher-detail-avatar">
        {{ $docente['iniciales'] }}
    </div>

    <div class="coord-teacher-detail-info">

        <span>DOCENTE</span>

        <strong>
            {{ $docente['nombre'] }}
        </strong>

        <small>
            {{ $docente['facultad'] }}
        </small>

    </div>


    <div class="coord-teacher-detail-stats">

        <div>
            <span>Practicantes</span>
            <strong>{{ $docente['practicantes'] }}</strong>
        </div>

        <div>
            <span>Visitas próximas</span>
            <strong>{{ $docente['visitas'] }}</strong>
        </div>

        <div>
            <span>Estado</span>

            <strong class="{{ $docente['estado'] === 'Activo' ? 'text-success' : 'text-warning' }}">
                {{ $docente['estado'] }}
            </strong>
        </div>

    </div>

</section>


{{-- ENCABEZADO CARGA --}}

<section class="content-card mb-4">

    <div class="card-header">

        <div>

            <span class="student-home-eyebrow">
                CARGA ACADÉMICA
            </span>

            <h2>
                Horario registrado por el docente
            </h2>

            <p>
                Coordinación puede consultar la carga académica
                y corregir registros únicamente cuando exista un error.
            </p>

        </div>

    </div>


    <div class="teacher-load-period">

        <div>

            <label for="coordLoadYear">
                Año académico
            </label>

            <select
                id="coordLoadYear"
                class="form-select"
                onchange="renderCoordinatorLoad()"
            >
                <option value="2026">2026</option>
                <option value="2027">2027</option>
                <option value="2028">2028</option>
            </select>

        </div>


        <div>

            <label for="coordLoadPeriod">
                Período
            </label>

            <select
                id="coordLoadPeriod"
                class="form-select"
                onchange="renderCoordinatorLoad()"
            >
                <option value="I">I Período</option>
                <option value="II">II Período</option>
                <option value="III" selected>III Período</option>
            </select>

        </div>


        <div class="coord-load-notice">

            <i class="bi bi-shield-check"></i>

            <span>
                Las modificaciones realizadas por Coordinación
                quedan registradas como correcciones administrativas.
            </span>

        </div>

    </div>

</section>


{{-- RESUMEN --}}

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-book"></i>
            </div>

            <div>
                <span>Clases registradas</span>
                <strong id="coordStatClasses">0</strong>
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
                <strong id="coordStatDays">0</strong>
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
                <strong id="coordStatBlocks">0</strong>
            </div>

        </div>

    </div>

</section>


{{-- HORARIO SEMANAL --}}

<section class="content-card mb-4">

    <div class="card-header">

        <div>
            <h2>Horario semanal</h2>

            <p>
                Distribución de clases correspondiente al período seleccionado.
            </p>
        </div>

    </div>


    <div class="teacher-week-grid">

        @foreach(
            ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
            as $day
        )

            <div
                class="teacher-week-day coord-week-day"
                data-day="{{ $day }}"
            >

                <div class="teacher-week-title">
                    {{ $day }}
                </div>

                <div class="teacher-week-content"></div>

            </div>

        @endforeach

    </div>

</section>


{{-- LISTADO --}}

<section class="content-card mb-4">

    <div class="card-header">

        <div>

            <h2>
                Detalle de la carga académica
            </h2>

            <p>
                Utiliza la opción de corregir únicamente cuando
                la información ingresada por el docente sea incorrecta.
            </p>

        </div>

    </div>


    <div id="coordAcademicLoadList"></div>

</section>


{{-- HISTORIAL --}}

<section class="content-card">

    <div class="card-header">

        <div>

            <h2>
                Historial de correcciones
            </h2>

            <p>
                Registro de modificaciones realizadas por Coordinación Académica.
            </p>

        </div>

    </div>

    <div id="coordCorrectionHistory"></div>

</section>



{{-- MODAL DE CORRECCIÓN --}}

<div
    class="modal fade"
    id="coordLoadModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content teacher-load-modal">

            <div class="modal-header">

                <div>

                    <span class="student-home-eyebrow">
                        CORRECCIÓN ADMINISTRATIVA
                    </span>

                    <h5 class="modal-title">
                        Corregir carga académica
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
                    id="coordLoadId"
                >


                <div class="coord-correction-warning">

                    <i class="bi bi-exclamation-triangle"></i>

                    <div>

                        <strong>
                            Edición por excepción
                        </strong>

                        <span>
                            Esta función debe utilizarse únicamente
                            para corregir información ingresada
                            incorrectamente por el docente.
                        </span>

                    </div>

                </div>


                <div class="row g-3">

                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Carrera
                        </label>

                        <input
                            type="text"
                            id="coordCareer"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Asignatura
                        </label>

                        <input
                            type="text"
                            id="coordSubject"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Sección
                        </label>

                        <input
                            type="text"
                            id="coordSection"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Días
                        </label>

                        <div class="teacher-load-days">

                            @foreach(
                                ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                                as $day
                            )

                                <label>

                                    <input
                                        type="checkbox"
                                        class="coord-day-checkbox"
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
                            id="coordStart"
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
                            id="coordEnd"
                            class="form-control"
                            min="07:00"
                            max="22:00"
                            step="300"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Motivo de la corrección
                        </label>

                        <textarea
                            id="coordCorrectionReason"
                            class="form-control"
                            rows="3"
                            placeholder="Ej. El docente registró incorrectamente la hora de finalización."
                        ></textarea>

                        <small class="coord-required-note">
                            El motivo es obligatorio para guardar la corrección.
                        </small>

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
                    onclick="saveCoordinatorCorrection()"
                >
                    <i class="bi bi-check-lg"></i>
                    Guardar corrección
                </button>

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>

const DOCENTE_ID = {{ $docente['id'] }};

const DOCENTE_NAME = @json($docente['nombre']);

const LOAD_STORAGE_KEY =
    `unicah_docente_${DOCENTE_ID}_carga_academica`;

const CORRECTION_STORAGE_KEY =
    'unicah_correcciones_carga_academica';


let academicLoads =
    JSON.parse(
        localStorage.getItem(LOAD_STORAGE_KEY)
    ) || [];


let corrections =
    JSON.parse(
        localStorage.getItem(CORRECTION_STORAGE_KEY)
    ) || [];


/* =========================================
   UTILIDADES
========================================= */

function formatTime(time) {

    if (!time) return '';


    const [hour, minute] =
        time.split(':').map(Number);


    const suffix =
        hour >= 12 ? 'p. m.' : 'a. m.';


    const normalized =
        hour % 12 || 12;


    return `${normalized}:${String(minute).padStart(2, '0')} ${suffix}`;

}


function getFilteredLoads() {

    const year =
        document.getElementById('coordLoadYear').value;

    const period =
        document.getElementById('coordLoadPeriod').value;


    return academicLoads.filter(item =>
        item.year === year &&
        item.period === period
    );

}


/* =========================================
   RENDER GENERAL
========================================= */

function renderCoordinatorLoad() {

    const loads =
        getFilteredLoads();


    renderCoordinatorStats(loads);

    renderCoordinatorSchedule(loads);

    renderCoordinatorList(loads);

    renderCorrectionHistory();

}


/* =========================================
   ESTADÍSTICAS
========================================= */

function renderCoordinatorStats(loads) {

    document.getElementById('coordStatClasses')
        .textContent = loads.length;


    const uniqueDays =
        new Set(
            loads.flatMap(item => item.days)
        );


    document.getElementById('coordStatDays')
        .textContent = uniqueDays.size;


    const blocks =
        loads.reduce(
            (total, item) =>
                total + item.days.length,
            0
        );


    document.getElementById('coordStatBlocks')
        .textContent = blocks;

}


/* =========================================
   CALENDARIO SEMANAL
========================================= */

function renderCoordinatorSchedule(loads) {

    document
        .querySelectorAll('.coord-week-day')
        .forEach(column => {

            const day =
                column.dataset.day;

            const content =
                column.querySelector(
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


/* =========================================
   LISTADO
========================================= */

function renderCoordinatorList(loads) {

    const container =
        document.getElementById(
            'coordAcademicLoadList'
        );


    if (loads.length === 0) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-calendar2-week"></i>

                <strong>
                    No hay carga académica registrada
                </strong>

                <span>
                    El docente todavía no ha registrado
                    clases para este período.
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


                <div class="coord-load-actions">

                    <button
                        type="button"
                        onclick="openCoordinatorCorrection(${item.id})"
                    >
                        <i class="bi bi-pencil-square"></i>

                        Corregir
                    </button>

                </div>

            </article>

        `).join('');

}


/* =========================================
   ABRIR CORRECCIÓN
========================================= */

function openCoordinatorCorrection(id) {

    const item =
        academicLoads.find(
            load =>
                String(load.id) === String(id)
        );


    if (!item) return;


    document.getElementById('coordLoadId').value =
        item.id;


    document.getElementById('coordCareer').value =
        item.career;


    document.getElementById('coordSubject').value =
        item.subject;


    document.getElementById('coordSection').value =
        item.section || '';


    document.getElementById('coordStart').value =
        item.start;


    document.getElementById('coordEnd').value =
        item.end;


    document.getElementById(
        'coordCorrectionReason'
    ).value = '';


    document
        .querySelectorAll('.coord-day-checkbox')
        .forEach(check => {

            check.checked =
                item.days.includes(check.value);

        });


    const modal =
        new bootstrap.Modal(
            document.getElementById(
                'coordLoadModal'
            )
        );


    modal.show();

}


/* =========================================
   GUARDAR CORRECCIÓN
========================================= */

function saveCoordinatorCorrection() {

    const id =
        document.getElementById(
            'coordLoadId'
        ).value;


    const career =
        document.getElementById(
            'coordCareer'
        ).value.trim();


    const subject =
        document.getElementById(
            'coordSubject'
        ).value.trim();


    const section =
        document.getElementById(
            'coordSection'
        ).value.trim();


    const start =
        document.getElementById(
            'coordStart'
        ).value;


    const end =
        document.getElementById(
            'coordEnd'
        ).value;


    const reason =
        document.getElementById(
            'coordCorrectionReason'
        ).value.trim();


    const days =
        Array.from(
            document.querySelectorAll(
                '.coord-day-checkbox:checked'
            )
        ).map(check => check.value);


    if (
        !career ||
        !subject ||
        days.length === 0 ||
        !start ||
        !end
    ) {

        alert(
            'Completa toda la información de la carga académica.'
        );

        return;

    }


    if (!reason) {

        alert(
            'Debes indicar el motivo de la corrección.'
        );

        return;

    }


    if (
        start < '07:00' ||
        end > '22:00'
    ) {

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


    const index =
        academicLoads.findIndex(
            item =>
                String(item.id) === String(id)
        );


    if (index === -1) return;


    const original =
        JSON.parse(
            JSON.stringify(
                academicLoads[index]
            )
        );


    const year =
        original.year;

    const period =
        original.period;


    const overlap =
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


    if (overlap) {

        alert(
            'La corrección genera un conflicto con otra clase registrada.'
        );

        return;

    }


    academicLoads[index] = {

        ...academicLoads[index],

        career,
        subject,
        section,
        days,
        start,
        end,

        updatedAt:
            new Date().toISOString(),

        correctedByCoordination: true

    };


    corrections.unshift({

        id: Date.now(),

        teacherId: DOCENTE_ID,

        teacherName: DOCENTE_NAME,

        loadId: original.id,

        reason,

        previous: original,

        current:
            academicLoads[index],

        correctedBy:
            'Coordinación Académica',

        correctedAt:
            new Date().toISOString()

    });


    localStorage.setItem(
        LOAD_STORAGE_KEY,
        JSON.stringify(academicLoads)
    );


    localStorage.setItem(
        CORRECTION_STORAGE_KEY,
        JSON.stringify(corrections)
    );


    bootstrap.Modal
        .getInstance(
            document.getElementById(
                'coordLoadModal'
            )
        )
        .hide();


    renderCoordinatorLoad();

}


/* =========================================
   HISTORIAL
========================================= */

function renderCorrectionHistory() {

    const container =
        document.getElementById(
            'coordCorrectionHistory'
        );


    const teacherCorrections =
        corrections.filter(item =>
            String(item.teacherId) ===
            String(DOCENTE_ID)
        );


    if (teacherCorrections.length === 0) {

        container.innerHTML = `

            <div class="coord-history-empty">

                <i class="bi bi-shield-check"></i>

                <strong>
                    Sin correcciones registradas
                </strong>

                <span>
                    Coordinación no ha modificado la carga académica de este docente.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        teacherCorrections.map(item => {

            const date =
                new Date(
                    item.correctedAt
                );


            return `

                <article class="coord-correction-row">

                    <div class="coord-correction-icon">

                        <i class="bi bi-pencil-square"></i>

                    </div>


                    <div class="coord-correction-main">

                        <strong>
                            ${item.current.subject}
                        </strong>

                        <span>
                            ${item.reason}
                        </span>

                    </div>


                    <div class="coord-correction-change">

                        <span>
                            Horario corregido
                        </span>

                        <strong>
                            ${formatTime(item.previous.start)}
                            –
                            ${formatTime(item.previous.end)}

                            →

                            ${formatTime(item.current.start)}
                            –
                            ${formatTime(item.current.end)}
                        </strong>

                    </div>


                    <div class="coord-correction-date">

                        <span>
                            ${date.toLocaleDateString('es-HN')}
                        </span>

                        <small>
                            ${date.toLocaleTimeString(
                                'es-HN',
                                {
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }
                            )}
                        </small>

                    </div>

                </article>

            `;

        }).join('');

}


renderCoordinatorLoad();

</script>

@endpush