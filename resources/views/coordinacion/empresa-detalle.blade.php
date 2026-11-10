@extends('layouts.app')

@section('title', 'Detalle de empresa | UNICAH')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            EMPRESA
        </span>

        <h1>{{ $empresa->official_name }}</h1>

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
            href="{{ route('coordinacion.empresas', ['editar' => $empresa->id]) }}"
            class="btn btn-primary"
        >
            <i class="bi bi-pencil"></i>
            Editar empresa
        </a>

    </div>

</div>



@if (session('status'))
    <div class="alert alert-success" role="status">{{ session('status') }}</div>
@endif

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

            <strong>{{ $empresa->official_name }}</strong>

            <small>{{ $empresa->commercial_name ? 'Nombre comercial: '.$empresa->commercial_name : 'Sin nombre comercial' }}</small>

        </div>


        <div class="coord-company-profile-data">

            <div>
                <span>Sector</span>
                <strong>{{ $empresa->sector ?? 'Sin sector' }}</strong>
            </div>

            <div>
                <span>Estado</span>
                <strong>{{ ['ACTIVE' => 'Activa', 'INACTIVE' => 'Inactiva'][$empresa->status] ?? $empresa->status }}</strong>
            </div>

            <div>
                <span>Ubicación</span>
                <strong>{{ collect([$empresa->city, $empresa->department])->filter()->implode(', ') ?: 'No registrada' }}</strong>
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
                <strong>{{ $empresa->rtn ?? 'No registrado' }}</strong>
            </div>

            <div>
                <span>Teléfono</span>
                <strong>{{ $empresa->phone ?? 'No registrado' }}</strong>
            </div>

            <div>
                <span>Correo</span>
                <strong>{{ $empresa->email ?? 'No registrado' }}</strong>
            </div>

            <div>
                <span>Dirección</span>
                <strong>{{ $empresa->address ?? 'No registrada' }}</strong>
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
            >
                <i class="bi bi-person-plus"></i>
                Agregar responsable
            </button>

        </div>


        <div id="responsiblesList">
            @forelse ($empresa->responsibles as $responsable)
                <article class="coord-responsible-row">
                    <div class="coord-responsible-avatar"><i class="bi bi-person"></i></div>
                    <div>
                        <span>RESPONSABLE</span>
                        <strong>{{ $responsable->full_name }}</strong>
                        <small>{{ $responsable->position }}</small>
                    </div>
                    <div>
                        <span>Contacto</span>
                        <strong>{{ $responsable->email ?? 'Sin correo' }}</strong>
                        <small>{{ $responsable->phone ?? 'Sin teléfono' }}</small>
                    </div>
                    <div>
                        <span>Practicantes asignados</span>
                        <strong>{{ $asignaciones->get($responsable->id, 0) }}</strong>
                    </div>
                    <span class="coord-company-status {{ $responsable->status === 'ACTIVE' ? 'active' : 'inactive' }}">
                        {{ ['ACTIVE' => 'Activo', 'INACTIVE' => 'Inactivo'][$responsable->status] ?? $responsable->status }}
                    </span>
                    <button type="button" class="coord-icon-action" data-bs-toggle="modal" data-bs-target="#responsibleModal"
                        data-record-id="{{ $responsable->id }}" data-action="{{ route('coordinacion.empresas.responsables.update', [$empresa, $responsable]) }}"
                        data-fields="{{ json_encode($responsable->only(['full_name', 'position', 'email', 'phone', 'status'])) }}"
                        title="Editar responsable">
                        <i class="bi bi-pencil"></i>
                    </button>
                </article>
            @empty
                <div class="teacher-load-empty">
                    <i class="bi bi-person-vcard"></i>
                    <strong>No hay responsables registrados</strong>
                    <span>Agrega el primer responsable de practicantes de esta empresa.</span>
                </div>
            @endforelse
        </div>

    </section>



    {{-- PRACTICANTES --}}

    <section class="content-card">

        <div class="card-header">

            <div>

                <h2>
                    Prácticas vinculadas
                </h2>

                <p>
                    Estudiantes con prácticas vinculadas
                    a esta empresa.
                </p>

            </div>

        </div>


        <div id="companyStudentsList">
            @forelse ($empresa->practices as $practica)
                <article class="coord-company-student">
                    <div class="coord-company-student-avatar">{{ Str::initials($practica->student->user->name, true) }}</div>
                    <div>
                        <span>ESTUDIANTE</span>
                        <strong><a href="{{ route('coordinacion.estudiante', $practica->student) }}">{{ $practica->student->user->name }}</a></strong>
                        <small>{{ $practica->student->account_number }}</small>
                    </div>
                    <div>
                        <span>Carrera</span>
                        <strong>{{ $practica->student->career }}</strong>
                    </div>
                    <div>
                        <span>Responsable</span>
                        <strong>{{ $practica->companyResponsible?->full_name ?? 'Sin responsable asignado' }}</strong>
                        <small>Docente de seguimiento: {{ $practica->teacher?->user?->name ?? 'Sin asignar' }}</small>
                    </div>
                    <div>
                        <span>Etapa</span>
                        <strong><a href="{{ route('coordinacion.practica', $practica) }}">{{ $practica->stage }}</a></strong>
                        <small>{{ $practica->academic_period }} {{ $practica->academic_year }}</small>
                    </div>
                    <div>
                        <span>Progreso</span>
                        <strong>{{ $practica->progress }}%</strong>
                        <div class="coord-company-progress"><span style="width: {{ $practica->progress }}%"></span></div>
                    </div>
                </article>
            @empty
                <div class="teacher-load-empty">
                    <i class="bi bi-people"></i>
                    <strong>No hay practicantes vinculados</strong>
                </div>
            @endforelse
        </div>

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

        <form class="modal-content coord-company-modal" method="POST"
            action="{{ route('coordinacion.empresas.responsables.store', $empresa) }}"
            data-record-modal
            data-create-action="{{ route('coordinacion.empresas.responsables.store', $empresa) }}"
            data-create-title="Agregar responsable" data-edit-title="Editar responsable"
            data-edit-id="{{ request()->string('editar') }}"
            data-validation-failed="{{ $errors->any() ? 1 : 0 }}"
            data-old-input="{{ json_encode(session()->getOldInput()) }}">
            @csrf
            <input type="hidden" name="_method" value="PUT" disabled>

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
                    id="responsibleId" name="_record"
                >


                <div class="row g-3">

                    <div class="col-12">

                        <label class="form-label">
                            Nombre completo *
                        </label>

                        <input
                            type="text"
                            id="responsibleName" name="full_name" required
                            class="form-control"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Cargo *
                        </label>

                        <input
                            type="text"
                            id="responsiblePosition" name="position" required
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
                            id="responsibleEmail" name="email"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12 col-md-6">

                        <label class="form-label">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            id="responsiblePhone" name="phone"
                            class="form-control"
                        >

                    </div>


                    <div class="col-12">

                        <label class="form-label">
                            Estado
                        </label>

                        <select
                            id="responsibleStatus" name="status"
                            class="form-select"
                        >

                            <option value="ACTIVE">
                                Activo
                            </option>

                            <option value="INACTIVE">
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
                    type="submit" class="btn btn-primary"
                >
                    <i class="bi bi-check-lg"></i>
                    Guardar responsable
                </button>

            </div>

        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/coordination-record-modal.js') }}"></script>
@endpush
