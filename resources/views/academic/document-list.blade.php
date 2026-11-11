@forelse ($documentos as $documento)
    <article class="coord-doc-row" data-flow-row data-status="{{ $documento->currentVersion?->status }}" data-career="{{ $documento->type }}">
        <div class="coord-doc-icon"><i class="bi bi-file-earmark-text"></i></div>
        <div class="coord-doc-main"><strong>{{ \App\Models\AcademicDocument::TYPES[$documento->type] ?? $documento->type }}</strong><small>{{ $documento->practice->student->user->name }} · {{ $documento->practice->student->account_number }}</small></div>
        <div><span class="coord-request-label">Versión</span><strong>{{ $documento->currentVersion?->version ?? 'Sin versión' }}</strong></div>
        <div><span class="coord-request-label">Fecha de registro</span><strong>{{ $documento->currentVersion?->uploaded_at->format('d/m/Y') ?? 'Sin registro' }}</strong></div>
        <span class="coord-doc-status {{ ['Aprobado' => 'approved', 'Requiere corrección' => 'correction'][$documento->currentVersion?->status] ?? 'pending' }}">{{ $documento->currentVersion?->status ?? 'Sin versión' }}</span>
        <a class="coord-teacher-view" href="{{ route($studentPortal ? 'estudiante.documento' : 'coordinacion.documento', $documento) }}"><i class="bi bi-eye"></i> Ver documento</a>
    </article>
@empty
    <div class="teacher-load-empty">No hay documentos registrados.</div>
@endforelse
