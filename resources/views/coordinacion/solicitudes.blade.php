@extends('layouts.app')
@section('title', 'Solicitudes | Coordinación Académica')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">GESTIÓN DE SOLICITUDES</span><h1>Solicitudes</h1><p>Revisa las solicitudes de estudiantes que desean iniciar su proceso de práctica profesional.</p></div></div>
<section class="row g-3 mb-4">
    @foreach (['Total' => $solicitudes->count(), 'Pendientes' => $solicitudes->where('status', 'Pendiente')->count(), 'Aprobadas' => $solicitudes->where('status', 'Aprobada')->count(), 'Rechazadas' => $solicitudes->where('status', 'Rechazada')->count()] as $etiqueta => $cantidad)
        <div class="col-12 col-md-3"><div class="teacher-load-stat"><div class="teacher-load-stat-icon"><i class="bi bi-files"></i></div><div><span>{{ $etiqueta }}</span><strong>{{ $cantidad }}</strong></div></div></div>
    @endforeach
</section>
<section class="content-card">
    <div class="coord-request-toolbar">
        <div class="coord-company-search"><i class="bi bi-search"></i><input type="search" id="flowSearch" placeholder="Buscar estudiante, cuenta o empresa..."></div>
        <select id="flowStatus" class="form-select"><option value="">Todos los estados</option>@foreach (['Pendiente', 'Aprobada', 'Rechazada'] as $estado)<option>{{ $estado }}</option>@endforeach</select>
        <select id="flowCareer" class="form-select"><option value="">Todas las carreras</option>@foreach ($solicitudes->pluck('student.career')->unique()->sort() as $carrera)<option>{{ $carrera }}</option>@endforeach</select>
    </div>
    <div>
        @forelse ($solicitudes as $solicitud)
            <article class="coord-request-row" data-flow-row data-status="{{ $solicitud->status }}" data-career="{{ $solicitud->student->career }}">
                <div class="coord-request-avatar">{{ Str::initials($solicitud->student->user->name, true) }}</div>
                <div class="coord-request-student"><span>ESTUDIANTE</span><strong>{{ $solicitud->student->user->name }}</strong><small>{{ $solicitud->student->account_number }}</small></div>
                <div><span class="coord-request-label">Carrera</span><strong class="coord-request-value">{{ $solicitud->student->career }}</strong></div>
                <div><span class="coord-request-label">Empresa propuesta</span><strong class="coord-request-value">{{ $solicitud->practice?->company?->official_name ?? 'Sin empresa asignada' }}</strong></div>
                <div><span class="coord-request-label">Fecha solicitud</span><strong class="coord-request-value">{{ $solicitud->submitted_at->format('d/m/Y') }}</strong></div>
                <span class="coord-request-status {{ ['Pendiente' => 'pending', 'Aprobada' => 'approved', 'Rechazada' => 'rejected'][$solicitud->status] ?? '' }}">{{ $solicitud->status }}</span>
                <a href="{{ route('coordinacion.solicitud', $solicitud) }}" class="coord-teacher-view"><i class="bi bi-eye"></i> Revisar</a>
            </article>
        @empty
            <div class="teacher-load-empty">No hay solicitudes registradas.</div>
        @endforelse
    </div>
</section>
@endsection
@push('scripts')
<script src="{{ asset('js/academic-filters.js') }}"></script>
@endpush
