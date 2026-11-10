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
    >
        <i class="bi bi-plus-lg"></i>
        Nueva empresa
    </button>

</div>



@if (session('status'))
    <div class="alert alert-success" role="status">{{ session('status') }}</div>
@endif

<section class="row g-3 mb-4">

    <div class="col-12 col-md-4">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-buildings"></i>
            </div>

            <div>
                <span>Empresas registradas</span>
                <strong id="companyStatTotal">{{ $empresas->count() }}</strong>
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
                <strong id="companyStatStudents">{{ $practicantes }}</strong>
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
                <strong id="companyStatResponsibles">{{ $empresas->sum('responsibles_count') }}</strong>
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
            >

        </div>


        <select
            id="companySector"
            class="form-select"
        >
            <option value="">Todos los sectores</option>
            @foreach ($empresas->pluck('sector')->filter()->unique()->sort() as $sector)
                <option>{{ $sector }}</option>
            @endforeach

        </select>

    </div>


    <div class="coord-company-list" id="companiesList">
        @foreach ($empresas as $empresa)
            <article class="coord-company-row" data-name="{{ $empresa->official_name }} {{ $empresa->commercial_name }}" data-sector="{{ $empresa->sector }}">
                <div class="coord-company-icon"><i class="bi bi-building"></i></div>
                <div class="coord-company-main">
                    <span>NOMBRE OFICIAL</span>
                    <strong>{{ $empresa->official_name }}</strong>
                    <small>{{ $empresa->commercial_name ? 'Nombre comercial: '.$empresa->commercial_name : 'Sin nombre comercial' }}</small>
                </div>
                <div>
                    <span class="coord-company-label">Sector</span>
                    <strong class="coord-company-value">{{ $empresa->sector ?? 'Sin sector' }}</strong>
                </div>
                <div>
                    <span class="coord-company-label">Practicantes</span>
                    <strong class="coord-company-value">{{ $empresa->students_count }}</strong>
                </div>
                <div>
                    <span class="coord-company-label">Responsables</span>
                    <strong class="coord-company-value">{{ $empresa->responsibles_count }}</strong>
                </div>
                <span class="coord-company-status {{ $empresa->status === 'ACTIVE' ? 'active' : 'inactive' }}">
                    {{ ['ACTIVE' => 'Activa', 'INACTIVE' => 'Inactiva'][$empresa->status] ?? $empresa->status }}
                </span>
                <div class="coord-company-actions">
                    <button type="button" data-bs-toggle="modal" data-bs-target="#companyModal"
                        data-record-id="{{ $empresa->id }}" data-action="{{ route('coordinacion.empresas.update', $empresa) }}"
                        data-fields="{{ json_encode($empresa->only(['official_name', 'commercial_name', 'rtn', 'sector', 'address', 'city', 'department', 'phone', 'email', 'status'])) }}"
                        title="Editar empresa">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="{{ route('coordinacion.empresa', $empresa) }}" class="coord-teacher-view">
                        <i class="bi bi-eye"></i> Ver empresa
                    </a>
                </div>
            </article>
        @endforeach
        <div class="teacher-load-empty {{ $empresas->isEmpty() ? '' : 'd-none' }}" id="noCompanies">
            <i class="bi bi-buildings"></i>
            <strong>No se encontraron empresas</strong>
            <span>Cambia los filtros o registra una nueva empresa.</span>
        </div>
    </div>

</section>



{{-- MODAL EMPRESA --}}

<div
    class="modal fade"
    id="companyModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <form class="modal-content coord-company-modal" method="POST"
            action="{{ route('coordinacion.empresas.store') }}"
            data-record-modal
            data-create-action="{{ route('coordinacion.empresas.store') }}"
            data-create-title="Nueva empresa" data-edit-title="Editar empresa"
            data-edit-id="{{ request()->string('editar') }}"
            data-validation-failed="{{ $errors->any() ? 1 : 0 }}"
            data-old-input="{{ json_encode(session()->getOldInput()) }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" disabled>

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
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <input
                    type="hidden"
                    id="companyId" name="_record"
                >


                <div class="row g-3">

                    <div class="col-12 col-md-8">

                        <label class="form-label">
                            Nombre oficial / Razón social *
                        </label>

                        <input
                            type="text"
                            id="companyLegalName" name="official_name" required
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
                            id="companyCommercialName" name="commercial_name"
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
                            id="companyRtn" name="rtn"
                            class="form-control"
                            placeholder="RTN de la empresa"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Sector *
                        </label>

                        <select
                            id="companyFormSector" name="sector" required
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
                            id="companyAddress" name="address"
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
                            id="companyCity" name="city"
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
                            id="companyDepartment" name="department"
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
                            id="companyPhone" name="phone"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Correo
                        </label>

                        <input
                            type="email"
                            id="companyEmail" name="email"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Estado
                        </label>

                        <select
                            id="companyStatus" name="status"
                            class="form-select"
                        >
                            <option value="ACTIVE">
                                Activa
                            </option>

                            <option value="INACTIVE">
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
                    type="submit" class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Guardar empresa
                </button>

            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/coordination-record-modal.js') }}"></script>
<script>
    const companySearch = document.getElementById('companySearch');
    const companySector = document.getElementById('companySector');
    const companyRows = Array.from(document.querySelectorAll('#companiesList .coord-company-row'));

    function filterCompanies() {
        const search = companySearch.value.trim().toLocaleLowerCase();
        const sector = companySector.value;
        let matches = 0;

        for (const row of companyRows) {
            const nameMatches = row.dataset.name.toLocaleLowerCase().includes(search);
            const sectorMatches = !sector || row.dataset.sector === sector;
            row.classList.toggle('d-none', !(nameMatches && sectorMatches));
            if (nameMatches && sectorMatches) matches++;
        }

        document.getElementById('noCompanies').classList.toggle('d-none', matches > 0);
    }

    companySearch.addEventListener('input', filterCompanies);
    companySector.addEventListener('change', filterCompanies);
</script>
@endpush
