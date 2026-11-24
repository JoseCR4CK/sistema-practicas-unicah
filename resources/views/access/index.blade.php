@extends('layouts.app')
@section('title', 'Solicitudes de acceso | Coordinación Académica')
@section('content')
<div class="page-header"><div><h1>Solicitudes de acceso</h1><p>Revisa el ingreso de estudiantes y docentes al sistema.</p></div></div>
<div class="row g-3 mb-4">@foreach(\App\Models\AccessRequest::STATES as $state => $label)<div class="col-6 col-lg"><div class="content-card p-3"><span>{{ $label }}</span><strong class="d-block fs-3">{{ $counts[$state] ?? 0 }}</strong></div></div>@endforeach</div>
<section class="content-card p-3">
<nav class="d-flex gap-2 flex-wrap mb-3" aria-label="Tipo de solicitudes"><a class="btn btn-outline-primary" href="{{ route('coordinacion.accesos', ['type' => 'STUDENT']) }}">Estudiantes</a><a class="btn btn-outline-primary" href="{{ route('coordinacion.accesos', ['type' => 'TEACHER']) }}">Docentes</a></nav>
<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><label for="search" class="form-label">Nombre, DNI o cuenta</label><input class="form-control" name="search" id="search" value="{{ $filters['search'] ?? '' }}"></div>
    <div class="col-md-2"><label for="type" class="form-label">Tipo</label><select class="form-select" id="type" name="type"><option value="">Todos</option><option value="STUDENT" @selected(($filters['type'] ?? '') === 'STUDENT')>Estudiantes</option><option value="TEACHER" @selected(($filters['type'] ?? '') === 'TEACHER')>Docentes</option></select></div>
    <div class="col-md-3"><label for="faculty" class="form-label">Facultad / Área</label><select class="form-select" name="faculty" id="faculty"><option value="">Todas</option>@foreach($faculties as $faculty)<option value="{{ $faculty->id }}" @selected((int)($filters['faculty'] ?? 0) === $faculty->id)>{{ $faculty->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><label for="status" class="form-label">Estado</label><select class="form-select" name="status" id="status"><option value="">Todos</option>@foreach(\App\Models\AccessRequest::STATES as $key => $label)<option value="{{ $key }}" @selected(($filters['status'] ?? '') === $key)>{{ $label }}</option>@endforeach</select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Filtrar</button></div>
</form>
<div class="table-responsive"><table class="table align-middle"><thead><tr><th>Solicitante</th><th>Tipo</th><th>Facultad</th><th>Asignaturas</th><th>Estado</th><th>Registro</th><th>Detalle</th></tr></thead><tbody>
@forelse($applications as $application)<tr><td>{{ $application->full_name }}<small class="d-block">{{ $application->dni }} {{ $application->account_number }}</small></td><td>{{ $application->isStudent() ? 'Estudiante' : 'Docente' }}</td><td>{{ $application->career->name }}</td><td>{{ $application->subjects_count }} @if($application->subjects_count > 2)<span class="badge bg-warning text-dark">Requiere revisión de CA</span>@endif</td><td>{{ $application->statusLabel() }}</td><td>{{ $application->created_at->format('d/m/Y') }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('coordinacion.acceso', $application) }}">Abrir</a></td></tr>@empty<tr><td colspan="7">No hay solicitudes que coincidan con los filtros.</td></tr>@endforelse
</tbody></table></div>
{{ $applications->links('pagination::bootstrap-5') }}
</section>
@endsection
