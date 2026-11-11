@extends('layouts.app')
@section('title', 'Documentos | Coordinación Académica')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">CONTROL DOCUMENTAL</span><h1>Documentos</h1><p>Revisa los documentos registrados durante los procesos de práctica profesional.</p></div></div>
<section class="row g-3 mb-4">
    @foreach (['Total' => $documentos->count(), 'Pendientes' => $documentos->where('currentVersion.status', 'Pendiente de revisión')->count(), 'Aprobados' => $documentos->where('currentVersion.status', 'Aprobado')->count(), 'Requieren corrección' => $documentos->where('currentVersion.status', 'Requiere corrección')->count()] as $etiqueta => $cantidad)
        <div class="col-12 col-md-3"><div class="teacher-load-stat"><div class="teacher-load-stat-icon"><i class="bi bi-files"></i></div><div><span>{{ $etiqueta }}</span><strong>{{ $cantidad }}</strong></div></div></div>
    @endforeach
</section>
<section class="content-card">
    <div class="coord-doc-toolbar">
        <div class="coord-company-search"><i class="bi bi-search"></i><input type="search" id="flowSearch" placeholder="Buscar estudiante, cuenta o documento..."></div>
        <select id="flowCareer" class="form-select"><option value="">Todos los tipos</option>@foreach (\App\Models\AcademicDocument::TYPES as $codigo => $tipo)<option value="{{ $codigo }}">{{ $tipo }}</option>@endforeach</select>
        <select id="flowStatus" class="form-select"><option value="">Todos los estados</option>@foreach (['Pendiente de revisión', 'Aprobado', 'Requiere corrección'] as $estado)<option>{{ $estado }}</option>@endforeach</select>
    </div>
    @include('academic.document-list', ['studentPortal' => false])
</section>
@endsection
@push('scripts')<script src="{{ asset('js/academic-filters.js') }}"></script>@endpush
