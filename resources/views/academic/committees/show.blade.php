@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : 'layouts.app'))
@section('title', 'Terna | UNICAH')
@section('content')
@php($manager = auth()->user()->can('update', $terna))
<div class="page-header">
<div>
<h1>Terna de defensa</h1>
<p>{{ $terna->practice->student->user->name }} · {{ $originLabel }}</p>
</div>
</div>
<p>
<a class="btn btn-light coord-back-button" href="{{ route($manager ? 'coordinacion.ternas' : (auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante.terna' : 'docente.ternas')) }}">Volver a ternas</a>
</p>
@include('academic.closing-feedback')
<section class="defense-overview-card">
<div class="defense-overview-content">
<span class="section-label">ESTADO DE LA TERNA</span>
<h2>{{ $terna->status }}</h2>
<p>Período de origen: {{ $originLabel }}</p>
@if($terna->isClosed())<p>Cierre: {{ $terna->closed_at?->format('d/m/Y H:i') }} · {{ $terna->closure_reason }}</p>
@endif</div>
</section>
<section class="content-card mb-4">
<div class="card-header">
<h2>Integrantes actuales</h2>
</div>@include('academic.committees.members', ['members'=>$terna->members])</section>

@if($manager && !$terna->isClosed())
<section class="content-card mb-4">
<div class="card-header">
<div>
<h2>Asignación manual</h2>
<p>Coordinación Académica asigna tres docentes distintos. El coordinador de terna será el docente de seguimiento.</p>
</div>
</div>

@if($catalog->isEmpty())<div class="alert alert-info m-3">No hay docentes activos y aprobados con facultad/área confirmada. Los registros históricos no se reclasifican automáticamente.</div>
@endif
<form method="POST" action="{{ route('coordinacion.terna.update', $terna) }}" class="p-3" id="committeeAssignment">@csrf @method('PUT')<input type="hidden" name="revision" value="{{ $terna->revision }}">
<input type="hidden" name="status" value="Activa">
<div class="row g-3">
@foreach(\App\Models\AcademicCommittee::FUNCTIONS as $slot=>$function)
@php($member = $terna->members->firstWhere('function', $function))
<div class="col-lg-4" data-committee-slot>
<h3 class="h5">{{ $function }}</h3>
<input type="hidden" name="members[{{ $slot }}][function]" value="{{ $function }}">
<label for="area{{ $slot }}" class="form-label">Facultad/Área</label>
<select class="form-select mb-2" id="area{{ $slot }}" name="members[{{ $slot }}][academic_career_id]" data-committee-area required>
<option value="">Seleccione facultad o área</option>
@foreach($faculties as $faculty)<option value="{{ $faculty->id }}" @selected(old('members.'.$slot.'.academic_career_id', $member?->academic_career_id) == $faculty->id)>{{ $faculty->name }}</option>
@endforeach</select>
<label for="teacher{{ $slot }}" class="form-label">Docente</label>
<select class="form-select" id="teacher{{ $slot }}" name="members[{{ $slot }}][user_id]" data-committee-teacher data-selected="{{ old('members.'.$slot.'.user_id', $member?->user_id) }}" disabled required>
<option value="">Seleccione primero facultad o área</option>
</select>
<p class="small mt-2" data-committee-load aria-live="polite">
</p>
</div>

@endforeach</div>
<label for="assignmentReason" class="form-label mt-3">Motivo de cambio de integrantes, funciones o docente de seguimiento</label>
<textarea id="assignmentReason" class="form-control mb-3" name="reason" maxlength="5000">{{ old('reason') }}</textarea>
<button class="btn btn-primary" @disabled($catalog->count() < 3)>Guardar asignación</button>
</form>
</section>

@if($terna->defense?->status === 'Realizada')
<section class="content-card mb-4 p-3">
<h2 class="h4">Resultado y cierre formal</h2>
<p>Defensa realizada · Ronda {{ $terna->defense->scheduling_round }} · {{ $terna->defense->result ?? 'Resultado pendiente' }}</p>
<form method="POST" action="{{ route('coordinacion.terna.gestion', $terna) }}">@csrf<input type="hidden" name="revision" value="{{ $terna->revision }}">
<input type="hidden" name="round" value="{{ $terna->defense->scheduling_round }}">

@if(!$terna->defense->result)<input type="hidden" name="action" value="result">
<label for="defenseResult" class="form-label">Resultado de defensa</label>
<select id="defenseResult" name="result" class="form-select mb-3" required>
<option value="" selected disabled>Seleccione un resultado</option>
<option>Aprobada</option>
<option>Reprobada</option>
</select>


@elseif($terna->defense->result === 'Reprobada')<input type="hidden" name="action" value="retry">
<p>Se conserva esta misma terna y su carga activa para una nueva ronda.</p>

@else<input type="hidden" name="action" value="close">
<label class="form-check mb-3">
<input class="form-check-input" name="final_records_verified" type="checkbox" value="1" required> Confirmo que la evaluación/documentación final de defensa fue registrada cuando corresponde.</label>
@endif
<label for="lifecycleReason" class="form-label">Motivo y referencia de verificación</label>
<textarea id="lifecycleReason" name="reason" class="form-control mb-3" maxlength="5000" required>
</textarea>
<button class="btn btn-primary">{{ !$terna->defense->result ? 'Registrar resultado' : ($terna->defense->result === 'Reprobada' ? 'Preparar nueva ronda con la misma terna' : 'Cerrar formalmente la terna') }}</button>
</form>
</section>

@endif

@endif
<section class="content-card mb-4">
<div class="card-header">
<h2>Historial de integrantes</h2>
</div>
<div class="table-responsive">
<table class="data-table">
<thead>
<tr>
<th>Docente</th>
<th>Función</th>
<th>Facultad/Área</th>
<th>Desde</th>
<th>Hasta</th>
</tr>
</thead>
<tbody>
@forelse($terna->membershipHistory as $member)<tr>
<td>{{ $member->user->name }}</td>
<td>{{ $member->function }}</td>
<td>{{ $member->faculty?->name ?? 'Sin clasificación confirmada' }}</td>
<td>{{ $member->started_at?->format('d/m/Y H:i') }}</td>
<td>{{ $member->ended_at?->format('d/m/Y H:i') ?? ($terna->isClosed() ? 'Conservado al cierre' : 'Vigente') }}</td>
</tr>
@empty<tr>
<td colspan="5">Sin asignaciones registradas.</td>
</tr>
@endforelse</tbody>
</table>
</div>
</section>
<section class="content-card p-3">
<h2 class="h4">Historial de cambios</h2>
@foreach($terna->events->sortBy('id') as $event)<article class="border-bottom py-3">
<h3 class="h6">{{ $event->action }}</h3>
<p>{{ $event->occurred_at?->format('d/m/Y H:i') }} · {{ $event->actor?->name ?? 'Registro técnico conservado' }}</p>
<p>{{ $event->reason }}</p>
@foreach(['before'=>'Antes','after'=>'Después'] as $key=>$label)
@if(isset($event->$key['members']))<div>
<strong>{{ $label }}:</strong>
@foreach($event->$key['members'] as $entry)<div>{{ $entry['function'] }} — {{ $entry['name'] ?? $terna->membershipHistory->firstWhere('user_id', $entry['user_id'])?->user->name ?? 'Usuario #'.$entry['user_id'] }}
@if($entry['faculty'] ?? null) · {{ $entry['faculty'] }}
@endif</div>
@endforeach</div>
@endif
@endforeach</article>
@endforeach</section>

@endsection

@if($manager && !$terna->isClosed())

@push('scripts')
<script type="application/json" id="committeeTeacherCatalog">@json($catalog)</script>
<script type="application/json" id="committeeOriginPeriod">@json($originLabel)</script>
<script src="{{ asset('js/committee-assignment.js') }}" defer>
</script>

@endpush

@endif
