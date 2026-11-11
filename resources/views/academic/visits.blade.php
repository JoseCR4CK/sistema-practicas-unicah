@extends($portal === 'docente' ? 'layouts.teacher' : ($portal === 'estudiante' ? 'layouts.student' : 'layouts.app'))
@section('title', 'Visitas | UNICAH')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">SEGUIMIENTO DE VISITAS</span><h1>Visitas</h1><p>Consulta la habilitación y el estado de las visitas de práctica profesional.</p></div></div>
<section class="row g-3 mb-4">
    @foreach (['Total' => $visitas->count(), 'Habilitadas' => $visitas->filter(fn ($visita) => $visita->effectiveStatus() === 'Habilitada')->count(), 'Programadas' => $visitas->where('status', 'Programada')->count(), 'Realizadas' => $visitas->where('status', 'Realizada')->count()] as $etiqueta => $cantidad)
        <div class="col-12 col-md-3"><div class="teacher-load-stat"><div class="teacher-load-stat-icon"><i class="bi bi-calendar3"></i></div><div><span>{{ $etiqueta }}</span><strong>{{ $cantidad }}</strong></div></div></div>
    @endforeach
</section>
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@if ($portal === 'docente')
<section class="content-card mb-4"><form method="POST" action="{{ route('docente.visitas.store') }}" class="p-3 d-flex gap-2">@csrf<input type="hidden" name="submission_key" value="{{ Str::uuid() }}"><select name="practice_id" class="form-select" required>@foreach ($practicas as $practica)<option value="{{ $practica->id }}">{{ $practica->student->user->name }} · {{ $practica->academic_period }} {{ $practica->academic_year }}</option>@endforeach</select><button class="btn btn-primary" @disabled($practicas->isEmpty())>Registrar visita</button></form></section>
@endif
@if ($portal === 'estudiante' && $studentMessage)
    <div class="coord-visit-message" role="status">
        <i class="bi bi-info-circle"></i>
        <div>{{ $studentMessage }}</div>
    </div>
@endif
<section class="content-card">
    <div class="coord-visit-toolbar"><div class="coord-company-search"><i class="bi bi-search"></i><input type="search" id="flowSearch" placeholder="Buscar estudiante, empresa o docente..."></div><select id="flowStatus" class="form-select"><option value="">Todos los estados</option>@foreach (['Pendiente', 'Habilitada', 'Programada', 'Realizada'] as $estado)<option>{{ $estado }}</option>@endforeach</select></div>
    @forelse ($visitas as $visita)
        <article class="coord-visit-row" data-flow-row data-status="{{ $visita->effectiveStatus() }}">
            <div class="coord-request-avatar">{{ Str::initials($visita->practice->student->user->name, true) }}</div>
            <div class="coord-visit-student">
                <strong>{{ $visita->practice->student->user->name }}</strong>
                <small>{{ $visita->practice->student->account_number }}</small>
            </div>
            <div><span class="coord-request-label">Empresa</span><strong>{{ $visita->practice->company?->official_name ?? 'Sin empresa asignada' }}</strong></div>
            <div><span class="coord-request-label">Docente de seguimiento</span><strong>{{ $visita->practice->teacher?->user?->name ?? 'Sin docente asignado' }}</strong></div>
            <div><span class="coord-request-label">Día de práctica</span><strong>{{ $visita->practice->currentDay() ?? '—' }}</strong></div>
            <span class="coord-visit-status {{ ['Pendiente' => 'locked', 'Habilitada' => 'enabled', 'Programada' => 'scheduled', 'Realizada' => 'completed'][$visita->effectiveStatus()] }}">{{ $visita->effectiveStatus() }}</span>
            <a class="coord-teacher-view" href="{{ route($portal === 'estudiante' ? 'estudiante.visita.show' : $portal.'.visita', $visita) }}"><i class="bi bi-eye"></i> Ver visita</a>
        </article>
    @empty
        @if ($portal !== 'estudiante')
            <div class="teacher-load-empty">No hay visitas registradas.</div>
        @endif
    @endforelse
</section>
@endsection
@push('scripts')<script src="{{ asset('js/academic-filters.js') }}"></script>@endpush
