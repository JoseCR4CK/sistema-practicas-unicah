@extends('layouts.app')

@section('title', 'Detalle de empresa | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            EMPRESA
        </span>

        <h1 id="companyDetailTitle">
            Empresa
        </h1>

        <p>
            Información institucional, responsables
            y practicantes vinculados.
        </p>

    </div>


    <div class="coord-company-detail-actions">

        <a
            href="{{ route('coordinacion.empresas') }}"
            class="btn btn-light coord-back-button"
        >
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>


        <a
            href="{{ route('coordinacion.empresas') }}?editar={{ $empresaId }}"
            class="btn btn-primary"
        >
            <i class="bi bi-pencil"></i>
            Editar empresa
        </a>

    </div>

</div>



<div id="companyNotFound" class="d-none">

    <section class="content-card">

        <div class="teacher-load-empty">

            <i class="bi bi-exclamation-circle"></i>

            <strong>
                Empresa no encontrada
            </strong>

            <span>
                El registro solicitado no existe.
            </span>

        </div>

    </section>

</div>



<div id="companyDetailContent">


    {{-- PERFIL --}}

    <section class="coord-company-profile mb-4">

        <div class="coord-company-profile-icon">
            <i class="bi bi-buildings"></i>
        </div>


        <div class="coord-company-profile-main">

            <span>
                NOMBRE OFICIAL / RAZÓN SOCIAL
            </span>

            <strong id="companyDetailLegalName"></strong>

            <small id="companyDetailCommercialName"></small>

        </div>


        <div class="coord-company-profile-data">

            <div>
                <span>Sector</span>
                <strong id="companyDetailSector"></strong>
            </div>

            <div>
                <span>Estado</span>
                <strong id="companyDetailStatus"></strong>
            </div>

            <div>
                <span>Ubicación</span>
                <strong id="companyDetailLocation"></strong>
            </div>

        </div>

    </section>



    {{-- INFORMACIÓN INSTITUCIONAL --}}

    <section class="content-card mb-4">

        <div class="card-header">

            <div>
                <h2>Información institucional</h2>

                <p>
                    Datos generales registrados para esta empresa.
                </p>
            </div>

        </div>


        <div class="coord-company-data-grid">

            <div>
                <span>RTN</span>
                <strong id="companyDetailRtn"></strong>
            </div>

            <div>
                <span>Teléfono</span>
                <strong id="companyDetailPhone"></strong>
            </div>

            <div>
                <span>Correo</span>
                <strong id="companyDetailEmail"></strong>
            </div>

            <div>
                <span>Dirección</span>
                <strong id="companyDetailAddress"></strong>
            </div>

        </div>

    </section>



    {{-- RESPONSABLES --}}

    <section class="content-card mb-4">

        <div class="card-header">

            <div>

                <h2>
                    Responsables de practicantes
                </h2>

                <p>
                    Una misma persona puede supervisar
                    estudiantes de diferentes carreras.
                </p>

            </div>


            <button
                type="button"
                class="btn btn-primary coord-small-action"
                data-bs-toggle="modal"
                data-bs-target="#responsibleModal"
                onclick="prepareNewResponsible()"
            >
                <i class="bi bi-person-plus"></i>
                Agregar responsable
            </button>

        </div>


        <div id="responsiblesList"></div>

    </section>



    {{-- PRACTICANTES --}}

    <section class="content-card">

        <div class="card-header">

            <div>

                <h2>
                    Practicantes actuales
                </h2>

                <p>
                    Estudiantes actualmente vinculados
                    a esta empresa.
                </p>

            </div>

        </div>


        <div id="companyStudentsList"></div>

    </section>



    <div class="coord-company-structure-note">

        <i class="bi bi-diagram-3"></i>

        <div>

            <strong>
                Estructura centralizada
            </strong>

            <span>
                Los estudiantes se vinculan a esta empresa
                desde su proceso de práctica. No se asignan
                directamente desde este módulo.
            </span>

        </div>

    </div>

</div>



{{-- MODAL RESPONSABLE --}}

<div
    class="modal fade"
    id="responsibleModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content coord-company-modal">

            <div class="modal-header">

                <div>

                    <span class="student-home-eyebrow">
                        RESPONSABLE
                    </span>

                    <h5
                        class="modal-title"
                        id="responsibleModalTitle"
                    >
                        Agregar responsable
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
                    id="responsibleId"
                >


                <div class="row g-3">

                    <div class="col-12">

                        <label class="form-label">
                            Nombre completo *
                        </label>

                        <input
                            type="text"
                            id="responsibleName"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Cargo *
                        </label>

                        <input
                            type="text"
                            id="responsiblePosition"
                            class="form-control"
                            placeholder="Ej. Jefe inmediato"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Correo
                        </label>

                        <input
                            type="email"
                            id="responsibleEmail"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            id="responsiblePhone"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Estado
                        </label>

                        <select
                            id="responsibleStatus"
                            class="form-select"
                        >

                            <option value="Activo">
                                Activo
                            </option>

                            <option value="Inactivo">
                                Inactivo
                            </option>

                        </select>

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
                    onclick="saveResponsible()"
                >
                    <i class="bi bi-check-lg"></i>
                    Guardar responsable
                </button>

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/companies-store.js') }}"></script>

<script>

const COMPANY_ID = {{ $empresaId }};

let currentCompany = null;


function getResponsibleName(id) {

    if (!id) {
        return 'Sin responsable asignado';
    }


    const responsible =
        currentCompany.responsables.find(item =>
            String(item.id) === String(id)
        );


    return responsible
        ? responsible.nombre
        : 'Sin responsable asignado';

}


function renderCompanyDetail() {

    currentCompany =
        CompanyStore.getById(
            COMPANY_ID
        );


    if (!currentCompany) {

        document
            .getElementById('companyDetailContent')
            .classList
            .add('d-none');


        document
            .getElementById('companyNotFound')
            .classList
            .remove('d-none');

        return;

    }


    document.getElementById(
        'companyDetailTitle'
    ).textContent =
        currentCompany.nombre_comercial ||
        currentCompany.nombre_oficial;


    document.getElementById(
        'companyDetailLegalName'
    ).textContent =
        currentCompany.nombre_oficial;


    document.getElementById(
        'companyDetailCommercialName'
    ).textContent =
        currentCompany.nombre_comercial
            ? `Nombre comercial: ${currentCompany.nombre_comercial}`
            : 'Sin nombre comercial';


    document.getElementById(
        'companyDetailSector'
    ).textContent =
        currentCompany.sector || '—';


    const statusElement =
        document.getElementById(
            'companyDetailStatus'
        );


    statusElement.textContent =
        currentCompany.estado;


    statusElement.className =
        currentCompany.estado === 'Activa'
            ? 'text-success'
            : 'text-secondary';


    const location =
        [
            currentCompany.municipio,
            currentCompany.departamento
        ]
        .filter(Boolean)
        .join(', ');


    document.getElementById(
        'companyDetailLocation'
    ).textContent =
        location || '—';


    document.getElementById(
        'companyDetailRtn'
    ).textContent =
        currentCompany.rtn || 'No registrado';


    document.getElementById(
        'companyDetailPhone'
    ).textContent =
        currentCompany.telefono || 'No registrado';


    document.getElementById(
        'companyDetailEmail'
    ).textContent =
        currentCompany.correo || 'No registrado';


    document.getElementById(
        'companyDetailAddress'
    ).textContent =
        currentCompany.direccion || 'No registrada';


    renderResponsibles();

    renderStudents();

}


function renderResponsibles() {

    const container =
        document.getElementById(
            'responsiblesList'
        );


    const responsibles =
        currentCompany.responsables || [];


    if (responsibles.length === 0) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-person-vcard"></i>

                <strong>
                    No hay responsables registrados
                </strong>

                <span>
                    Agrega el primer responsable
                    de practicantes de esta empresa.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        responsibles.map(responsible => {

            const assigned =
                currentCompany.practicantes
                    .filter(student =>
                        String(student.responsable_id) ===
                        String(responsible.id)
                    )
                    .length;


            const statusClass =
                responsible.estado === 'Activo'
                    ? 'active'
                    : 'inactive';


            return `

                <article class="coord-responsible-row">

                    <div class="coord-responsible-avatar">
                        <i class="bi bi-person"></i>
                    </div>


                    <div>

                        <span>RESPONSABLE</span>

                        <strong>
                            ${responsible.nombre}
                        </strong>

                        <small>
                            ${responsible.cargo}
                        </small>

                    </div>


                    <div>

                        <span>Contacto</span>

                        <strong>
                            ${responsible.correo || 'Sin correo'}
                        </strong>

                        <small>
                            ${responsible.telefono || 'Sin teléfono'}
                        </small>

                    </div>


                    <div>

                        <span>
                            Practicantes asignados
                        </span>

                        <strong>
                            ${assigned}
                        </strong>

                    </div>


                    <span class="coord-company-status ${statusClass}">
                        ${responsible.estado}
                    </span>


                    <button
                        type="button"
                        class="coord-icon-action"
                        onclick="editResponsible(${responsible.id})"
                        title="Editar responsable"
                    >
                        <i class="bi bi-pencil"></i>
                    </button>

                </article>

            `;

        }).join('');

}


function renderStudents() {

    const container =
        document.getElementById(
            'companyStudentsList'
        );


    const students =
        currentCompany.practicantes || [];


    if (students.length === 0) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-people"></i>

                <strong>
                    No hay practicantes vinculados
                </strong>

                <span>
                    Los estudiantes aparecerán aquí
                    cuando su proceso de práctica sea
                    asociado a esta empresa.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        students.map(student => {

            const initials =
                student.nombre
                    .split(' ')
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(word =>
                        word.charAt(0).toUpperCase()
                    )
                    .join('');


            return `

                <article class="coord-company-student">

                    <div class="coord-company-student-avatar">
                        ${initials}
                    </div>


                    <div>

                        <span>ESTUDIANTE</span>

                        <strong>
                            ${student.nombre}
                        </strong>

                        <small>
                            ${student.cuenta}
                        </small>

                    </div>


                    <div>

                        <span>Carrera</span>

                        <strong>
                            ${student.carrera}
                        </strong>

                    </div>


                    <div>

                        <span>Responsable</span>

                        <strong>
                            ${getResponsibleName(
                                student.responsable_id
                            )}
                        </strong>

                    </div>


                    <div>

                        <span>Etapa</span>

                        <strong>
                            ${student.etapa}
                        </strong>

                    </div>


                    <div>

                        <span>Progreso</span>

                        <strong>
                            ${student.progreso}%
                        </strong>

                        <div class="coord-company-progress">

                            <span
                                style="width:${student.progreso}%"
                            ></span>

                        </div>

                    </div>

                </article>

            `;

        }).join('');

}


function prepareNewResponsible() {

    document.getElementById(
        'responsibleId'
    ).value = '';


    document.getElementById(
        'responsibleModalTitle'
    ).textContent =
        'Agregar responsable';


    document.getElementById(
        'responsibleName'
    ).value = '';


    document.getElementById(
        'responsiblePosition'
    ).value = '';


    document.getElementById(
        'responsibleEmail'
    ).value = '';


    document.getElementById(
        'responsiblePhone'
    ).value = '';


    document.getElementById(
        'responsibleStatus'
    ).value =
        'Activo';

}


function editResponsible(id) {

    const responsible =
        currentCompany.responsables.find(item =>
            String(item.id) === String(id)
        );


    if (!responsible) {
        return;
    }


    document.getElementById(
        'responsibleId'
    ).value =
        responsible.id;


    document.getElementById(
        'responsibleName'
    ).value =
        responsible.nombre;


    document.getElementById(
        'responsiblePosition'
    ).value =
        responsible.cargo;


    document.getElementById(
        'responsibleEmail'
    ).value =
        responsible.correo || '';


    document.getElementById(
        'responsiblePhone'
    ).value =
        responsible.telefono || '';


    document.getElementById(
        'responsibleStatus'
    ).value =
        responsible.estado;


    document.getElementById(
        'responsibleModalTitle'
    ).textContent =
        'Editar responsable';


    new bootstrap.Modal(
        document.getElementById(
            'responsibleModal'
        )
    ).show();

}


function saveResponsible() {

    const id =
        document.getElementById(
            'responsibleId'
        ).value;


    const name =
        document
            .getElementById('responsibleName')
            .value
            .trim();


    const position =
        document
            .getElementById('responsiblePosition')
            .value
            .trim();


    const email =
        document
            .getElementById('responsibleEmail')
            .value
            .trim();


    const phone =
        document
            .getElementById('responsiblePhone')
            .value
            .trim();


    const status =
        document.getElementById(
            'responsibleStatus'
        ).value;


    if (!name || !position) {

        alert(
            'El nombre y el cargo del responsable son obligatorios.'
        );

        return;

    }


    if (id) {

        const index =
            currentCompany.responsables
                .findIndex(item =>
                    String(item.id) === String(id)
                );


        if (index === -1) {
            return;
        }


        currentCompany.responsables[index] = {

            ...currentCompany.responsables[index],

            nombre: name,
            cargo: position,
            correo: email,
            telefono: phone,
            estado: status,

            updated_at:
                new Date().toISOString()

        };

    } else {

        currentCompany.responsables.push({

            id: CompanyStore.createId(),

            nombre: name,
            cargo: position,
            correo: email,
            telefono: phone,
            estado: status,

            created_at:
                new Date().toISOString(),

            updated_at:
                new Date().toISOString()

        });

    }


    currentCompany.updated_at =
        new Date().toISOString();


    CompanyStore.saveCompany(
        currentCompany
    );


    bootstrap.Modal
        .getInstance(
            document.getElementById(
                'responsibleModal'
            )
        )
        .hide();


    renderCompanyDetail();

}


renderCompanyDetail();

</script>

@endpush