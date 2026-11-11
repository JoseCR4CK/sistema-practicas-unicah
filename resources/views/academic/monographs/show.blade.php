@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : (auth()->user()->hasRole('JEFE_INMEDIATO') ? 'layouts.company' : 'layouts.app')))
@section('title', 'Monografía | UNICAH')
@section('content')
@php
    $portal = in_array(auth()->user()->role->name, ['COORDINACION', 'ROOT'], true) ? 'coordinacion' : (auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'docente' : 'academico'));
    $manager = in_array(auth()->user()->role->name, ['COORDINACION', 'ROOT'], true);
@endphp
<div class="page-header">
    <div><h1>Monografía</h1><p>{{ $monografia->practice->student->user->name }} · {{ $monografia->practice->academic_period }} {{ $monografia->practice->academic_year }}</p></div>
    <a class="btn btn-light coord-back-button" href="{{ route($portal === 'academico' ? 'academico.ternas' : ($portal === 'estudiante' ? 'estudiante.monografia' : $portal.'.monografias')) }}">Volver</a>
</div>
@include('academic.closing-feedback')
<section class="monograph-overview">
    <div>
        <span class="section-label">VERSIÓN ACTUAL</span>
        <h2>{{ $monografia->currentVersion?->title ?? 'Sin versión' }}</h2>
        <div class="monograph-meta">
            <span>Versión {{ $monografia->currentVersion?->number }}</span>
            <span>{{ $monografia->currentVersion?->submitted_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
    <div class="monograph-overview-actions"><span class="status-pill monograph-review">{{ $monografia->statusLabel() }}</span></div>
</section>
<section class="content-card monograph-process-card mb-4">
    <div class="card-header"><div><h2>Proceso de revisión</h2><p>Entrega límite: {{ $monografia->practice->monographDeadline()?->format('d/m/Y') ?? 'Fecha final de práctica pendiente' }}</p></div></div>
    <div class="monograph-review-flow">
        @foreach ($monografia->currentReviews() as $revision)
            <article class="review-step {{ $revision->status === 'Aprobada' ? 'completed' : ($revision->status === 'En revisión' ? 'active' : 'pending') }}">
                <div class="review-step-indicator"><div class="review-circle"><i class="bi bi-journal-check"></i></div><div class="review-line"></div></div>
                <div class="review-step-content">
                    <div class="review-step-header"><div><h3>{{ \App\Models\Monograph::REVIEW_LABELS[$revision->stage] }}</h3></div><span class="badge info">{{ $revision->status }}</span></div>
                    <div class="review-time-box">
                        <div><span>Tiempo máximo</span><strong>{{ \App\Models\Monograph::REVIEW_DAYS[$revision->stage] }} días</strong></div>
                        <div><span>Inicio</span><strong>{{ $revision->started_at?->format('d/m/Y H:i') ?? 'Pendiente' }}</strong></div>
                        <div><span>Fecha límite</span><strong>{{ $revision->due_at?->format('d/m/Y H:i') ?? 'Pendiente' }}</strong></div>
                    </div>
                    <p>Responsable: {{ $revision->reviewer?->name ?? 'Pendiente de identificar' }}</p>
                    <p>{{ $revision->observation }}</p>
                    @if ($revision->status === 'En revisión' && ($manager || ($portal === 'docente' && $revision->stage === 'tecnica' && $revision->reviewer_id === auth()->id()) || ($portal !== 'estudiante' && $revision->stage === 'coordinador' && $committeeCoordinatorId === auth()->id())))
                        <form method="POST" action="{{ route($portal.'.monografia.review', $monografia) }}">
                            @csrf<input type="hidden" name="review_id" value="{{ $revision->id }}">
                            <label for="result{{ $revision->id }}" class="form-label">Resultado</label>
                            <select id="result{{ $revision->id }}" name="status" class="form-select mb-2"><option>Aprobada</option><option>Requiere corrección</option></select>
                            <label for="note{{ $revision->id }}" class="form-label">Observación</label>
                            <textarea id="note{{ $revision->id }}" name="observation" class="form-control mb-2" maxlength="5000"></textarea>
                            <button class="btn btn-primary">Registrar revisión</button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>
@if ($portal === 'estudiante' && $monografia->statusLabel() === 'Requiere corrección')
    <section class="content-card mb-4">
        <div class="card-header"><div><h2>Registrar corrección</h2></div></div>
        <form method="POST" action="{{ route('estudiante.monografia.submit') }}" class="p-3">
            @csrf<input type="hidden" name="practice_id" value="{{ $monografia->practice_id }}"><input type="hidden" name="submission_key" value="{{ Str::uuid() }}">
            @include('academic.monographs.version-fields', ['version' => $monografia->currentVersion])
            <button class="btn btn-primary">Registrar nueva versión</button>
        </form>
    </section>
@endif
<section class="content-card mb-4">
    <div class="card-header"><div><h2>Historial de versiones</h2></div></div>
    <div class="table-container table-responsive"><table class="data-table">
        <thead><tr><th>Versión</th><th>Título</th><th>Estado</th><th>Fecha</th><th>Registrada por</th><th>Observación</th></tr></thead>
        <tbody>@foreach ($monografia->versions->sortByDesc('number') as $version)<tr><td>{{ $version->number }}</td><td>{{ $version->title }}</td><td>{{ $version->status }}</td><td>{{ $version->submitted_at->format('d/m/Y H:i') }}</td><td>{{ $version->submitter->name }}</td><td>{{ $version->observation }}</td></tr>@endforeach</tbody>
    </table></div>
</section>
<section class="content-card">
    <div class="card-header"><div><h2>Historial de revisiones</h2></div></div>
    <div class="table-container table-responsive"><table class="data-table">
        <thead><tr><th>Revisión</th><th>Intento</th><th>Versión</th><th>Estado</th><th>Fecha límite</th><th>Resultado registrado por</th><th>Observación</th></tr></thead>
        <tbody>@foreach ($monografia->reviews->sortByDesc('id') as $revision)<tr><td>{{ \App\Models\Monograph::REVIEW_LABELS[$revision->stage] }}</td><td>{{ $revision->attempt }}</td><td>{{ $revision->version->number }}</td><td>{{ $revision->status }}</td><td>{{ $revision->due_at?->format('d/m/Y H:i') ?? 'Pendiente' }}</td><td>{{ $revision->recorder?->name ?? 'Pendiente' }}</td><td>{{ $revision->observation }}</td></tr>@endforeach</tbody>
    </table></div>
</section>
@endsection
