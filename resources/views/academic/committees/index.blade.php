@extends(auth()->user()->hasRole('ESTUDIANTE') ? 'layouts.student' : (auth()->user()->hasRole('DOCENTE_SEGUIMIENTO') ? 'layouts.teacher' : 'layouts.app'))
@section('title', 'Ternas | UNICAH')
@section('content')
@php($portal = $manager ? 'coordinacion' : (auth()->user()->hasRole('ESTUDIANTE') ? 'estudiante' : 'docente'))
<div class="page-header">
<div>
<h1>Ternas</h1>
<p>Asignación por proceso de práctica y facultad.</p>
</div>
@if($manager)<a class="btn btn-light" href="{{ route('coordinacion.ternas.carga') }}">Carga de ternas por docente</a>
@endif</div>
@include('academic.closing-feedback')

@if($manager) @include('academic.committees.filters')
@endif
<section class="content-card">
<div class="table-container table-responsive">
<table class="data-table">
<thead>
<tr>
<th>Estudiante</th>
<th>Facultad</th>
<th>Período de origen</th>
<th>Estado</th>
<th>Integrantes</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>

@forelse($practicas as $practice)
<tr>
<td>
<div>{{ $practice->student->user->name }}</div>
<small>{{ $practice->student->account_number }}</small>
</td>
<td>{{ $practice->student->academicCareer?->name ?? $practice->student->career ?? 'Sin facultad registrada' }}</td>
<td>{{ $practice->committee?->originLabel() ?? $practice->academic_period.' '.$practice->academic_year }}</td>
<td>{{ $practice->committee?->status ?? 'Pendiente de asignación' }}</td>
<td>{{ $practice->committee?->members->count() ?? 0 }}</td>
<td>
@if($practice->committee)<a class="coord-teacher-view" href="{{ route($portal.'.terna.show', $practice->committee) }}">Ver terna</a>

@elseif($manager)<form method="POST" action="{{ route('coordinacion.ternas.store') }}">@csrf<input type="hidden" name="practice_id" value="{{ $practice->id }}">
<input type="hidden" name="revision" value="0">
<input type="hidden" name="status" value="Pendiente de asignación">
<button class="btn btn-primary btn-sm">Registrar terna</button>
</form>
@endif</td>
</tr>

@empty<tr>
<td colspan="6">No hay procesos para estos filtros.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</section>

@endsection
