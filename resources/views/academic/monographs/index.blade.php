@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : 'layouts.app'))
@section('title', 'Monografía | UNICAH')
@section('content')
@php($portal = auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'docente' : 'coordinacion'))
<div class="page-header">
    <div><h1>Monografía</h1><p>Consulta las versiones, revisiones y fechas de entrega del proceso de práctica profesional.</p></div>
</div>
@include('academic.closing-feedback')
<section class="content-card mb-4">
    <div class="card-header"><div><h2>Monografías registradas</h2></div></div>
    <div class="table-container table-responsive">
        <table class="data-table">
            <thead><tr><th>Estudiante</th><th>Período</th><th>Estado</th><th>Versión</th><th>Entrega límite</th><th></th></tr></thead>
            <tbody>
                @forelse ($monografias as $monografia)
                    <tr>
                        <td>{{ $monografia->practice->student->user->name }}</td>
                        <td>{{ $monografia->practice->academic_period }} {{ $monografia->practice->academic_year }}</td>
                        <td>{{ $monografia->statusLabel() }}</td>
                        <td>{{ $monografia->currentVersion?->number ?? 'Sin versión' }}</td>
                        <td>{{ $monografia->practice->monographDeadline()?->format('d/m/Y') ?? 'Fecha final de práctica pendiente' }}</td>
                        <td><a class="coord-teacher-view" href="{{ route($portal.'.monografia.show', $monografia) }}"><i class="bi bi-eye"></i> Ver</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">No hay monografías registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@if ($portal === 'estudiante' && $practicas->isNotEmpty())
    <section class="content-card">
        <div class="card-header"><div><h2>Registrar monografía</h2><p>Registra el título y las observaciones de la versión.</p></div></div>
        <form method="POST" action="{{ route('estudiante.monografia.submit') }}" class="p-3">
            @csrf
            <input type="hidden" name="submission_key" value="{{ Str::uuid() }}">
            <label for="monographPractice" class="form-label">Práctica</label>
            <select name="practice_id" id="monographPractice" class="form-select mb-3" required>
                @foreach ($practicas as $practica)
                    <option value="{{ $practica->id }}">{{ $practica->academic_period }} {{ $practica->academic_year }} · {{ $practica->stage }}</option>
                @endforeach
            </select>
            @include('academic.monographs.version-fields', ['version' => null])
            <button class="btn btn-primary">Registrar monografía</button>
        </form>
    </section>
@endif
@endsection
