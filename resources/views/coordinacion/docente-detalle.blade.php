@extends('layouts.app')

@section('title', 'Detalle del docente de seguimiento | UNICAH')

@section('content')

<div class="page-header">

    <div>

        <span class="student-home-eyebrow">
            CONTROL DE DOCENTES
        </span>

        <h1>
            {{ $docente->user->name }}
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
        {{ Str::initials($docente->user->name, true) }}
    </div>

    <div class="coord-teacher-detail-info">

        <span>DOCENTE DE SEGUIMIENTO</span>

        <strong>
            {{ $docente->user->name }}
        </strong>

        <small>
            {{ $docente->faculty ?? 'Sin facultad registrada' }}
        </small>
        <small>{{ $docente->employee_code ?? 'Sin código registrado' }} · {{ $docente->user->email }}</small>
        @if ($docente->phone)
            <small>{{ $docente->phone }}</small>
        @endif

    </div>


    <div class="coord-teacher-detail-stats">

        <div>
            <span>Practicantes</span>
            <strong>{{ $practicantes }}</strong>
        </div>

        <div>
            <span>Prácticas activas</span>
            <strong>{{ $practicasActivas }}</strong>
        </div>

        <div>
            <span>Estado</span>

            <strong class="{{ $docente->user->isActive() ? 'text-success' : 'text-warning' }}">
                {{ ['ACTIVE' => 'Activo', 'INACTIVE' => 'Inactivo'][$docente->user->status] ?? $docente->user->status }}
            </strong>
        </div>

    </div>

</section>


<section class="content-card mb-4">
    <div class="card-header">
        <div>
            <h2>Practicantes asignados</h2>
            <p>Prácticas asociadas a este docente de seguimiento.</p>
        </div>
    </div>

    <div class="table-container table-responsive">
        <table class="data-table table align-middle">
            <thead>
                <tr><th>Estudiante</th><th>Empresa</th><th>Período</th><th>Etapa</th><th>Estado</th></tr>
            </thead>
            <tbody>
                @forelse ($docente->practices as $practica)
                    <tr>
                        <td><a href="{{ route('coordinacion.estudiante', $practica->student) }}">{{ $practica->student->user->name }}</a></td>
                        <td>{{ $practica->company?->official_name ?? 'Sin empresa asignada' }}</td>
                        <td>{{ $practica->academic_period }} {{ $practica->academic_year }}</td>
                        <td><a href="{{ route('coordinacion.practica', $practica) }}">{{ $practica->stage }}</a></td>
                        <td>{{ $practica->statusLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">Este docente de seguimiento todavía no tiene practicantes asignados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="content-card mb-4">
    <div class="card-header"><div><h2>Carga académica</h2><p>Historial persistente de asignaturas y horarios del docente.</p></div><a href="{{ route('coordinacion.carga-academica', $docente) }}" class="btn btn-light">Consultar y corregir carga</a></div>
    <div class="table-container table-responsive"><table class="data-table">
        <thead><tr><th>Asignatura</th><th>Período</th><th>Horario</th><th>Vigencia</th><th>Estado</th></tr></thead>
        <tbody>@forelse ($docente->academicLoads as $load)<tr><td>{{ $load->subject }}</td><td>{{ $load->academic_period }} {{ $load->academic_year }}</td><td>{{ substr($load->starts_at, 0, 5) }} – {{ substr($load->ends_at, 0, 5) }}</td><td>{{ $load->valid_from->format('d/m/Y') }} – {{ $load->valid_until->format('d/m/Y') }}</td><td>{{ $load->status === 'ACTIVE' ? 'Activa' : 'Inactiva' }}</td></tr>@empty<tr><td colspan="5">Sin clases registradas.</td></tr>@endforelse</tbody>
    </table></div>
</section>
@endsection
