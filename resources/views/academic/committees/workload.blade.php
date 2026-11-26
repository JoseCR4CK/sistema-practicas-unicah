@extends('layouts.app')
@section('title', 'Carga de ternas por docente | UNICAH')
@section('content')
<div class="page-header">
<div>
<h1>Carga de ternas por docente</h1>
<p>Coordinación Académica · Carga activa por período de origen, sin límite automático.</p>
</div>
<a class="btn btn-light" href="{{ route('coordinacion.ternas') }}">Volver a ternas</a>
</div>
@include('academic.committees.filters', ['workload' => true])
<section class="content-card">
<div class="table-container table-responsive">
<table class="data-table">
<thead>
<tr>
<th>Docente</th>
<th>Facultad/Área</th>
<th>Carga por período</th>
<th>Total activas</th>
<th>Historial</th>
</tr>
</thead>
<tbody>

@forelse($teachers as $teacher)
<tr>
<td>{{ $teacher->user->name }}</td>
<td>{{ $teacher->faculties->firstWhere('pivot.is_primary', true)?->name ?? $teacher->faculty ?? 'Sin clasificación confirmada' }}</td>
<td>
@forelse(($loads[$teacher->user_id]['periods'] ?? []) as $period => $count)<div>{{ $period }}: {{ $count }}</div>
@empty Sin ternas activas
@endforelse</td>
<td>{{ $loads[$teacher->user_id]['total'] ?? 0 }}</td>
<td>
@foreach($histories[$teacher->id] as $committee)<div>
<a href="{{ route('coordinacion.terna.show', $committee) }}">{{ $committee->practice->student->user->name }} · {{ $committee->originLabel() }} · {{ $committee->status }}</a>
</div>
@endforeach</td>
</tr>

@empty<tr>
<td colspan="5">No hay docentes para estos filtros.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</section>

@endsection
