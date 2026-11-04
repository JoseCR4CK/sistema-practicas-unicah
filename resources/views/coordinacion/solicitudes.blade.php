@extends('layouts.app')

@section('title', 'Solicitudes | Coordinación Académica')

@section('content')


<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            GESTIÓN DE SOLICITUDES
        </span>

        <h1>
            Solicitudes
        </h1>

        <p>
            Revisa las solicitudes de estudiantes
            que desean iniciar su proceso de práctica profesional.
        </p>

    </div>

</div>



{{-- RESUMEN --}}

<section class="row g-3 mb-4">

    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-files"></i>
            </div>

            <div>
                <span>Total</span>
                <strong id="requestStatTotal">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-clock"></i>
            </div>

            <div>
                <span>Pendientes</span>
                <strong id="requestStatPending">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>

            <div>
                <span>Aprobadas</span>
                <strong id="requestStatApproved">0</strong>
            </div>

        </div>

    </div>


    <div class="col-12 col-md-3">

        <div class="teacher-load-stat">

            <div class="teacher-load-stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>

            <div>
                <span>Rechazadas</span>
                <strong id="requestStatRejected">0</strong>
            </div>

        </div>

    </div>

</section>



{{-- LISTADO --}}

<section class="content-card">

    <div class="coord-request-toolbar">

        <div class="coord-company-search">

            <i class="bi bi-search"></i>

            <input
                type="text"
                id="requestSearch"
                placeholder="Buscar estudiante, cuenta o empresa..."
                oninput="renderRequests()"
            >

        </div>


        <select
            id="requestStatus"
            class="form-select"
            onchange="renderRequests()"
        >

            <option value="">
                Todos los estados
            </option>

            <option value="Pendiente">
                Pendientes
            </option>

            <option value="Aprobada">
                Aprobadas
            </option>

            <option value="Rechazada">
                Rechazadas
            </option>

        </select>


        <select
            id="requestCareer"
            class="form-select"
            onchange="renderRequests()"
        >

            <option value="">
                Todas las carreras
            </option>

            <option value="Ingeniería en Ciencias de la Computación">
                Ing. Ciencias de la Computación
            </option>

            <option value="Ingeniería Industrial">
                Ingeniería Industrial
            </option>

            <option value="Administración de Empresas">
                Administración de Empresas
            </option>

        </select>

    </div>


    <div id="requestsList"></div>

</section>

@endsection



@push('scripts')

<script src="{{ asset('js/requests-store.js') }}"></script>

<script>

function formatRequestDate(date) {

    if (!date) {
        return '—';
    }


    return new Date(
        date + 'T12:00:00'
    ).toLocaleDateString(
        'es-HN',
        {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        }
    );

}


function renderRequestStats(requests) {

    document.getElementById(
        'requestStatTotal'
    ).textContent =
        requests.length;


    document.getElementById(
        'requestStatPending'
    ).textContent =
        requests.filter(item =>
            item.estado === 'Pendiente'
        ).length;


    document.getElementById(
        'requestStatApproved'
    ).textContent =
        requests.filter(item =>
            item.estado === 'Aprobada'
        ).length;


    document.getElementById(
        'requestStatRejected'
    ).textContent =
        requests.filter(item =>
            item.estado === 'Rechazada'
        ).length;

}


function renderRequests() {

    const requests =
        RequestStore.getAll();


    renderRequestStats(requests);


    const search =
        document
            .getElementById('requestSearch')
            .value
            .trim()
            .toLowerCase();


    const status =
        document.getElementById(
            'requestStatus'
        ).value;


    const career =
        document.getElementById(
            'requestCareer'
        ).value;


    const filtered =
        requests.filter(request => {

            const searchable =
                `
                    ${request.estudiante}
                    ${request.cuenta}
                    ${request.empresa}
                `
                .toLowerCase();


            const matchesSearch =
                searchable.includes(search);


            const matchesStatus =
                !status ||
                request.estado === status;


            const matchesCareer =
                !career ||
                request.carrera === career;


            return (
                matchesSearch &&
                matchesStatus &&
                matchesCareer
            );

        });


    const container =
        document.getElementById(
            'requestsList'
        );


    if (!filtered.length) {

        container.innerHTML = `

            <div class="teacher-load-empty">

                <i class="bi bi-file-earmark-text"></i>

                <strong>
                    No se encontraron solicitudes
                </strong>

                <span>
                    Cambia los criterios de búsqueda o filtrado.
                </span>

            </div>

        `;

        return;

    }


    container.innerHTML =
        filtered.map(request => {

            const initials =
                request.estudiante
                    .split(' ')
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(word =>
                        word.charAt(0)
                    )
                    .join('')
                    .toUpperCase();


            const statusClass =
                request.estado === 'Pendiente'
                    ? 'pending'
                    : request.estado === 'Aprobada'
                        ? 'approved'
                        : 'rejected';


            return `

                <article class="coord-request-row">

                    <div class="coord-request-avatar">
                        ${initials}
                    </div>


                    <div class="coord-request-student">

                        <span>ESTUDIANTE</span>

                        <strong>
                            ${request.estudiante}
                        </strong>

                        <small>
                            ${request.cuenta}
                        </small>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Carrera
                        </span>

                        <strong class="coord-request-value">
                            ${request.carrera}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Empresa propuesta
                        </span>

                        <strong class="coord-request-value">
                            ${request.empresa}
                        </strong>

                    </div>


                    <div>

                        <span class="coord-request-label">
                            Fecha solicitud
                        </span>

                        <strong class="coord-request-value">
                            ${formatRequestDate(
                                request.fecha_solicitud
                            )}
                        </strong>

                    </div>


                    <span class="coord-request-status ${statusClass}">
                        ${request.estado}
                    </span>


                    <a
                        href="/coordinacion/solicitudes/${request.id}"
                        class="coord-teacher-view"
                    >
                        <i class="bi bi-eye"></i>
                        Revisar
                    </a>

                </article>

            `;

        }).join('');

}


renderRequests();

</script>

@endpush