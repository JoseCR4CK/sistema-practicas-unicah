<section class="content-card p-3 p-md-4 mt-4">
    <h2 class="h5">Asignaturas cursadas durante la práctica</h2>
    <p class="text-muted">Declaradas en la solicitud de acceso y revisadas por Coordinación Académica.</p>
    <ul>@forelse($student?->pendingSubjects ?? [] as $subject)<li>{{ $subject->code }} {{ $subject->name }}</li>@empty<li>No declaró asignaturas pendientes para cursar durante la práctica.</li>@endforelse</ul>
    @if($student?->documents->isNotEmpty())
        <h3 class="h6">Documentos de la solicitud de acceso</h3>
        <ul>@foreach($student->documents as $document)<li>{{ \App\Models\AcademicDocument::ACCESS_TYPES[$document->type] ?? $document->type }} @foreach($document->currentVersion?->files ?? [] as $index => $file)<a href="{{ route('academico.archivo-acceso', [$document->currentVersion, $index]) }}">{{ $file['name'] }}</a> @endforeach</li>@endforeach</ul>
    @endif
</section>
