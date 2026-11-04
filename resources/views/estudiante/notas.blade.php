@extends('layouts.student')

@section('title', 'Mis notas | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            MI ESPACIO
        </span>

        <h1>
            Mis notas
        </h1>

        <p>
            Guarda ideas, recordatorios y anotaciones personales
            relacionadas con tu práctica.
        </p>

    </div>


    <button
        type="button"
        class="notes-new-button"
        data-bs-toggle="modal"
        data-bs-target="#noteModal"
        onclick="prepareNewNote()"
    >
        <i class="bi bi-plus-lg"></i>

        Nueva nota
    </button>

</div>



<!-- PRIVACIDAD -->

<div class="notes-private-box">

    <div class="notes-private-icon">

        <i class="bi bi-shield-lock"></i>

    </div>


    <div>

        <strong>
            Tus notas son privadas
        </strong>

        <p>
            Solo tú puedes consultar, crear, editar y eliminar
            las notas guardadas en este espacio.
        </p>

    </div>

</div>



<!-- RESUMEN -->

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="notes-stat-card">

            <div class="notes-stat-icon blue">

                <i class="bi bi-sticky"></i>

            </div>

            <div>

                <span>
                    Total de notas
                </span>

                <strong id="totalNotes">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="notes-stat-card">

            <div class="notes-stat-icon gold">

                <i class="bi bi-pin-angle"></i>

            </div>

            <div>

                <span>
                    Notas fijadas
                </span>

                <strong id="pinnedNotes">
                    0
                </strong>

            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="notes-stat-card">

            <div class="notes-stat-icon green">

                <i class="bi bi-clock-history"></i>

            </div>

            <div>

                <span>
                    Última actualización
                </span>

                <strong
                    id="lastNoteUpdate"
                    class="notes-stat-date"
                >
                    Sin actividad
                </strong>

            </div>

        </div>

    </div>

</section>



<!-- BUSCADOR -->

<section class="notes-toolbar">

    <div class="notes-search">

        <i class="bi bi-search"></i>

        <input
            type="text"
            id="noteSearch"
            placeholder="Buscar en mis notas..."
            oninput="renderNotes()"
        >

    </div>


    <select
        id="noteFilter"
        class="notes-filter"
        onchange="renderNotes()"
    >

        <option value="all">
            Todas las notas
        </option>

        <option value="pinned">
            Solo fijadas
        </option>

        <option value="recent">
            Más recientes
        </option>

    </select>

</section>



<!-- LISTADO -->

<section
    id="notesContainer"
    class="notes-grid"
>
</section>



<!-- ESTADO VACÍO -->

<section
    id="notesEmpty"
    class="notes-empty"
    style="display: none;"
>

    <div class="notes-empty-icon">

        <i class="bi bi-sticky"></i>

    </div>

    <h2>
        Aún no tienes notas
    </h2>

    <p>
        Utiliza este espacio para guardar ideas, recordatorios
        o información útil durante tu práctica profesional.
    </p>

    <button
        type="button"
        class="notes-new-button"
        data-bs-toggle="modal"
        data-bs-target="#noteModal"
        onclick="prepareNewNote()"
    >

        <i class="bi bi-plus-lg"></i>

        Crear mi primera nota

    </button>

</section>



<!-- =====================================
     MODAL DE NOTA
====================================== -->

<div
    class="modal fade"
    id="noteModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content notes-modal">

            <div class="modal-header">

                <div>

                    <span class="section-label">
                        MI ESPACIO
                    </span>

                    <h2
                        class="modal-title"
                        id="noteModalTitle"
                    >
                        Nueva nota
                    </h2>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"
                ></button>

            </div>


            <div class="modal-body">

                <input
                    type="hidden"
                    id="noteId"
                >


                <div class="notes-form-group">

                    <label for="noteTitle">
                        Título
                    </label>

                    <input
                        type="text"
                        id="noteTitle"
                        maxlength="80"
                        placeholder="Ej. Ideas para mi monografía"
                    >

                </div>


                <div class="notes-form-group">

                    <label for="noteContent">
                        Nota
                    </label>

                    <textarea
                        id="noteContent"
                        rows="7"
                        maxlength="1500"
                        placeholder="Escribe aquí tu anotación..."
                    ></textarea>

                    <small>
                        Máximo 1,500 caracteres.
                    </small>

                </div>


                <label class="notes-pin-option">

                    <input
                        type="checkbox"
                        id="notePinned"
                    >

                    <span>

                        <i class="bi bi-pin-angle"></i>

                        Fijar esta nota

                    </span>

                </label>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="notes-secondary-button"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>


                <button
                    type="button"
                    class="notes-save-button"
                    onclick="saveNote()"
                >

                    <i class="bi bi-check2"></i>

                    Guardar nota

                </button>

            </div>

        </div>

    </div>

</div>


@endsection



@push('scripts')

<script>

const NOTES_STORAGE_KEY =
    'unicah_practica_0801202200458_notes';


let notes =
    JSON.parse(
        localStorage.getItem(NOTES_STORAGE_KEY)
    ) || [];


/* ========================================
   GUARDAR LOCALMENTE
======================================== */

function persistNotes() {

    localStorage.setItem(
        NOTES_STORAGE_KEY,
        JSON.stringify(notes)
    );

}


/* ========================================
   NUEVA NOTA
======================================== */

function prepareNewNote() {

    document.getElementById('noteId').value = '';

    document.getElementById('noteTitle').value = '';

    document.getElementById('noteContent').value = '';

    document.getElementById('notePinned').checked = false;

    document.getElementById('noteModalTitle').textContent =
        'Nueva nota';

}


/* ========================================
   GUARDAR / EDITAR
======================================== */

function saveNote() {

    const id =
        document.getElementById('noteId').value;

    const title =
        document.getElementById('noteTitle')
            .value
            .trim();

    const content =
        document.getElementById('noteContent')
            .value
            .trim();

    const pinned =
        document.getElementById('notePinned')
            .checked;


    if (!title) {

        alert('Escribe un título para la nota.');

        return;

    }


    if (!content) {

        alert('Escribe el contenido de la nota.');

        return;

    }


    const now =
        new Date().toISOString();


    if (id) {

        const index =
            notes.findIndex(
                note => note.id === id
            );


        if (index !== -1) {

            notes[index] = {

                ...notes[index],

                title,

                content,

                pinned,

                updatedAt: now

            };

        }

    }
    else {

        notes.push({

            id:
                'note_' +
                Date.now(),

            title,

            content,

            pinned,

            createdAt: now,

            updatedAt: now

        });

    }


    persistNotes();

    renderNotes();


    const modal =
        bootstrap.Modal.getInstance(
            document.getElementById('noteModal')
        );


    modal.hide();

}


/* ========================================
   EDITAR
======================================== */

function editNote(id) {

    const note =
        notes.find(
            note => note.id === id
        );


    if (!note) {
        return;
    }


    document.getElementById('noteId').value =
        note.id;

    document.getElementById('noteTitle').value =
        note.title;

    document.getElementById('noteContent').value =
        note.content;

    document.getElementById('notePinned').checked =
        note.pinned;


    document.getElementById('noteModalTitle').textContent =
        'Editar nota';


    const modal =
        new bootstrap.Modal(
            document.getElementById('noteModal')
        );


    modal.show();

}


/* ========================================
   ELIMINAR
======================================== */

function deleteNote(id) {

    const note =
        notes.find(
            note => note.id === id
        );


    if (!note) {
        return;
    }


    const confirmed =
        confirm(
            '¿Deseas eliminar la nota "' +
            note.title +
            '"?'
        );


    if (!confirmed) {
        return;
    }


    notes =
        notes.filter(
            note => note.id !== id
        );


    persistNotes();

    renderNotes();

}


/* ========================================
   FIJAR / DESFIJAR
======================================== */

function togglePin(id) {

    const index =
        notes.findIndex(
            note => note.id === id
        );


    if (index === -1) {
        return;
    }


    notes[index].pinned =
        !notes[index].pinned;

    notes[index].updatedAt =
        new Date().toISOString();


    persistNotes();

    renderNotes();

}


/* ========================================
   FORMATO FECHA
======================================== */

function formatDate(dateString) {

    const date =
        new Date(dateString);


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
   EVITAR HTML INYECTADO
======================================== */

function escapeHTML(value) {

    const element =
        document.createElement('div');

    element.textContent = value;

    return element.innerHTML;

}


/* ========================================
   RENDER
======================================== */

function renderNotes() {

    const container =
        document.getElementById(
            'notesContainer'
        );

    const empty =
        document.getElementById(
            'notesEmpty'
        );

    const search =
        document.getElementById(
            'noteSearch'
        )
        .value
        .toLowerCase()
        .trim();

    const filter =
        document.getElementById(
            'noteFilter'
        )
        .value;


    let filtered =
        [...notes];


    if (search) {

        filtered =
            filtered.filter(
                note =>

                    note.title
                        .toLowerCase()
                        .includes(search)

                    ||

                    note.content
                        .toLowerCase()
                        .includes(search)
            );

    }


    if (filter === 'pinned') {

        filtered =
            filtered.filter(
                note => note.pinned
            );

    }


    filtered.sort(
        (a, b) => {

            if (a.pinned !== b.pinned) {

                return b.pinned - a.pinned;

            }

            return new Date(b.updatedAt)
                - new Date(a.updatedAt);

        }
    );


    container.innerHTML = '';


    if (filtered.length === 0) {

        empty.style.display =
            search || filter !== 'all'
                ? 'none'
                : 'flex';

    }
    else {

        empty.style.display =
            'none';

    }


    filtered.forEach(note => {

        const article =
            document.createElement(
                'article'
            );


        article.className =
            'personal-note-card'
            +
            (note.pinned
                ? ' pinned'
                : '');


        article.innerHTML = `

            <div class="personal-note-header">

                <div>

                    ${
                        note.pinned
                        ?
                        `
                        <span class="personal-note-pin">
                            <i class="bi bi-pin-angle-fill"></i>
                            Fijada
                        </span>
                        `
                        :
                        ''
                    }

                    <h3>
                        ${escapeHTML(note.title)}
                    </h3>

                </div>


                <div class="personal-note-actions">

                    <button
                        type="button"
                        onclick="togglePin('${note.id}')"
                        title="${
                            note.pinned
                            ?
                            'Desfijar nota'
                            :
                            'Fijar nota'
                        }"
                    >
                        <i
                            class="bi ${
                                note.pinned
                                ?
                                'bi-pin-angle-fill'
                                :
                                'bi-pin-angle'
                            }"
                        ></i>
                    </button>


                    <button
                        type="button"
                        onclick="editNote('${note.id}')"
                        title="Editar"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>


                    <button
                        type="button"
                        onclick="deleteNote('${note.id}')"
                        title="Eliminar"
                        class="danger"
                    >
                        <i class="bi bi-trash3"></i>
                    </button>

                </div>

            </div>


            <p class="personal-note-content">
                ${escapeHTML(note.content)}
            </p>


            <div class="personal-note-footer">

                <span>
                    <i class="bi bi-clock"></i>

                    Actualizada
                    ${formatDate(note.updatedAt)}
                </span>

            </div>

        `;


        container.appendChild(
            article
        );

    });


    updateNotesSummary();

}


/* ========================================
   RESUMEN
======================================== */

function updateNotesSummary() {

    document.getElementById(
        'totalNotes'
    ).textContent =
        notes.length;


    document.getElementById(
        'pinnedNotes'
    ).textContent =
        notes.filter(
            note => note.pinned
        ).length;


    const last =
        [...notes]
        .sort(
            (a, b) =>
                new Date(b.updatedAt)
                -
                new Date(a.updatedAt)
        )[0];


    document.getElementById(
        'lastNoteUpdate'
    ).textContent =
        last
        ?
        formatDate(
            last.updatedAt
        )
        :
        'Sin actividad';

}


document.addEventListener(
    'DOMContentLoaded',
    renderNotes
);

</script>

@endpush