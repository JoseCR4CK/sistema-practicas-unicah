@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : (auth()->user()->hasRole('JEFE_INMEDIATO') ? 'layouts.company' : 'layouts.app')))
@section('title', 'Terna | UNICAH')
@section('content')
@php
    $manager = in_array(auth()->user()->role->name, ['COORDINACION', 'ROOT'], true);
    $portal = $manager ? 'coordinacion' : (auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'docente' : 'academico'));
@endphp
<div class="page-header"><div><h1>Terna</h1><p>Consulta los integrantes y la asignación de cada proceso de práctica.</p></div></div>
@include('academic.closing-feedback')
<section class="content-card mb-4">
    <div class="table-container table-responsive"><table class="data-table">
        <thead><tr><th>Estudiante</th><th>Período</th><th>Estado</th><th>Integrantes</th><th></th></tr></thead>
        <tbody>
            @forelse ($ternas as $terna)
                <tr>
                    <td>{{ $terna->practice->student->user->name }}</td>
                    <td>{{ $terna->practice->academic_period }} {{ $terna->practice->academic_year }}</td>
                    <td>{{ $terna->status }}</td>
                    <td>{{ $terna->members->count() }}</td>
                    <td><a href="{{ route($portal.'.terna.show', $terna) }}" class="coord-teacher-view"><i class="bi bi-eye"></i> Ver terna</a></td>
                </tr>
            @empty
                <tr><td colspan="5">No hay ternas registradas.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</section>
@if ($manager)
    <section class="content-card">
        <div class="card-header"><div><h2>Registrar terna pendiente</h2></div></div>
        <form method="POST" action="{{ route('coordinacion.ternas.store') }}" class="p-3">
            @csrf<input type="hidden" name="revision" value="0"><input type="hidden" name="status" value="Pendiente de asignación">
            <label for="committeePractice" class="form-label">Práctica</label>
            <select name="practice_id" id="committeePractice" class="form-select mb-3" required>
                @foreach ($practicas as $practica)
                    <option value="{{ $practica->id }}">{{ $practica->student->user->name }} · {{ $practica->academic_period }} {{ $practica->academic_year }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary" @disabled($practicas->isEmpty())>Registrar terna</button>
        </form>
    </section>
@endif
@endsection
