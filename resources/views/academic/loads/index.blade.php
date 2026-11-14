@extends(auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : 'layouts.app')
@section('title', 'Carga académica | UNICAH')
@section('content')
@php
    $manager = in_array(auth()->user()->role->name, ['COORDINACION', 'ROOT'], true);
    $portal = $manager ? 'coordinacion' : 'docente';
    $active = $loads->where('status', 'ACTIVE');
    $days = $active->flatMap(fn ($item) => $item->weekdays)->unique()->count();
@endphp
<div class="page-header">
    <div><span class="student-home-eyebrow">GESTIÓN ACADÉMICA</span><h1>Carga académica</h1><p>{{ $manager ? 'Consulta y gestiona las asignaturas y horarios registrados por el docente durante cada período académico.' : 'Registra las asignaturas y horarios que impartes durante cada período académico.' }}</p></div>
    <button class="btn btn-primary teacher-load-add" data-bs-toggle="modal" data-bs-target="#academicLoadModal"><i class="bi bi-plus-lg"></i> Agregar clase</button>
</div>
@include('academic.closing-feedback')
@if ($careers->flatMap->subjects->every(fn ($subject) => $subject->confirmedWeeklyHours() === null))
    <div class="alert alert-info">El catálogo aún no contiene asignaturas con datos académicos confirmados. El historial se conserva; los nuevos registros requieren una asignatura disponible en el catálogo.</div>
@endif
<section class="teacher-load-profile"><div class="teacher-load-profile-icon"><i class="bi bi-person-badge"></i></div><div><span>DOCENTE DE SEGUIMIENTO</span><strong>{{ $teacher->user->name }}</strong><small>Historial de asignaturas y horas reales de clase.</small></div></section>
<section class="content-card mb-4"><form method="GET" class="teacher-load-period">
    <div><label for="filterYear">Año académico</label><input id="filterYear" name="year" type="number" min="1900" max="2200" value="{{ $filters['year'] ?? '' }}" class="form-control"></div>
    <div><label for="filterPeriod">Período</label><select id="filterPeriod" name="period" class="form-select"><option value="">Todos los períodos</option>@foreach (\App\Models\AcademicLoad::PERIODS as $key => $label)<option value="{{ $key }}" @selected(($filters['period'] ?? '') === $key)>{{ $label }}</option>@endforeach</select></div>
    <button class="btn btn-light">Filtrar</button><a href="{{ $manager ? route('coordinacion.carga-academica', $teacher) : route('docente.carga-academica') }}">Ver historial completo</a>
</form></section>
<section class="row g-3 mb-4">
    <div class="col-md-4"><div class="teacher-load-stat"><div class="teacher-load-stat-icon"><i class="bi bi-book"></i></div><div><span>Clases registradas</span><strong>{{ $loads->count() }}</strong></div></div></div>
    <div class="col-md-4"><div class="teacher-load-stat"><div class="teacher-load-stat-icon"><i class="bi bi-calendar3"></i></div><div><span>Días con clases activas</span><strong>{{ $days }}</strong></div></div></div>
    <div class="col-md-4"><div class="teacher-load-stat"><div class="teacher-load-stat-icon"><i class="bi bi-clock"></i></div><div><span>Horas semanales registradas (clases activas)</span><strong>{{ $active->whereNotNull('weekly_class_hours')->isEmpty() ? 'Sin registrar' : $active->sum('weekly_class_hours') }}</strong></div></div></div>
</section>
<section class="content-card mb-4"><div class="card-header"><div><h2>Horario semanal</h2><p>Clases activas de los períodos seleccionados. Consulta su vigencia en el listado.</p></div></div><div class="teacher-week-grid">
    @foreach (\App\Models\AcademicLoad::DAYS as $number => $name)
        <div class="teacher-week-day"><div class="teacher-week-title">{{ $name }}</div><div class="teacher-week-content">
            @forelse ($active->filter(fn ($item) => in_array($number, $item->weekdays, true)) as $item)
                <div class="teacher-week-class"><strong>{{ $item->subject }}</strong><span>{{ substr($item->starts_at, 0, 5) }} – {{ substr($item->ends_at, 0, 5) }}</span><small>{{ $item->academic_period }} {{ $item->academic_year }}</small></div>
            @empty<div class="teacher-week-empty">Sin clases registradas</div>@endforelse
        </div></div>
    @endforeach
</div></section>
<section class="content-card"><div class="card-header"><div><h2>Clases registradas</h2><p>Los períodos conservan su historial. Las correcciones requieren una asignatura confirmada del catálogo.</p></div></div>
    @forelse ($loads as $item)
        <article class="teacher-load-row"><div class="teacher-load-row-icon"><i class="bi bi-book"></i></div><div class="teacher-load-row-main"><strong>{{ $item->subject }}</strong><span>{{ $item->career }} {{ $item->section }}</span><small>{{ $item->academic_period }} {{ $item->academic_year }} · {{ $item->status === 'ACTIVE' ? 'Activa' : 'Inactiva' }}</small><small>Créditos: {{ $item->credits ?? 'Sin registrar' }} · Horas semanales: {{ $item->weekly_class_hours ?? 'Sin registrar' }}</small></div>
        <div class="teacher-load-row-days">{{ collect($item->weekdays)->map(fn ($day) => \App\Models\AcademicLoad::DAYS[$day])->join(', ') }}<small>{{ $item->academic_subject_id ? 'Asociada al catálogo' : 'Pendiente de asociación al catálogo' }}</small>@if ($item->academic_subject_id && $item->weekly_class_hours !== null)<small>{{ $item->scheduledWeeklyMinutes() }} de {{ (int) ($item->weekly_class_hours * \App\Services\AcademicClassTime::CLASS_MINUTES) }} minutos semanales{{ $item->scheduledWeeklyMinutes() < $item->weekly_class_hours * \App\Services\AcademicClassTime::CLASS_MINUTES ? ' · Horario parcial' : '' }}</small>@endif</div>
        <div class="teacher-load-row-time"><strong>{{ substr($item->starts_at, 0, 5) }} – {{ substr($item->ends_at, 0, 5) }}</strong><small>{{ $item->valid_from->format('d/m/Y') }} – {{ $item->valid_until->format('d/m/Y') }}</small></div>
        <div class="teacher-load-row-actions"><button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#editLoad{{ $item->id }}">{{ $manager ? 'Corregir' : 'Editar' }}</button></div></article>
        @if ($item->changes->isNotEmpty())<details class="p-3"><summary>Historial de cambios</summary>@foreach ($item->changes->sortByDesc('id') as $change)<p>{{ $change->created_at->format('d/m/Y H:i') }} · {{ $change->user->name }} · {{ $change->reason ?? 'Actualización de horario' }}</p>@endforeach</details>@endif
    @empty<div class="teacher-load-empty">No hay clases registradas para esta consulta.</div>@endforelse
</section>
<div class="modal fade" id="academicLoadModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><form method="POST" action="{{ $manager ? route('coordinacion.carga-academica.store', $teacher) : route('docente.carga-academica.store') }}" class="modal-content teacher-load-modal">
    @csrf<input type="hidden" name="submission_key" value="{{ old('form_id') === 'new' ? old('submission_key', (string) Str::uuid()) : Str::uuid() }}">
    <div class="modal-header"><h5>Agregar clase</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
    <div class="modal-body">@include('academic.loads.fields', ['load' => null])</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar clase</button></div>
</form></div></div>
@foreach ($loads as $item)
<div class="modal fade" id="editLoad{{ $item->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-lg modal-dialog-centered"><form method="POST" action="{{ route($portal.'.carga-academica.update', $item) }}" class="modal-content teacher-load-modal">
    @csrf @method('PUT')<input type="hidden" name="revision" value="{{ $item->revision }}">
    <div class="modal-header"><h5>{{ $manager ? 'Corregir clase' : 'Editar clase' }}</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
    <div class="modal-body">@include('academic.loads.fields', ['load' => $item])</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary">Guardar cambios</button></div>
</form></div></div>
@endforeach
@endsection
@push('scripts')
<script type="application/json" id="academicCatalogData">{!! \Illuminate\Support\Js::encode($catalog) !!}</script>
<script src="{{ asset('js/academic-load-catalog.js') }}" defer></script>
@endpush
