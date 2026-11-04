@extends('layouts.student')

@section('title', 'Mis pendientes | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            MI ESPACIO
        </span>

        <h1>
            Mis pendientes
        </h1>

        <p>
            Organiza tus tareas personales y actividades
            relacionadas con la práctica profesional.
        </p>

    </div>


    <button
        type="button"
        class="tasks-new-button"
        data-bs-toggle="modal"
        data-bs-target="#taskModal"
        onclick="prepareNewTask()"
    >
        <i class="bi bi-plus-lg"></i>
        Nuevo pendiente
    </button>

</div>



<!-- INFORMACIÓN -->

<div class="tasks-info-box">

    <div class="tasks-info-icon">

        <i class="bi bi-check2-square"></i>

    </div>

    <div>

        <strong>
            Organiza tu práctica a tu manera
        </strong>

        <p>
            Estos pendientes son personales y te permiten
            llevar control de actividades, recordatorios y
            tareas durante tu proceso de práctica.
        </p>

    </div>

</div>



<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="tasks-stat-card">

            <div class="tasks-stat-icon blue">
                <i class="bi bi-list-task"></i>
            </div>

            <div>
                <span>Pendientes</span>
                <strong id="pendingTasks">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="tasks-stat-card">

            <div class="tasks-stat-icon green">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Completadas</span>
                <strong id="completedTasks">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="tasks-stat-card">

            <div class="tasks-stat-icon gold">
                <i class="bi bi-exclamation-circle"></i>
            </div>

            <div>
                <span>Prioridad alta</span>
                <strong id="highPriorityTasks">0</strong>
            </div>

        </div>

    </div>

</section>



<!-- BUSCADOR Y FILTROS -->

<section class="tasks-toolbar">

    <div class="tasks-search">

        <i class="bi bi-search"></i>

        <input
            type="text"
            id="taskSearch"
            placeholder="Buscar pendiente..."
            oninput="renderTasks()"
        >

    </div>


    <select
        id="taskStatusFilter"
        class="tasks-filter"
        onchange="renderTasks()"
    >
        <option value="all">
            Todos
        </option>

        <option value="pending">
            Pendientes
        </option>

        <option value="completed">
            Completados
        </option>
    </select>


    <select
        id="taskPriorityFilter"
        class="tasks-filter"
        onchange="renderTasks()"
    >
        <option value="all">
            Todas las prioridades
        </option>

        <option value="high">
            Alta
        </option>

        <option value="medium">
            Media
        </option>

        <option value="low">
            Baja
        </option>
    </select>

</section>



<!-- LISTADO -->

<section
    id="tasksContainer"
    class="tasks-list"
>
</section>



<!-- VACÍO -->

<section
    id="tasksEmpty"
    class="tasks-empty"
    style="display: none;"
>

    <div class="tasks-empty-icon">

        <i class="bi bi-check2-square"></i>

    </div>

    <h2>
        No tienes pendientes
    </h2>

    <p>
        Crea tareas personales para organizar mejor
        las actividades de tu práctica profesional.
    </p>

    <button
        type="button"
        class="tasks-new-button"
        data-bs-toggle="modal"
        data-bs-target="#taskModal"
        onclick="prepareNewTask()"
    >
        <i class="bi bi-plus-lg"></i>
        Crear mi primer pendiente
    </button>

</section>



<!-- MODAL -->

<div
    class="modal fade"
    id="taskModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content tasks-modal">

            <div class="modal-header">

                <div>

                    <span class="section-label">
                        MI ESPACIO
                    </span>

                    <h2
                        class="modal-title"
                        id="taskModalTitle"
                    >
                        Nuevo pendiente
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
                    id="taskId"
                >


                <div class="tasks-form-group">

                    <label for="taskTitle">
                        Tarea
                    </label>

                    <input
                        type="text"
                        id="taskTitle"
                        maxlength="100"
                        placeholder="Ej. Revisar capítulo de metodología"
                    >

                </div>


                <div class="tasks-form-group">

                    <label for="taskDescription">
                        Descripción
                    </label>

                    <textarea
                        id="taskDescription"
                        rows="4"
                        maxlength="600"
                        placeholder="Añade información adicional..."
                    ></textarea>

                </div>


                <div class="row g-3">

                    <div class="col-12 col-md-6">

                        <div class="tasks-form-group">

                            <label for="taskDate">
                                Fecha límite
                            </label>

                            <input
                                type="date"
                                id="taskDate"
                            >

                        </div>

                    </div>


                    <div class="col-12 col-md-6">

                        <div class="tasks-form-group">

                            <label for="taskPriority">
                                Prioridad
                            </label>

                            <select id="taskPriority">

                                <option value="medium">
                                    Media
                                </option>

                                <option value="high">
                                    Alta
                                </option>

                                <option value="low">
                                    Baja
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="tasks-form-group">

                    <label for="taskStage">
                        Relacionar con
                    </label>

                    <select id="taskStage">

                        <option value="">
                            General / Personal
                        </option>

                        <option value="Documentos">
                            Documentos
                        </option>

                        <option value="Práctica">
                            Práctica
                        </option>

                        <option value="Visita">
                            Visita
                        </option>

                        <option value="Monografía">
                            Monografía
                        </option>

                        <option value="Defensa">
                            Defensa
                        </option>

                    </select>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="tasks-secondary-button"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>


                <button
                    type="button"
                    class="tasks-save-button"
                    onclick="saveTask()"
                >

                    <i class="bi bi-check2"></i>

                    Guardar pendiente

                </button>

            </div>

        </div>

    </div>

</div>


@endsection



@push('scripts')

<script>

const TASKS_STORAGE_KEY =
    'unicah_practica_0801202200458_tasks';


let tasks =
    JSON.parse(
        localStorage.getItem(TASKS_STORAGE_KEY)
    ) || [];


/* ========================================
   PERSISTENCIA
======================================== */

function persistTasks() {

    localStorage.setItem(
        TASKS_STORAGE_KEY,
        JSON.stringify(tasks)
    );

}


/* ========================================
   NUEVA
======================================== */

function prepareNewTask() {

    document.getElementById('taskId').value = '';

    document.getElementById('taskTitle').value = '';

    document.getElementById('taskDescription').value = '';

    document.getElementById('taskDate').value = '';

    document.getElementById('taskPriority').value = 'medium';

    document.getElementById('taskStage').value = '';

    document.getElementById('taskModalTitle').textContent =
        'Nuevo pendiente';

}


/* ========================================
   GUARDAR
======================================== */

function saveTask() {

    const id =
        document.getElementById('taskId').value;

    const title =
        document.getElementById('taskTitle')
            .value
            .trim();

    const description =
        document.getElementById('taskDescription')
            .value
            .trim();

    const dueDate =
        document.getElementById('taskDate').value;

    const priority =
        document.getElementById('taskPriority').value;

    const stage =
        document.getElementById('taskStage').value;


    if (!title) {

        alert('Escribe el nombre del pendiente.');

        return;

    }


    const now =
        new Date().toISOString();


    if (id) {

        const index =
            tasks.findIndex(
                task => task.id === id
            );


        if (index !== -1) {

            tasks[index] = {

                ...tasks[index],

                title,

                description,

                dueDate,

                priority,

                stage,

                updatedAt: now

            };

        }

    }
    else {

        tasks.push({

            id:
                'task_' +
                Date.now(),

            title,

            description,

            dueDate,

            priority,

            stage,

            completed: false,

            createdAt: now,

            updatedAt: now

        });

    }


    persistTasks();

    renderTasks();


    const modal =
        bootstrap.Modal.getInstance(
            document.getElementById('taskModal')
        );


    modal.hide();

}


/* ========================================
   COMPLETAR
======================================== */

function toggleTask(id) {

    const index =
        tasks.findIndex(
            task => task.id === id
        );


    if (index === -1) {
        return;
    }


    tasks[index].completed =
        !tasks[index].completed;

    tasks[index].updatedAt =
        new Date().toISOString();


    persistTasks();

    renderTasks();

}


/* ========================================
   EDITAR
======================================== */

function editTask(id) {

    const task =
        tasks.find(
            task => task.id === id
        );


    if (!task) {
        return;
    }


    document.getElementById('taskId').value =
        task.id;

    document.getElementById('taskTitle').value =
        task.title;

    document.getElementById('taskDescription').value =
        task.description || '';

    document.getElementById('taskDate').value =
        task.dueDate || '';

    document.getElementById('taskPriority').value =
        task.priority;

    document.getElementById('taskStage').value =
        task.stage || '';


    document.getElementById(
        'taskModalTitle'
    ).textContent =
        'Editar pendiente';


    new bootstrap.Modal(
        document.getElementById('taskModal')
    ).show();

}


/* ========================================
   ELIMINAR
======================================== */

function deleteTask(id) {

    const task =
        tasks.find(
            task => task.id === id
        );


    if (!task) {
        return;
    }


    const confirmed =
        confirm(
            '¿Deseas eliminar el pendiente "' +
            task.title +
            '"?'
        );


    if (!confirmed) {
        return;
    }


    tasks =
        tasks.filter(
            task => task.id !== id
        );


    persistTasks();

    renderTasks();

}


/* ========================================
   ESCAPAR HTML
======================================== */

function escapeTaskHTML(value) {

    const div =
        document.createElement('div');

    div.textContent =
        value || '';

    return div.innerHTML;

}


/* ========================================
   FECHA
======================================== */

function formatTaskDate(value) {

    if (!value) {
        return 'Sin fecha límite';
    }


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


/* ========================================
   PRIORIDAD
======================================== */

function priorityData(priority) {

    if (priority === 'high') {

        return {
            label: 'Alta',
            className: 'high'
        };

    }


    if (priority === 'low') {

        return {
            label: 'Baja',
            className: 'low'
        };

    }


    return {
        label: 'Media',
        className: 'medium'
    };

}


/* ========================================
   RENDER
======================================== */

function renderTasks() {

    const container =
        document.getElementById(
            'tasksContainer'
        );

    const empty =
        document.getElementById(
            'tasksEmpty'
        );

    const search =
        document.getElementById(
            'taskSearch'
        )
        .value
        .toLowerCase()
        .trim();

    const status =
        document.getElementById(
            'taskStatusFilter'
        )
        .value;

    const priority =
        document.getElementById(
            'taskPriorityFilter'
        )
        .value;


    let filtered =
        [...tasks];


    if (search) {

        filtered =
            filtered.filter(
                task =>

                    task.title
                        .toLowerCase()
                        .includes(search)

                    ||

                    (
                        task.description || ''
                    )
                    .toLowerCase()
                    .includes(search)
            );

    }


    if (status === 'pending') {

        filtered =
            filtered.filter(
                task => !task.completed
            );

    }


    if (status === 'completed') {

        filtered =
            filtered.filter(
                task => task.completed
            );

    }


    if (priority !== 'all') {

        filtered =
            filtered.filter(
                task =>
                    task.priority === priority
            );

    }


    filtered.sort(
        (a, b) => {

            if (a.completed !== b.completed) {

                return a.completed - b.completed;

            }


            if (
                a.dueDate &&
                b.dueDate
            ) {

                return a.dueDate
                    .localeCompare(
                        b.dueDate
                    );

            }


            return new Date(b.updatedAt)
                -
                new Date(a.updatedAt);

        }
    );


    container.innerHTML = '';


    empty.style.display =
        tasks.length === 0
        &&
        !search
        &&
        status === 'all'
        &&
        priority === 'all'
        ?
        'flex'
        :
        'none';


    filtered.forEach(task => {

        const priorityInfo =
            priorityData(
                task.priority
            );


        const article =
            document.createElement(
                'article'
            );


        article.className =
            'personal-task-card'
            +
            (
                task.completed
                ?
                ' completed'
                :
                ''
            );


        article.innerHTML = `

            <button
                type="button"
                class="task-check ${
                    task.completed
                    ?
                    'checked'
                    :
                    ''
                }"
                onclick="toggleTask('${task.id}')"
                title="${
                    task.completed
                    ?
                    'Marcar pendiente'
                    :
                    'Marcar completado'
                }"
            >
                <i class="bi bi-check-lg"></i>
            </button>


            <div class="task-main">

                <div class="task-top">

                    <div>

                        <h3>
                            ${escapeTaskHTML(task.title)}
                        </h3>

                        <div class="task-tags">

                            <span
                                class="task-priority ${priorityInfo.className}"
                            >
                                ${priorityInfo.label}
                            </span>

                            ${
                                task.stage
                                ?
                                `
                                <span class="task-stage">
                                    ${escapeTaskHTML(task.stage)}
                                </span>
                                `
                                :
                                ''
                            }

                        </div>

                    </div>


                    <div class="task-actions">

                        <button
                            type="button"
                            onclick="editTask('${task.id}')"
                            title="Editar"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>


                        <button
                            type="button"
                            onclick="deleteTask('${task.id}')"
                            class="danger"
                            title="Eliminar"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>

                    </div>

                </div>


                ${
                    task.description
                    ?
                    `
                    <p>
                        ${escapeTaskHTML(task.description)}
                    </p>
                    `
                    :
                    ''
                }


                <div class="task-footer">

                    <span>

                        <i class="bi bi-calendar3"></i>

                        ${formatTaskDate(task.dueDate)}

                    </span>


                    <span>

                        ${
                            task.completed
                            ?
                            `
                            <i class="bi bi-check-circle"></i>
                            Completado
                            `
                            :
                            `
                            <i class="bi bi-circle"></i>
                            Pendiente
                            `
                        }

                    </span>

                </div>

            </div>

        `;


        container.appendChild(
            article
        );

    });


    updateTaskSummary();

}


/* ========================================
   RESUMEN
======================================== */

function updateTaskSummary() {

    document.getElementById(
        'pendingTasks'
    ).textContent =
        tasks.filter(
            task => !task.completed
        ).length;


    document.getElementById(
        'completedTasks'
    ).textContent =
        tasks.filter(
            task => task.completed
        ).length;


    document.getElementById(
        'highPriorityTasks'
    ).textContent =
        tasks.filter(
            task =>
                !task.completed
                &&
                task.priority === 'high'
        ).length;

}


document.addEventListener(
    'DOMContentLoaded',
    renderTasks
);

</script>

@endpush