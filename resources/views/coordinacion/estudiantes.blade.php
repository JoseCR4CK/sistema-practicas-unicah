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

    <div class="filters-grid">


        <!-- BUSCADOR -->

        <div class="filter-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                placeholder="Buscar por nombre, cuenta o identidad..."
            >

        </div>



        <!-- CARRERA -->

        <div class="filter-field">

            <label>
                Carrera
            </label>

            <select>

                <option>
                    Todas
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



        <!-- ESTADO -->

        <div class="filter-field">

            <label>
                Estado
            </label>

            <select>

                <option>
                    Todos
                </option>

                <option>
                    En proceso
                </option>

                <option>
                    Pendiente
                </option>

                <option>
                    Finalizado
                </option>

            </select>

        </div>



        <!-- ETAPA -->

        <div class="filter-field">

            <label>
                Etapa
            </label>

            <select>

                <option>
                    Todas
                </option>

                <option>
                    Solicitud
                </option>

                <option>
                    Documentación
                </option>

                <option>
                    Práctica
                </option>

                <option>
                    Monografía
                </option>

                <option>
                    Defensa
                </option>

            </select>

        </div>


    </div>

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
                84 estudiantes encontrados
            </p>

        </div>


        <button
            type="button"
            class="secondary-button"
        >

            <i class="bi bi-download"></i>

            Exportar

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


                <!-- ESTUDIANTE 1 -->

                <tr>

                    <td>

                        <div class="student-cell">

                            <div class="student-avatar">
                                AM
                            </div>


                            <div>

                                <span class="student-name">
                                    Andrea Martínez
                                </span>

                                <span class="student-email">
                                    amartinez@unicah.edu
                                </span>

                            </div>

                        </div>

                    </td>


                    <td>
                        0801-2022-00458
                    </td>


                    <td>
                        Ing. Ciencias de la Computación
                    </td>


                    <td>
                        Grupo Tecnológico S.A.
                    </td>


                    <td>

                        <span class="badge success">

                            <i class="bi bi-circle-fill"></i>

                            En proceso

                        </span>

                    </td>


                    <td>
                        Solicitud
                    </td>


                    <td>

                        <div class="progress-cell">

                            <div class="progress-bar">

                                <div
                                    class="progress-fill"
                                    style="width: 15%;"
                                ></div>

                            </div>

                            <span>
                                15%
                            </span>

                        </div>

                    </td>


                    <td>

                        <div class="table-actions">

                            <a
                                href="{{ route('coordinacion.estudiantes.expediente', 1) }}"
                                class="action-button"
                                title="Ver expediente"
                            >
                                <i class="bi bi-eye"></i>
                            </a>


                            <button
                                type="button"
                                class="action-button"
                                title="Editar"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>

                        </div>

                    </td>

                </tr>



                <!-- ESTUDIANTE 2 -->

                <tr>

                    <td>

                        <div class="student-cell">

                            <div class="student-avatar">
                                CR
                            </div>


                            <div>

                                <span class="student-name">
                                    Carlos Rodríguez
                                </span>

                                <span class="student-email">
                                    crodriguez@unicah.edu
                                </span>

                            </div>

                        </div>

                    </td>


                    <td>
                        0801-2021-00231
                    </td>


                    <td>
                        Ingeniería Industrial
                    </td>


                    <td>
                        Corporación Industrial HN
                    </td>


                    <td>

                        <span class="badge success">

                            <i class="bi bi-circle-fill"></i>

                            En práctica

                        </span>

                    </td>


                    <td>
                        Seguimiento
                    </td>


                    <td>

                        <div class="progress-cell">

                            <div class="progress-bar">

                                <div
                                    class="progress-fill"
                                    style="width: 58%;"
                                ></div>

                            </div>

                            <span>
                                58%
                            </span>

                        </div>

                    </td>


                    <td>

                        <div class="table-actions">

                            <a
                                href="{{ route('coordinacion.estudiantes.expediente', 2) }}"
                                class="action-button"
                            >
                                <i class="bi bi-eye"></i>
                            </a>


                            <button
                                type="button"
                                class="action-button"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>

                        </div>

                    </td>

                </tr>



                <!-- ESTUDIANTE 3 -->

                <tr>

                    <td>

                        <div class="student-cell">

                            <div class="student-avatar">
                                ML
                            </div>


                            <div>

                                <span class="student-name">
                                    María López
                                </span>

                                <span class="student-email">
                                    mlopez@unicah.edu
                                </span>

                            </div>

                        </div>

                    </td>


                    <td>
                        0801-2022-00671
                    </td>


                    <td>
                        Administración de Empresas
                    </td>


                    <td>
                        Inversiones del Valle
                    </td>


                    <td>

                        <span class="badge info">

                            <i class="bi bi-circle-fill"></i>

                            Revisión

                        </span>

                    </td>


                    <td>
                        Monografía
                    </td>


                    <td>

                        <div class="progress-cell">

                            <div class="progress-bar">

                                <div
                                    class="progress-fill"
                                    style="width: 78%;"
                                ></div>

                            </div>

                            <span>
                                78%
                            </span>

                        </div>

                    </td>


                    <td>

                        <div class="table-actions">

                            <a
                                href="{{ route('coordinacion.estudiantes.expediente', 3) }}"
                                class="action-button"
                            >
                                <i class="bi bi-eye"></i>
                            </a>


                            <button
                                type="button"
                                class="action-button"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>

                        </div>

                    </td>

                </tr>



                <!-- ESTUDIANTE 4 -->

                <tr>

                    <td>

                        <div class="student-cell">

                            <div class="student-avatar">
                                JH
                            </div>


                            <div>

                                <span class="student-name">
                                    José Hernández
                                </span>

                                <span class="student-email">
                                    jhernandez@unicah.edu
                                </span>

                            </div>

                        </div>

                    </td>


                    <td>
                        0801-2021-00394
                    </td>


                    <td>
                        Ing. Ciencias de la Computación
                    </td>


                    <td>
                        Soluciones Digitales HN
                    </td>


                    <td>

                        <span class="badge warning">

                            <i class="bi bi-circle-fill"></i>

                            Por finalizar

                        </span>

                    </td>


                    <td>
                        Defensa
                    </td>


                    <td>

                        <div class="progress-cell">

                            <div class="progress-bar">

                                <div
                                    class="progress-fill"
                                    style="width: 92%;"
                                ></div>

                            </div>

                            <span>
                                92%
                            </span>

                        </div>

                    </td>


                    <td>

                        <div class="table-actions">

                            <a
                                href="{{ route('coordinacion.estudiantes.expediente', 4) }}"
                                class="action-button"
                            >
                                <i class="bi bi-eye"></i>
                            </a>


                            <button
                                type="button"
                                class="action-button"
                            >
                                <i class="bi bi-pencil"></i>
                            </button>

                        </div>

                    </td>

                </tr>



                <!-- ESTUDIANTE 5 -->

                <tr>

                    <td>

                        <div class="student-cell">

                            <div class="student-avatar">
                                SP
                            </div>


                            <div>

                                <span class="student-name">
                                    Sofía Pérez
                                </span>

                                <span class="student-email">
                                    sperez@unicah.edu
                                </span>

                            </div>

                        </div>

                    </td>


                    <td>
                        0801-2020-00118
                    </td>


                    <td>
                        Ingeniería Industrial
                    </td>


                    <td>
                        Manufacturas Centroamericanas
                    </td>


                    <td>

                        <span class="badge success">

                            <i class="bi bi-check-circle"></i>

                            Finalizado

                        </span>

                    </td>


                    <td>
                        Finalizado
                    </td>


                    <td>

                        <div class="progress-cell">

                            <div class="progress-bar">

                                <div
                                    class="progress-fill complete"
                                    style="width: 100%;"
                                ></div>

                            </div>

                            <span>
                                100%
                            </span>

                        </div>

                    </td>


                    <td>

                        <div class="table-actions">

                            <a
                                href="{{ route('coordinacion.estudiantes.expediente', 5) }}"
                                class="action-button"
                            >
                                <i class="bi bi-eye"></i>
                            </a>

                        </div>

                    </td>

                </tr>


            </tbody>


        </table>


    </div>



    <!-- PAGINACIÓN -->

    <div class="table-footer">


        <span>
            Mostrando 1 - 5 de 84 estudiantes
        </span>


        <div class="pagination">

            <button disabled>
                <i class="bi bi-chevron-left"></i>
            </button>

            <button class="active">
                1
            </button>

            <button>
                2
            </button>

            <button>
                3
            </button>

            <button>
                ...
            </button>

            <button>
                17
            </button>

            <button>
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

@endpush