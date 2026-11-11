@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : (auth()->user()->hasRole('JEFE_INMEDIATO') ? 'layouts.company' : 'layouts.app')))
@section('title', 'Defensa | UNICAH')
@section('content')
@php
    $manager = in_array(auth()->user()->role->name, ['COORDINACION', 'ROOT'], true);
    $portal = $manager ? 'coordinacion' : (auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'docente' : 'academico'));
    $participants = $defensa->participants();
    $mutable = in_array($defensa->status, ['Pendiente', 'En coordinación'], true);
@endphp
<div class="page-header"><div><h1>Defensa</h1><p>{{ $defensa->committee->practice->student->user->name }} · {{ $defensa->committee->practice->academic_period }} {{ $defensa->committee->practice->academic_year }}</p></div><a class="btn btn-light coord-back-button" href="{{ route($portal === 'estudiante' ? 'estudiante.defensa' : $portal.'.defensas') }}">Volver</a></div>
@include('academic.closing-feedback')
<section class="defense-overview-card">
    <div class="defense-overview-content">
        <span class="section-label">ESTADO DE DEFENSA</span><h2>{{ $defensa->status }}</h2>
        <p>Duración: {{ $defensa->duration_minutes }} minutos</p>
        <p>Fecha y hora confirmadas: {{ $defensa->selectedOption?->starts_at->format('d/m/Y H:i') ?? 'Pendiente' }}</p>
        <p>{{ $defensa->observation }}</p>
    </div>
</section>
<section class="content-card mb-4">
    <div class="card-header"><div><h2>Terna de defensa</h2><p>{{ $defensa->committee->status }}</p></div></div>
    @include('academic.committees.members', ['members' => $defensa->committee->members])
</section>
@if ($manager && $mutable)
    @if ($defensa->options->isEmpty())
        <section class="content-card mb-4">
            <form method="POST" action="{{ route('coordinacion.defensa.duration', $defensa) }}" class="p-3">
                @csrf @method('PUT')
                <label for="duration" class="form-label">Duración</label><select id="duration" name="duration_minutes" class="form-select mb-3">@foreach (\App\Models\Defense::DURATIONS as $minutes)<option value="{{ $minutes }}" @selected($defensa->duration_minutes === $minutes)>{{ $minutes }} minutos</option>@endforeach</select><button class="btn btn-primary">Guardar duración</button>
            </form>
        </section>
    @endif
    <section class="content-card mb-4">
        <div class="card-header"><div><h2>Proponer fecha y hora</h2></div></div>
        <form method="POST" action="{{ route('coordinacion.defensa.options', $defensa) }}" class="p-3">
            @csrf<label for="optionStart" class="form-label">Inicio</label><input id="optionStart" name="starts_at" type="datetime-local" class="form-control mb-3" required value="{{ old('starts_at') }}"><button class="btn btn-primary">Registrar opción</button>
        </form>
    </section>
@endif
<section class="content-card defense-options-card mb-4">
    <div class="card-header"><div><h2>Opciones de fecha y hora</h2><p>La programación se confirma cuando el estudiante y todos los integrantes de la terna declaran disponibilidad en la misma opción.</p></div></div>
    <div class="defense-options-list">
        @forelse ($defensa->options->sortBy('starts_at') as $option)
            @php
                $available = $option->availabilities->whereIn('user_id', $participants->pluck('id'))->where('available', true)->count();
                $currentAssignment = $option->committee_revision === $defensa->committee->revision;
                $answer = $option->availabilities->firstWhere('user_id', auth()->id());
            @endphp
            <article class="defense-option">
                <div class="defense-date-box"><strong>{{ $option->starts_at->format('d') }}</strong><span>{{ $option->starts_at->format('m/Y') }}</span></div>
                <div class="defense-option-info">
                    <h3>{{ $option->starts_at->format('H:i') }} – {{ $option->ends_at->format('H:i') }}</h3>
                    <p>{{ $option->duration_minutes }} minutos · {{ $available }} de {{ $participants->count() }} participantes disponibles</p>
                    @if (!$currentAssignment)<p>La terna cambió después de proponer este horario.</p>@endif
                    @if ($classConflicts->get($option->id))<p class="text-danger">La opción coincide con horas de clase registradas.</p>@endif
                    <div class="table-container table-responsive"><table class="data-table">
                        <thead><tr><th>Participante</th><th>Disponibilidad</th><th>Observación</th></tr></thead>
                        <tbody>@foreach ($participants as $participant)
                            @php($response = $option->availabilities->firstWhere('user_id', $participant->id))
                            <tr><td>{{ $participant->name }}</td><td>{{ $response ? ($response->available ? 'Disponible' : 'No disponible') : 'Sin respuesta' }}</td><td>{{ $response?->observation }}</td></tr>
                        @endforeach</tbody>
                    </table></div>
                    @can('respond', $defensa)
                        @if ($mutable && $currentAssignment)
                            <form method="POST" action="{{ route('academico.defensa.respond', [$defensa, $option]) }}" class="mt-3">
                                @csrf<label for="available{{ $option->id }}" class="form-label">Mi disponibilidad</label>
                                <select id="available{{ $option->id }}" name="available" class="form-select mb-2" required><option value="">Seleccionar respuesta</option><option value="1" @selected($answer?->available === true)>Disponible</option><option value="0" @selected($answer?->available === false)>No disponible</option></select>
                                <label for="availabilityNote{{ $option->id }}" class="form-label">Observación</label><textarea id="availabilityNote{{ $option->id }}" name="observation" class="form-control mb-2" maxlength="5000">{{ $answer?->observation }}</textarea>
                                <button class="btn btn-primary">Guardar disponibilidad</button>
                            </form>
                        @endif
                    @endcan
                    @if ($manager && $mutable)
                        <form method="POST" action="{{ route('coordinacion.defensa.confirm', [$defensa, $option]) }}" class="mt-3">@csrf<button class="btn btn-success" @disabled(!$currentAssignment || $available !== $participants->count() || $defensa->committee->status !== 'Confirmada' || $classConflicts->get($option->id))>Confirmar programación</button></form>
                    @endif
                </div>
            </article>
        @empty
            <div class="defense-locked-state">Aún no hay opciones de horario registradas.</div>
        @endforelse
    </div>
</section>
<section class="content-card mb-4">
    <div class="card-header"><div><h2>Horas de clase registradas</h2><p>Los integrantes declaran su disponibilidad teniendo en cuenta sus horas de clase.</p></div></div>
    <div class="table-container table-responsive"><table class="data-table">
        <thead><tr><th>Docente</th><th>Inicio</th><th>Finalización</th></tr></thead>
        <tbody>@forelse ($clases as $clase)<tr><td>{{ $clase->teacher->user->name }}</td><td>{{ $clase->starts_at->format('d/m/Y H:i') }}</td><td>{{ $clase->ends_at->format('d/m/Y H:i') }}</td></tr>@empty<tr><td colspan="3">No hay horas de clase registradas para contrastar.</td></tr>@endforelse</tbody>
    </table></div>
    @can('respond', $defensa)
        @if ($mutable && auth()->user()->teacher)
            <form method="POST" action="{{ route('academico.defensa.classes', $defensa) }}" class="p-3">
                @csrf
                <label for="className" class="form-label">Asignatura</label><input id="className" name="course_name" class="form-control mb-2" maxlength="255">
                <label for="classStart" class="form-label">Inicio de clase</label><input id="classStart" name="starts_at" type="datetime-local" class="form-control mb-2" required>
                <label for="classEnd" class="form-label">Finalización de clase</label><input id="classEnd" name="ends_at" type="datetime-local" class="form-control mb-2" required>
                <button class="btn btn-primary">Registrar horas de clase</button>
            </form>
        @endif
    @endcan
</section>
@if ($manager && $defensa->status === 'Programada')
    <section class="content-card"><form method="POST" action="{{ route('coordinacion.defensa.performed', $defensa) }}" class="p-3">@csrf<label for="defenseObservation" class="form-label">Observación de realización</label><textarea id="defenseObservation" name="observation" class="form-control mb-3" maxlength="5000"></textarea><button class="btn btn-primary">Registrar como realizada</button></form></section>
@endif
@endsection
