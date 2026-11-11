@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : (auth()->user()->hasRole('JEFE_INMEDIATO') ? 'layouts.company' : 'layouts.app')))
@section('title', 'Defensa | UNICAH')
@section('content')
@php
    $manager = in_array(auth()->user()->role->name, ['COORDINACION', 'ROOT'], true);
    $portal = $manager ? 'coordinacion' : (auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'docente' : 'academico'));
@endphp
<div class="page-header"><div><h1>Defensa</h1><p>Consulta la programación y las opciones de fecha y hora del proceso.</p></div></div>
@include('academic.closing-feedback')
<section class="content-card mb-4">
    <div class="table-container table-responsive"><table class="data-table">
        <thead><tr><th>Estudiante</th><th>Período</th><th>Estado</th><th>Duración</th><th>Fecha confirmada</th><th></th></tr></thead>
        <tbody>
            @forelse ($defensas as $defensa)
                <tr>
                    <td>{{ $defensa->committee->practice->student->user->name }}</td>
                    <td>{{ $defensa->committee->practice->academic_period }} {{ $defensa->committee->practice->academic_year }}</td>
                    <td>{{ $defensa->status }}</td>
                    <td>{{ $defensa->duration_minutes }} minutos</td>
                    <td>{{ $defensa->selectedOption?->starts_at->format('d/m/Y H:i') ?? 'Sin fecha confirmada' }}</td>
                    <td><a class="coord-teacher-view" href="{{ route($portal.'.defensa.show', $defensa) }}"><i class="bi bi-eye"></i> Ver defensa</a></td>
                </tr>
            @empty
                <tr><td colspan="6">No hay defensas registradas.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</section>
@if ($manager)
    <section class="content-card">
        <div class="card-header"><div><h2>Registrar proceso de programación</h2></div></div>
        <form method="POST" action="{{ route('coordinacion.defensas.store') }}" class="p-3">
            @csrf
            <label for="defenseCommittee" class="form-label">Terna y práctica</label>
            <select id="defenseCommittee" name="academic_committee_id" class="form-select mb-3" required>
                @foreach ($ternas as $terna)
                    <option value="{{ $terna->id }}">{{ $terna->practice->student->user->name }} · {{ $terna->practice->academic_period }} {{ $terna->practice->academic_year }} · {{ $terna->status }}</option>
                @endforeach
            </select>
            <label for="defenseDuration" class="form-label">Duración</label>
            <select id="defenseDuration" name="duration_minutes" class="form-select mb-3">
                @foreach (\App\Models\Defense::DURATIONS as $minutes)<option value="{{ $minutes }}">{{ $minutes }} minutos</option>@endforeach
            </select>
            <button class="btn btn-primary" @disabled($ternas->isEmpty())>Registrar defensa pendiente</button>
        </form>
    </section>
@endif
@endsection
