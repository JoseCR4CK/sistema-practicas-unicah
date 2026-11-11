@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : (auth()->user()->hasRole('JEFE_INMEDIATO') ? 'layouts.company' : 'layouts.app')))
@section('title', 'Terna | UNICAH')
@section('content')
@php
    $manager = in_array(auth()->user()->role->name, ['COORDINACION', 'ROOT'], true);
    $portal = $manager ? 'coordinacion' : (auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'docente' : 'academico'));
@endphp
<div class="page-header"><div><h1>Terna de defensa</h1><p>{{ $terna->practice->student->user->name }} · {{ $terna->practice->academic_period }} {{ $terna->practice->academic_year }}</p></div><a class="btn btn-light coord-back-button" href="{{ route($portal === 'estudiante' ? 'estudiante.terna' : $portal.'.ternas') }}">Volver</a></div>
@include('academic.closing-feedback')
<section class="defense-overview-card">
    <div class="defense-overview-content"><span class="section-label">ESTADO DE LA TERNA</span><h2>{{ $terna->status }}</h2><p>{{ $terna->confirmed_at?->format('d/m/Y H:i') }}</p></div>
</section>
<section class="content-card mb-4">
    <div class="card-header"><div><h2>Integrantes</h2></div></div>
    @include('academic.committees.members', ['members' => $terna->members])
</section>
@can('update', $terna)
    <section class="content-card">
        <div class="card-header"><div><h2>Asignación manual</h2><p>La confirmación requiere tres personas distintas y permite como máximo un coordinador.</p></div></div>
        <form method="POST" action="{{ route('coordinacion.terna.update', $terna) }}" class="p-3">
            @csrf @method('PUT')<input type="hidden" name="revision" value="{{ $terna->revision }}">
            <label for="committeeStatus" class="form-label">Estado</label>
            <select id="committeeStatus" name="status" class="form-select mb-3">
                @foreach (\App\Models\AcademicCommittee::STATUSES as $estado)
                    <option @selected(old('status', $terna->status) === $estado)>{{ $estado }}</option>
                @endforeach
            </select>
            @for ($slot = 0; $slot < 3; $slot++)
                @php($member = $terna->members->values()->get($slot))
                <div class="mb-3">
                    <label class="form-check-label">
                        <input type="checkbox" class="form-check-input" data-member-toggle="committeeMember{{ $slot }}" @checked(old('members.'.$slot.'.user_id', $member?->user_id))>
                        Incluir integrante {{ $slot + 1 }}
                    </label>
                    <fieldset id="committeeMember{{ $slot }}" @disabled(!old('members.'.$slot.'.user_id', $member?->user_id))>
                        <label for="person{{ $slot }}" class="form-label">Persona</label>
                        <select id="person{{ $slot }}" name="members[{{ $slot }}][user_id]" class="form-select mb-2" required>
                            <option value="">Seleccionar persona</option>
                            @foreach ($personas as $persona)
                                <option value="{{ $persona->id }}" @selected(old('members.'.$slot.'.user_id', $member?->user_id) == $persona->id)>{{ $persona->name }}</option>
                            @endforeach
                        </select>
                        <label for="function{{ $slot }}" class="form-label">Función dentro de esta terna</label>
                        <select id="function{{ $slot }}" name="members[{{ $slot }}][function]" class="form-select">
                            @foreach (['Miembro', 'Coordinador'] as $function)
                                <option @selected(old('members.'.$slot.'.function', $member?->function) === $function)>{{ $function }}</option>
                            @endforeach
                        </select>
                    </fieldset>
                </div>
            @endfor
            <button class="btn btn-primary">Guardar asignación</button>
        </form>
    </section>
@endcan
@endsection
@push('scripts')
<script>
document.querySelectorAll('[data-member-toggle]').forEach(toggle => {
    toggle.addEventListener('change', () => {
        document.getElementById(toggle.dataset.memberToggle).disabled = !toggle.checked;
    });
});
</script>
@endpush
