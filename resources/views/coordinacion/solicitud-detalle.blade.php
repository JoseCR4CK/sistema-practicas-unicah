@extends(($studentPortal ?? false) ? 'layouts.student' : 'layouts.app')
@section('title', 'Detalle de solicitud | UNICAH')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">SOLICITUD DE PRÁCTICA</span><h1>{{ $solicitud->student->user->name }}</h1><p>Revisa la información antes de tomar una decisión.</p></div><a href="{{ route(($studentPortal ?? false) ? 'estudiante.solicitudes' : 'coordinacion.solicitudes') }}" class="btn btn-light coord-back-button">Volver a solicitudes</a></div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="coord-request-detail-header mb-4">
    <div class="coord-request-detail-icon"><i class="bi bi-file-earmark-text"></i></div>
    <div><span>ESTADO DE LA SOLICITUD</span><strong>{{ $solicitud->status }}</strong><small>{{ $solicitud->submitted_at->format('d/m/Y H:i') }}</small></div>
    @if (!($studentPortal ?? false) && $solicitud->status === 'Pendiente')
        <div class="coord-request-detail-actions">
            <button class="btn coord-request-reject" data-bs-toggle="modal" data-bs-target="#rejectRequestModal">Rechazar</button>
            <form method="POST" action="{{ route('coordinacion.solicitud.review', $solicitud) }}">@csrf<input type="hidden" name="status" value="Aprobada"><button class="btn btn-success">Aprobar solicitud</button></form>
        </div>
    @endif
</section>
<section class="content-card mb-4">
    <div class="card-header"><div><h2>Información del estudiante</h2></div></div>
    <div class="coord-request-data-grid">
        <div><span>Nombre</span><strong>{{ $solicitud->student->user->name }}</strong></div>
        <div><span>Número de cuenta</span><strong>{{ $solicitud->student->account_number }}</strong></div>
        <div><span>Correo institucional</span><strong>{{ $solicitud->student->user->email }}</strong></div>
        <div><span>Carrera</span><strong>{{ $solicitud->student->career }}</strong></div>
    </div>
</section>
<section class="content-card mb-4">
    <div class="card-header"><div><h2>Información de la solicitud</h2></div></div>
    <div class="coord-request-data-grid">
        <div><span>Empresa propuesta</span><strong>{{ $solicitud->practice?->company?->official_name ?? 'Sin empresa asignada' }}</strong></div>
        <div><span>Fecha de solicitud</span><strong>{{ $solicitud->submitted_at->format('d/m/Y') }}</strong></div>
        <div><span>Inicio propuesto</span><strong>{{ $solicitud->practice?->proposed_start_date?->format('d/m/Y') ?? 'Sin fecha propuesta' }}</strong></div>
    </div>
    <div class="coord-request-observation"><span>Observación del estudiante</span><p>{{ $solicitud->student_observation ?? 'Sin observaciones' }}</p></div>
</section>
<div class="coord-request-info-note"><i class="bi bi-info-circle"></i><div><strong>Alcance de la aprobación</strong><span>Aprobar esta solicitud autoriza la continuación del proceso.</span></div></div>
<section class="content-card mt-4">
    <div class="card-header"><div><h2>Revisión de la solicitud</h2></div></div>
    <div class="coord-request-data-grid">
        <div><span>Revisado por</span><strong>{{ $solicitud->reviewer?->name ?? 'Pendiente' }}</strong></div>
        <div><span>Fecha de revisión</span><strong>{{ $solicitud->reviewed_at?->format('d/m/Y H:i') ?? 'Pendiente' }}</strong></div>
        <div><span>Reinicio del plazo</span><strong>{{ $solicitud->deadline_restarted_at?->format('d/m/Y H:i') ?? 'Sin reinicio registrado' }}</strong></div>
    </div>
    <div class="coord-request-observation"><p>{{ $solicitud->observation ?? 'Sin observaciones' }}</p></div>
</section>
@if (!($studentPortal ?? false))
<div class="modal fade" id="rejectRequestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content coord-company-modal" method="POST" action="{{ route('coordinacion.solicitud.review', $solicitud) }}">
            @csrf<input type="hidden" name="status" value="Rechazada">
            <div class="modal-header"><h5 class="modal-title">Indicar motivo</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body"><label class="form-label" for="requestObservation">Motivo del rechazo</label><textarea id="requestObservation" name="observation" class="form-control" rows="4" required maxlength="5000">{{ old('observation') }}</textarea></div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-danger">Rechazar solicitud</button></div>
        </form>
    </div>
</div>
@endif
@endsection
