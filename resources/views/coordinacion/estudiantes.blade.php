@extends('layouts.app')


@section('title', 'Estudiantes | Coordinación Académica')


@section('content')


<div class="page-header">

    <div>

        <h1>
            Estudiantes
        </h1>

        <p>
            Administración y seguimiento de estudiantes
            en proceso de práctica profesional.
        </p>

    </div>


    <button
        type="button"
        class="primary-button"
        id="openStudentModal"
    >
        <i class="bi bi-person-plus"></i>

        Nuevo estudiante
    </button>

</div>



<!-- =====================================
     FILTROS
====================================== -->

<section class="content-card filters-card">

    <form class="filters-grid" id="studentFilters" method="GET" action="{{ route('coordinacion.estudiantes') }}">
        <div class="filter-search">
            <i class="bi bi-search"></i>
            <input type="search" name="buscar" value="{{ request()->string('buscar') }}" placeholder="Buscar por nombre, cuenta o identidad...">
            <button type="submit" class="visually-hidden-focusable">Buscar estudiantes</button>
        </div>

        <div class="filter-field">
            <label for="studentCareer">Carrera</label>
            <select id="studentCareer" name="carrera" onchange="this.form.submit()">
                <option value="">Todas</option>
                @foreach ($carreras as $carrera)
                    <option @selected(request('carrera') === $carrera)>{{ $carrera }}</option>
                @endforeach
            </select>
        </div>

        <div class="filter-field">
            <label for="studentStatus">Estado</label>
            <select id="studentStatus" name="estado" onchange="this.form.submit()">
                <option value="">Todos</option>
                @foreach ($estados as $estado)
                    <option value="{{ $estado->status }}" @selected(request('estado') === $estado->status)>{{ $estado->statusLabel() }}</option>
                @endforeach
                <option value="sin-practica" @selected(request('estado') === 'sin-practica')>Sin práctica</option>
            </select>
        </div>

        <div class="filter-field">
            <label for="studentStage">Etapa</label>
            <select id="studentStage" name="etapa" onchange="this.form.submit()">
                <option value="">Todas</option>
                @foreach ($etapas as $etapa)
                    <option @selected(request('etapa') === $etapa)>{{ $etapa }}</option>
                @endforeach
            </select>
        </div>
    </form>

</section>



<!-- =====================================
     TABLA
====================================== -->

<section class="content-card students-card">


    <div class="card-header">

        <div>

            <h2>
                Estudiantes registrados
            </h2>

            <p>
                {{ $estudiantes->total() }} {{ $estudiantes->total() === 1 ? 'estudiante encontrado' : 'estudiantes encontrados' }}
            </p>

        </div>

        <button
            type="button"
            class="btn btn-primary coord-report-button"
            data-bs-toggle="modal"
            data-bs-target="#studentReportModal"
        >
            <i class="bi bi-file-earmark-bar-graph"></i>
            Generar informe
        </button>

    </div>



    <div class="table-container">


        <table class="data-table students-table">


            <thead>

                <tr>

                    <th>
                        Estudiante
                    </th>

                    <th>
                        Cuenta
                    </th>

                    <th>
                        Carrera
                    </th>

                    <th>
                        Empresa
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Etapa actual
                    </th>

                    <th>
                        Progreso
                    </th>

                    <th>
                        Acciones
                    </th>

                </tr>

            </thead>



            <tbody>
                @forelse ($estudiantes as $estudiante)
                    <tr>
                        <td>
                            <div class="student-cell">
                                <div class="student-avatar">{{ $estudiante['iniciales'] }}</div>
                                <div>
                                    <span class="student-name">{{ $estudiante['nombre'] }}</span>
                                    <span class="student-email">{{ $estudiante['correo'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $estudiante['cuenta'] }}</td>
                        <td>{{ $estudiante['carrera'] }}</td>
                        <td>{{ $estudiante['empresa'] }}</td>
                        <td>
                            <span class="badge {{ $estudiante['estado_codigo'] === 'ACTIVE' ? 'success' : 'warning' }}">
                                <i class="bi bi-circle-fill"></i>
                                {{ $estudiante['estado'] }}
                            </span>
                        </td>
                        <td>{{ $estudiante['etapa'] }}</td>
                        <td>
                            @if ($estudiante['progreso'] === null)
                                —
                            @else
                                <div class="progress-cell">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $estudiante['progreso'] }}%;"></div>
                                    </div>
                                    <span>{{ $estudiante['progreso'] }}%</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('coordinacion.estudiante', $estudiante['id']) }}" class="action-button" title="Ver expediente">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="action-button" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">No se encontraron estudiantes.</td></tr>
                @endforelse
            </tbody>


        </table>


    </div>



    <!-- PAGINACIÓN -->

    <div class="table-footer">
        <span>Mostrando {{ $estudiantes->firstItem() ?? 0 }} - {{ $estudiantes->lastItem() ?? 0 }} de {{ $estudiantes->total() }} estudiantes</span>
        <div class="pagination">
            <button form="studentFilters" name="page" value="{{ $estudiantes->currentPage() - 1 }}" @disabled($estudiantes->onFirstPage()) aria-label="Página anterior">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button type="button" class="active" aria-current="page">{{ $estudiantes->currentPage() }}</button>
            <button form="studentFilters" name="page" value="{{ $estudiantes->currentPage() + 1 }}" @disabled(!$estudiantes->hasMorePages()) aria-label="Página siguiente">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>


</section>



<!-- =====================================
     MODAL NUEVO ESTUDIANTE
====================================== -->

<div
    class="modal-overlay"
    id="studentModal"
>


    <div class="modal-card">


        <div class="modal-header">

            <div>

                <h2>
                    Nuevo estudiante
                </h2>

                <p>
                    Registra la información inicial
                    del estudiante.
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                id="closeStudentModal"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>



        <div class="modal-body">


            <div class="form-grid">


                <div class="form-field">

                    <label>
                        Nombres
                    </label>

                    <input
                        type="text"
                        placeholder="Nombres del estudiante"
                    >

                </div>



                <div class="form-field">

                    <label>
                        Apellidos
                    </label>

                    <input
                        type="text"
                        placeholder="Apellidos del estudiante"
                    >

                </div>



                <div class="form-field">

                    <label>
                        Número de cuenta
                    </label>

                    <input
                        type="text"
                        placeholder="0000-0000-00000"
                    >

                </div>



                <div class="form-field">

                    <label>
                        Identidad
                    </label>

                    <input
                        type="text"
                        placeholder="0000-0000-00000"
                    >

                </div>



                <div class="form-field full">

                    <label>
                        Correo institucional
                    </label>

                    <input
                        type="email"
                        placeholder="estudiante@unicah.edu"
                    >

                </div>



                <div class="form-field full">

                    <label>
                        Carrera
                    </label>

                    <select>

                        <option>
                            Seleccione una carrera
                        </option>

                        <option>
                            Ingeniería en Ciencias de la Computación
                        </option>

                        <option>
                            Ingeniería Industrial
                        </option>

                        <option>
                            Administración de Empresas
                        </option>

                    </select>

                </div>


            </div>


        </div>



        <div class="modal-footer">

            <button
                type="button"
                class="secondary-button"
                id="cancelStudentModal"
            >
                Cancelar
            </button>


            <button
                type="button"
                class="primary-button"
            >

                <i class="bi bi-person-plus"></i>

                Registrar estudiante

            </button>

        </div>


    </div>


</div>

<div
    class="modal fade"
    id="studentReportModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content coord-report-modal">

            <div class="modal-header">

                <div>
                    <span class="student-home-eyebrow">
                        REPORTES
                    </span>

                    <h5 class="modal-title">
                        Generar informe de estudiantes
                    </h5>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Tipo de informe
                    </label>

                    <select
                        id="reportType"
                        class="form-select"
                    >
                        <option value="todos">
                            Todos los estudiantes
                        </option>

                        <option value="activas">
                            Prácticas activas
                        </option>

                        <option value="etapa">
                            Por etapa
                        </option>

                        <option value="carrera">
                            Por carrera
                        </option>

                        <option value="empresa">
                            Por empresa
                        </option>
                    </select>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Período académico
                    </label>

                    <select
                        id="reportPeriod"
                        class="form-select"
                    >
                        <option value="">Todos</option>
                        @foreach ($periodos as $periodo)
                            <option>{{ $periodo->academic_period }} {{ $periodo->academic_year }}</option>
                        @endforeach
                    </select>

                </div>


                <div
                    class="mb-3 d-none"
                    id="reportStageContainer"
                >

                    <label class="form-label">
                        Etapa
                    </label>

                    <select
                        id="reportStage"
                        class="form-select"
                    >
                        <option value="">Todas</option>
                        @foreach ($etapas as $etapa)
                            <option>{{ $etapa }}</option>
                        @endforeach
                    </select>

                </div>


                <div
                    class="mb-3 d-none"
                    id="reportCareerContainer"
                >

                    <label class="form-label">
                        Carrera
                    </label>

                    <select
                        id="reportCareer"
                        class="form-select"
                    >
                        <option value="">Todas</option>
                        @foreach ($carreras as $carrera)
                            <option>{{ $carrera }}</option>
                        @endforeach
                    </select>

                </div>


                <div
                    class="mb-3 d-none"
                    id="reportCompanyContainer"
                >

                    <label class="form-label">
                        Empresa
                    </label>

                    <select
                        id="reportCompany"
                        class="form-select"
                    >
                        <option value="">Todas</option>
                        @foreach ($empresas as $id => $empresa)
                            <option value="{{ $id }}">{{ $empresa }}</option>
                        @endforeach
                    </select>

                </div>


                <div class="coord-report-info">

                    <i class="bi bi-info-circle"></i>

                    <span>
                        Se generará una vista previa lista para impresión
                        o guardado en formato PDF.
                    </span>

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
                    onclick="generateStudentReport()"
                >
                    <i class="bi bi-file-earmark-text"></i>
                    Generar informe
                </button>

            </div>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>

    const studentModal =
        document.getElementById('studentModal');

    const openStudentModal =
        document.getElementById('openStudentModal');

    const closeStudentModal =
        document.getElementById('closeStudentModal');

    const cancelStudentModal =
        document.getElementById('cancelStudentModal');


    function openModal() {

        studentModal.classList.add('show');

        document.body.style.overflow = 'hidden';

    }


    function closeModal() {

        studentModal.classList.remove('show');

        document.body.style.overflow = '';

    }


    openStudentModal.addEventListener(
        'click',
        openModal
    );


    closeStudentModal.addEventListener(
        'click',
        closeModal
    );


    cancelStudentModal.addEventListener(
        'click',
        closeModal
    );


    studentModal.addEventListener(
        'click',
        function(event) {

            if (event.target === studentModal) {

                closeModal();

            }

        }
    );

</script>

<script>

const reportType =
    document.getElementById('reportType');

const reportStageContainer =
    document.getElementById('reportStageContainer');

const reportCareerContainer =
    document.getElementById('reportCareerContainer');

const reportCompanyContainer =
    document.getElementById('reportCompanyContainer');


reportType.addEventListener('change', function () {

    reportStageContainer.classList.add('d-none');
    reportCareerContainer.classList.add('d-none');
    reportCompanyContainer.classList.add('d-none');


    if (this.value === 'etapa') {
        reportStageContainer.classList.remove('d-none');
    }


    if (this.value === 'carrera') {
        reportCareerContainer.classList.remove('d-none');
    }


    if (this.value === 'empresa') {
        reportCompanyContainer.classList.remove('d-none');
    }

});


function generateStudentReport() {

    const type =
        document.getElementById('reportType').value;

    const period =
        document.getElementById('reportPeriod').value;

    const stage =
        document.getElementById('reportStage').value;

    const career =
        document.getElementById('reportCareer').value;

    const company =
        document.getElementById('reportCompany').value;


    const params =
        new URLSearchParams();


    params.set('tipo', type);

    params.set('periodo', period);


    if (stage) {
        params.set('etapa', stage);
    }


    if (career) {
        params.set('carrera', career);
    }


    if (company) {
        params.set('empresa', company);
    }


    window.location.href =
        "{{ route('coordinacion.estudiantes.informe') }}"
        + '?'
        + params.toString();

}

</script>

@endpush
