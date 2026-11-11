@extends($studentPortal ? 'layouts.student' : 'layouts.app')
@section('title', 'Detalle de documento | UNICAH')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">REVISIÓN DOCUMENTAL</span><h1>{{ \App\Models\AcademicDocument::TYPES[$documento->type] ?? $documento->type }}</h1><p>Revisa la versión actual y el historial del documento presentado.</p></div><a href="{{ route($studentPortal ? 'estudiante.documentos' : 'coordinacion.documentos') }}" class="btn btn-light coord-back-button">Volver a documentos</a></div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="coord-doc-detail-header mb-4">
    <div class="coord-doc-detail-icon"><i class="bi bi-file-earmark-text"></i></div>
    <div><span>{{ $documento->type === 'SOLICITUD_EMPRESA' ? 'DOCUMENTO OPCIONAL' : 'DOCUMENTO INICIAL' }}</span><strong>{{ \App\Models\AcademicDocument::TYPES[$documento->type] ?? $documento->type }}</strong><small>Versión {{ $documento->currentVersion?->version }}</small></div>
    @if (!$studentPortal && $documento->currentVersion?->status === 'Pendiente de revisión')
        <div class="coord-doc-detail-actions">
            <button class="btn coord-doc-correction-btn" data-bs-toggle="modal" data-bs-target="#correctionModal">Solicitar corrección</button>
            <form method="POST" action="{{ route('coordinacion.documento.review', $documento) }}">@csrf<input type="hidden" name="version_id" value="{{ $documento->currentVersion->id }}"><input type="hidden" name="status" value="Aprobado"><button class="btn btn-success">Aprobar documento</button></form>
        </div>
    @endif
</section>
<section class="row g-3 mb-4">
    <div class="col-12 col-xl-6"><div class="content-card h-100"><div class="card-header"><div><h2>Estudiante</h2></div></div><div class="coord-doc-student"><div class="coord-doc-student-avatar">{{ Str::initials($documento->practice->student->user->name, true) }}</div><div><strong>{{ $documento->practice->student->user->name }}</strong><small>{{ $documento->practice->student->account_number }}</small><small>{{ $documento->practice->student->career }}</small></div></div></div></div>
    <div class="col-12 col-xl-6"><div class="content-card h-100"><div class="card-header"><div><h2>Versión actual</h2></div></div><div class="coord-doc-current-file"><i class="bi bi-file-earmark-text"></i><div><span>Archivo adjunto</span><strong>Sin archivo registrado</strong><small>{{ $documento->currentVersion?->uploaded_at->format('d/m/Y H:i') }}</small></div><span class="coord-doc-status">{{ $documento->currentVersion?->status }}</span></div></div></div>
</section>
<section class="content-card mb-4"><div class="card-header"><div><h2>Observación actual</h2></div></div><div class="coord-doc-observation">{{ $documento->currentVersion?->observation ?? 'Sin observaciones de revisión' }}</div></section>
<section class="content-card"><div class="card-header"><div><h2>Historial de versiones</h2><p>Conserva la trazabilidad de correcciones y documentos presentados.</p></div></div>
    @foreach ($documento->versions->sortByDesc('version') as $version)
        <article class="coord-doc-version p-3"><strong>Versión {{ $version->version }} · {{ $version->status }}</strong><p>{{ $version->uploaded_at->format('d/m/Y H:i') }} · {{ $version->uploader->name }}</p><p>{{ $version->submission_note }}</p><p>{{ $version->observation }}</p>@if ($version->reviewer)<small>{{ $version->reviewer->name }} · {{ $version->reviewed_at->format('d/m/Y H:i') }}</small>@endif</article>
    @endforeach
</section>
@if (!$studentPortal && $documento->currentVersion)
<div class="modal fade" id="correctionModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered">
    <form class="modal-content coord-company-modal" method="POST" action="{{ route('coordinacion.documento.review', $documento) }}">
        @csrf<input type="hidden" name="version_id" value="{{ $documento->currentVersion->id }}"><input type="hidden" name="status" value="Requiere corrección">
        <div class="modal-header"><h5 class="modal-title">Solicitar corrección</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body"><label for="reviewObservation" class="form-label">Observación de Coordinación</label><textarea id="reviewObservation" name="observation" class="form-control" rows="4" required maxlength="5000">{{ old('observation') }}</textarea></div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-warning">Solicitar corrección</button></div>
    </form>
</div></div>
@endif
@endsection
