@extends($teacherPortal ? 'layouts.teacher' : 'layouts.app')
@section('title', 'Detalle de seguimiento | UNICAH')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">ACOMPAÑAMIENTO ACADÉMICO</span><h1>{{ $seguimiento->title }}</h1><p>{{ $seguimiento->practice->student->user->name }} · {{ $seguimiento->teacher->user->name }}</p></div><a href="{{ route($teacherPortal ? 'docente.seguimientos' : 'coordinacion.seguimientos') }}" class="btn btn-light">Volver</a></div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="content-card">
    @can('update', $seguimiento)
        <form method="POST" action="{{ route('docente.seguimiento.update', $seguimiento) }}" class="p-3">@csrf @method('PUT')<input type="hidden" name="revision" value="{{ $seguimiento->revision }}">@include('academic.followup-fields')<button class="btn btn-primary">Guardar cambios</button></form>
    @else
        <div class="coord-request-data-grid"><div><span>Fecha</span><strong>{{ $seguimiento->date->format('d/m/Y') }}</strong></div><div><span>Tipo</span><strong>{{ $seguimiento->type }}</strong></div><div><span>Estado</span><strong>{{ $seguimiento->status }}</strong></div></div><div class="coord-request-observation"><p>{{ $seguimiento->description }}</p><p>{{ $seguimiento->observation }}</p></div>
    @endcan
</section>
@endsection
