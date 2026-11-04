@extends('layouts.app')

@section('title', 'Empresas | Coordinación Académica')

@section('content')

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            GESTIÓN DE EMPRESAS
        </span>

        <h1>
            Empresas
        </h1>

        <p>
            Administra las empresas vinculadas a los procesos
            de práctica profesional.
        </p>

    </div>


    <button
        type="button"
        class="btn btn-primary coord-company-add"
        data-bs-toggle="modal"
        data-bs-target="#companyModal"
        onclick="prepareNewCompany()"
    >
        <i class="bi bi-plus-lg"></i>
        Nueva empresa
    </button>

</div>



<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-buildings"></i>
            </div>

            <div>
                <span>Empresas registradas</span>
                <strong id="companyStatTotal">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <span>Practicantes vinculados</span>
                <strong id="companyStatStudents">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-person-vcard"></i>
            </div>

            <div>
                <span>Responsables registrados</span>
                <strong id="companyStatResponsibles">0</strong>
            </div>

        </div>

    </div>

</section>



<section class="content-card">

    <div class="coord-company-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="companySearch"
                placeholder="Buscar por nombre oficial o comercial..."
                oninput="renderCompanies()"
            >

        </div>


        <select
            id="companySector"
            class="form-select"
            onchange="renderCompanies()"
        >

            <option value="">
                Todos los sectores
            </option>

            <option value="Tecnología">
                Tecnología
            </option>

            <option value="Industrial">
                Industrial
            </option>

            <option value="Servicios">
                Servicios
            </option>

            <option value="Comercio">
                Comercio
            </option>

            <option value="Salud">
                Salud
            </option>

            <option value="Educación">
                Educación
            </option>

            <option value="Otro">
                Otro
            </option>

        </select>

    </div>


    <div
        class="coord-company-list"
        id="companiesList"
    ></div>

</section>



{{-- MODAL EMPRESA --}}

<div
    class="modal fade"
    id="companyModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content coord-company-modal">

            <div class="modal-header">

                <div>

                    <span class="student-home-eyebrow">
                        EMPRESA
                    </span>

                    <h5
                        class="modal-title"
                        id="companyModalTitle"
                    >
                        Nueva empresa
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
                    id="companyId"
                >


                <div class="row g-3">

                    <div class="col-12 col-md-8">

                        <label class="form-label">
                            Nombre oficial / Razón social *
                        </label>

                        <input
                            type="text"
                            id="companyLegalName"
                            class="form-control"
                            placeholder="Ej. Grupo Tecnológico S.A."
                        >

                    </div>


                    <div class="col-12 col-md-4">

                        <label class="form-label">
                            Nombre comercial
                        </label>

                        <input
                            type="text"
                            id="companyCommercialName"
                            class="form-control"
                            placeholder="Ej. GT Solutions"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            RTN
                        </label>

                        <input
                            type="text"
                            id="companyRtn"
                            class="form-control"
                            placeholder="RTN de la empresa"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Sector *
                        </label>

                        <select
                            id="companyFormSector"
                            class="form-select"
                        >

                            <option value="">
                                Selecciona un sector
                            </option>

                            <option value="Tecnología">
                                Tecnología
                            </option>

                            <option value="Industrial">
                                Industrial
                            </option>

                            <option value="Servicios">
                                Servicios
                            </option>

                            <option value="Comercio">
                                Comercio
                            </option>

                            <option value="Salud">
                                Salud
                            </option>

                            <option value="Educación">
                                Educación
                            </option>

                            <option value="Otro">
                                Otro
                            </option>

                        </select>

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Dirección
                        </label>

                        <input
                            type="text"
                            id="companyAddress"
                            class="form-control"
                            placeholder="Dirección de la empresa"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Municipio / Ciudad
                        </label>

                        <input
                            type="text"
                            id="companyCity"
                            class="form-control"
                            value="Danlí"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Departamento
                        </label>

                        <input
                            type="text"
                            id="companyDepartment"
                            class="form-control"
                            value="El Paraíso"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            id="companyPhone"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Correo
                        </label>

                        <input
                            type="email"
                            id="companyEmail"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Estado
                        </label>

                        <select
                            id="companyStatus"
                            class="form-select"
                        >
                            <option value="Activa">
                                Activa
                            </option>

                            <option value="Inactiva">
                                Inactiva
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
                    onclick="saveCompany()"
                >
                    <i class="bi bi-check-lg"></i>
                    Guardar empresa
                </button>

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script src="{{ asset('js/companies-store.js') }}"></script>

<script>

function prepareNewCompany() {

    document.getElementById('companyId').value = '';

    document.getElementById('companyModalTitle')
        .textContent = 'Nueva empresa';


    document.getElementById('companyLegalName').value = '';
    document.getElementById('companyCommercialName').value = '';
    document.getElementById('companyRtn').value = '';
    document.getElementById('companyFormSector').value = '';
    document.getElementById('companyAddress').value = '';

    document.getElementById('companyCity').value =
        'Danlí';

    document.getElementById('companyDepartment').value =
        'El Paraíso';

    document.getElementById('companyPhone').value = '';
    document.getElementById('companyEmail').value = '';

    document.getElementById('companyStatus').value =
        'Activa';

}


function openEditCompany(id) {

    const company =
        CompanyStore.getById(id);


    if (!company) {
        return;
    }


    document.getElementById('companyId').value =
        company.id;

    document.getElementById('companyLegalName').value =
        company.nombre_oficial || '';

    document.getElementById('companyCommercialName').value =
        company.nombre_comercial || '';

    document.getElementById('companyRtn').value =
        company.rtn || '';

    document.getElementById('companyFormSector').value =
        company.sector || '';

    document.getElementById('companyAddress').value =
        company.direccion || '';

    document.getElementById('companyCity').value =
        company.municipio || '';

    document.getElementById('companyDepartment').value =
        company.departamento || '';

    document.getElementById('companyPhone').value =
        company.telefono || '';

    document.getElementById('companyEmail').value =
        company.correo || '';

    document.getElementById('companyStatus').value =
        company.estado || 'Activa';


    document.getElementById('companyModalTitle')
        .textContent = 'Editar empresa';


    new bootstrap.Modal(
        document.getElementById('companyModal')
    ).show();

}


function saveCompany() {

    const id =
        document.getElementById('companyId').value;


    const legalName =
        document
            .getElementById('companyLegalName')
            .value
            .trim();


    const commercialName =
        document
            .getElementById('companyCommercialName')
            .value
            .trim();


    const rtn =
        document
            .getElementById('companyRtn')
            .value
            .trim();


    const sector =
        document.getElementById(
            'companyFormSector'
        ).value;


    const address =
        document
            .getElementById('companyAddress')
            .value
            .trim();


    const city =
        document
            .getElementById('companyCity')
            .value
            .trim();


    const department =
        document
            .getElementById('companyDepartment')
            .value
            .trim();


    const phone =
        document
            .getElementById('companyPhone')
            .value
            .trim();


    const email =
        document
            .getElementById('companyEmail')
            .value
            .trim();


    const status =
        document.getElementById(
            'companyStatus'
        ).value;


    if (!legalName || !sector) {

        alert(
            'El nombre oficial y el sector son obligatorios.'
        );

        return;
    }


    const companies =
        CompanyStore.getAll();


    const duplicated =
        companies.some(company => {

            if (
                id &&
                String(company.id) === String(id)
            ) {
                return false;
            }


            return (
                company.nombre_oficial
                    .toLowerCase()
                    .trim()
                ===
                legalName
                    .toLowerCase()
                    .trim()
            );

        });


    if (duplicated) {

        alert(
            'Ya existe una empresa registrada con ese nombre oficial.'
        );

        return;
    }


    let company;


    if (id) {

        company =
            CompanyStore.getById(id);


        if (!company) {
            return;
        }


        company = {

            ...company,

            nombre_oficial: legalName,
            nombre_comercial: commercialName,
            rtn,
            sector,
            direccion: address,
            municipio: city,
            departamento: department,
            telefono: phone,
            correo: email,
            estado: status,

            updated_at:
                new Date().toISOString()

        };

    } else {

        company = {

            id: CompanyStore.createId(),

            nombre_oficial: legalName,
            nombre_comercial: commercialName,
            rtn,
            sector,
            direccion: address,
            municipio: city,
            departamento: department,
            telefono: phone,
            correo: email,
            estado: status,

            responsables: [],
            practicantes: [],

            created_at:
                new Date().toISOString(),

            updated_at:
                new Date().toISOString()

        };

    }


    CompanyStore.saveCompany(company);


    bootstrap.Modal
        .getInstance(
            document.getElementById('companyModal')
        )
        .hide();


    renderCompanies();

}


function renderStats(companies) {

    document.getElementById(
        'companyStatTotal'
    ).textContent =
        companies.length;


    const students =
        companies.reduce(
            (total, company) =>
                total +
                (company.practicantes?.length || 0),
            0
        );


    document.getElementById(
        'companyStatStudents'
    ).textContent =
        students;


    const responsibles =
        companies.reduce(
            (total, company) =>
                total +
                (company.responsables?.length || 0),
            0
        );


    document.getElementById(
        'companyStatResponsibles'
    ).textContent =
        responsibles;

}


function renderCompanies() {

    const companies =
        CompanyStore.getAll();


    renderStats(companies);


    const search =
        document
            .getElementById('companySearch')
            .value
            .toLowerCase()
            .trim();


    const sector =
        document.getElementById(
            'companySector'
        ).value;


    const filtered =
        companies.filter(company => {

            const text =
                `${company.nombre_oficial} ${company.nombre_comercial}`
                    .toLowerCase();


            const matchesSearch =
                text.includes(search);


            const matchesSector =
                !sector ||
                company.sector === sector;


            return (
                matchesSearch &&
                matchesSector
            );

        });


    const container =
        document.getElementById(
            'companiesList'
        );


    if (filtered.length === 0) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-buildings"></i>

                <strong>
                    No se encontraron empresas
                </strong>

                <span>
                    Cambia los filtros o registra una nueva empresa.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(company => {

            const responsibles =
                company.responsables?.length || 0;

            const students =
                company.practicantes?.length || 0;


            const statusClass =
                company.estado === 'Activa'
                    ? 'active'
                    : 'inactive';


            return `

                <article class="coord-company-row">

                    <div class="coord-company-icon">
                        <i class="bi bi-building"></i>
                    </div>


                    <div class="coord-company-main">

                        <span>
                            NOMBRE OFICIAL
                        </span>

                        <strong>
                            ${company.nombre_oficial}
                        </strong>

                        <small>
                            ${
                                company.nombre_comercial
                                    ? `Nombre comercial: ${company.nombre_comercial}`
                                    : 'Sin nombre comercial'
                            }
                        </small>

                    </div>


                    <div>

                        <span class="coord-company-label">
                            Sector
                        </span>

                        <strong class="coord-company-value">
                            ${company.sector}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-company-label">
                            Practicantes
                        </span>

                        <strong class="coord-company-value">
                            ${students}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-company-label">
                            Responsables
                        </span>

                        <strong class="coord-company-value">
                            ${responsibles}
                        </strong>

                    </div>


                    <span class="coord-company-status ${statusClass}">
                        ${company.estado}
                    </span>


                    <div class="coord-company-actions">

                        <button
                            type="button"
                            onclick="openEditCompany(${company.id})"
                            title="Editar empresa"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>


                        <a
                            href="/coordinacion/empresas/${company.id}"
                            class="coord-teacher-view"
                        >
                            <i class="bi bi-eye"></i>
                            Ver empresa
                        </a>

                    </div>

                </article>

            `;

        }).join('');

}


renderCompanies();


/*
|--------------------------------------------------------------------------
| ABRIR EDICIÓN DESDE DETALLE
|--------------------------------------------------------------------------
*/

const query =
    new URLSearchParams(
        window.location.search
    );


const editCompanyId =
    query.get('editar');


if (editCompanyId) {

    const company =
        CompanyStore.getById(
            editCompanyId
        );


    if (company) {

        setTimeout(() => {
            openEditCompany(editCompanyId);
        }, 100);

    }

}

</script>

@endpush