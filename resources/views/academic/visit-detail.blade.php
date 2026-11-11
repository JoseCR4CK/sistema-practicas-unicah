@extends($portal === 'docente' ? 'layouts.teacher' : ($portal === 'estudiante' ? 'layouts.student' : 'layouts.app'))
@section('title', 'Detalle de visita | UNICAH')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">VISITA DE SEGUIMIENTO</span><h1>{{ $visita->practice->student->user->name }}</h1></div><div class="d-flex gap-2"><a href="{{ route($portal === 'estudiante' ? 'estudiante.visita' : $portal.'.visitas') }}" class="btn btn-light coord-back-button">Volver</a>@if ($portal !== 'estudiante')<a href="{{ route($portal === 'docente' ? 'docente.practicante' : 'coordinacion.estudiante', $visita->practice->student_id) }}" class="btn btn-primary coord-back-button">Ver expediente</a>@endif</div></div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="teacher-visit-detail-profile mb-4"><div class="coord-practice-detail-avatar">{{ Str::initials($visita->practice->student->user->name, true) }}</div><div><span>PRACTICANTE</span><strong>{{ $visita->practice->student->user->name }}</strong><small>{{ $visita->practice->student->account_number }}</small></div><div class="teacher-visit-detail-summary"><div><span>Día actual</span><strong>{{ $visita->practice->currentDay() ?? '—' }}</strong></div><div><span>Estado</span><strong>{{ $visita->effectiveStatus() }}</strong></div></div></section>
<div class="coord-visit-message"><i class="bi bi-info-circle"></i><div><strong>Visita de seguimiento</strong><span>Puede realizarse a partir del día 45 de la práctica.</span></div></div>
<section class="row g-3 mb-4">
    <div class="col-12 col-xl-6"><div class="content-card h-100"><div class="card-header"><div><h2>Empresa</h2></div></div><div class="coord-practice-info-card"><i class="bi bi-buildings"></i><div><strong>{{ $visita->practice->company?->official_name ?? 'Sin empresa asignada' }}</strong><small>{{ $visita->practice->companyResponsible?->full_name ?? 'Sin responsable asignado' }}</small><small>Docente de seguimiento: {{ $visita->practice->teacher?->user?->name ?? 'Sin asignar' }}</small></div></div></div></div>
    <div class="col-12 col-xl-6"><div class="content-card h-100"><div class="card-header"><div><h2>Visita</h2></div></div><div class="coord-request-data-grid teacher-visit-data-grid"><div><span>Habilitada desde</span><strong>{{ $visita->enabledFrom()?->format('d/m/Y') ?? 'Inicio de práctica pendiente' }}</strong></div><div><span>Fecha programada</span><strong>{{ $visita->scheduled_date?->format('d/m/Y') ?? 'Sin programar' }}</strong></div><div><span>Fecha realizada</span><strong>{{ $visita->completed_date?->format('d/m/Y') ?? 'Sin realizar' }}</strong></div></div></div></div>
</section>
@can('update', $visita)
    @if ($visita->status !== 'Realizada')
        <section class="content-card mb-4"><div class="card-header"><div><h2>Registro de la visita</h2></div></div>
            <form method="POST" action="{{ route('docente.visita.update', $visita) }}" class="p-3">
                @csrf<label for="visitStatus" class="form-label">Acción</label><select id="visitStatus" name="status" class="form-select mb-3"><option value="Programada">Programar visita</option><option value="Realizada">Marcar como realizada</option></select>
                <label for="visitDate" class="form-label">Fecha</label><input type="date" id="visitDate" name="date" class="form-control mb-3" required min="{{ $visita->enabledFrom()?->format('Y-m-d') }}" value="{{ old('date', $visita->scheduled_date?->format('Y-m-d')) }}">
                <label for="visitObservation" class="form-label">Observaciones</label><textarea id="visitObservation" name="observation" class="form-control mb-3" maxlength="5000">{{ old('observation', $visita->observation) }}</textarea><button class="btn btn-primary">Guardar visita</button>
            </form>
        </section>
    @endif
@endcan
<section class="content-card"><div class="card-header"><div><h2>Observaciones registradas</h2></div></div><div class="coord-request-observation"><p>{{ $visita->observation ?? 'Sin observaciones' }}</p>@if ($visita->performer)<small>Realizada por {{ $visita->performer->name }}</small>@endif</div></section>
@endsection
