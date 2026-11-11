@extends($teacherPortal ? 'layouts.teacher' : 'layouts.app')
@section('title', 'Seguimientos | UNICAH')
@section('content')
<div class="page-header"><div><span class="student-home-eyebrow">ACOMPAÑAMIENTO ACADÉMICO</span><h1>Seguimientos</h1><p>Consulta las actividades de acompañamiento académico de las prácticas.</p></div>@if ($teacherPortal)<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#followupModal"><i class="bi bi-plus-lg"></i> Nuevo seguimiento</button>@endif</div>
@if (session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="row g-3 mb-4">
    @foreach (['Total' => $seguimientos->count(), 'Realizados' => $seguimientos->where('status', 'Realizado')->count(), 'Pendientes' => $seguimientos->where('status', 'Pendiente')->count(), 'Practicantes' => $seguimientos->pluck('practice.student_id')->unique()->count()] as $etiqueta => $cantidad)
        <div class="col-12 col-md-3"><div class="teacher-load-stat"><div class="teacher-load-stat-icon"><i class="bi bi-clipboard-check"></i></div><div><span>{{ $etiqueta }}</span><strong>{{ $cantidad }}</strong></div></div></div>
    @endforeach
</section>
<section class="content-card">
    <div class="teacher-followup-toolbar"><div class="coord-company-search"><i class="bi bi-search"></i><input type="search" id="flowSearch" placeholder="Buscar practicante o seguimiento..."></div><select id="flowCareer" class="form-select"><option value="">Todos los practicantes</option>@foreach ($seguimientos->pluck('practice.student.user.name')->unique()->sort() as $nombre)<option>{{ $nombre }}</option>@endforeach</select><select id="flowStatus" class="form-select"><option value="">Todos los estados</option>@foreach ($seguimientos->pluck('status')->unique()->sort() as $estado)<option>{{ $estado }}</option>@endforeach</select></div>
    @forelse ($seguimientos as $seguimiento)
        <article class="teacher-followup-row" data-flow-row data-status="{{ $seguimiento->status }}" data-career="{{ $seguimiento->practice->student->user->name }}">
            <div class="coord-request-avatar">{{ Str::initials($seguimiento->practice->student->user->name, true) }}</div>
            <div class="teacher-followup-student"><span>PRACTICANTE</span><strong>{{ $seguimiento->practice->student->user->name }}</strong><small>{{ $seguimiento->practice->student->account_number }}</small></div>
            <div><span class="coord-request-label">Actividad</span><strong class="coord-request-value">{{ $seguimiento->title }}</strong><small class="teacher-followup-type">{{ $seguimiento->type }}</small></div>
            <div><span class="coord-request-label">Fecha</span><strong class="coord-request-value">{{ $seguimiento->date->format('d/m/Y') }}</strong></div>
            <span class="teacher-followup-status {{ $seguimiento->status === 'Realizado' ? 'completed' : 'pending' }}">{{ $seguimiento->status }}</span>
            <div class="teacher-followup-actions">
                @can('update', $seguimiento)
                    @if ($seguimiento->status !== 'Realizado')
                        <form method="POST" action="{{ route('docente.seguimiento.status', $seguimiento) }}">@csrf @method('PATCH')<input type="hidden" name="revision" value="{{ $seguimiento->revision }}"><input type="hidden" name="status" value="Realizado"><button title="Marcar como realizado"><i class="bi bi-check-lg"></i></button></form>
                    @endif
                    <a href="{{ route('docente.seguimiento', $seguimiento) }}" class="coord-teacher-view" title="Editar seguimiento"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('docente.seguimiento.destroy', $seguimiento) }}" onsubmit="return confirm('¿Eliminar este seguimiento de la lista?')">@csrf @method('DELETE')<input type="hidden" name="revision" value="{{ $seguimiento->revision }}"><button title="Eliminar seguimiento"><i class="bi bi-trash"></i></button></form>
                @else
                    <a href="{{ route($teacherPortal ? 'docente.seguimiento' : 'coordinacion.seguimiento', $seguimiento) }}" class="coord-teacher-view">Ver</a>
                @endcan
            </div>
        </article>
        <div class="teacher-followup-observation"><i class="bi bi-chat-left-text"></i>{{ $seguimiento->description }} {{ $seguimiento->observation }}</div>
    @empty
        <div class="teacher-load-empty">No hay seguimientos registrados.</div>
    @endforelse
</section>
@if ($teacherPortal)
<div class="modal fade" id="followupModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><form class="modal-content" method="POST" action="{{ route('docente.seguimientos.store') }}">@csrf<input type="hidden" name="submission_key" value="{{ Str::uuid() }}"><div class="modal-header"><h5 class="modal-title">Nuevo seguimiento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body">
    <label for="followupPractice" class="form-label">Practicante</label><select id="followupPractice" name="practice_id" class="form-select mb-3" required>@foreach ($practicas as $practica)<option value="{{ $practica->id }}" @selected(old('practice_id') == $practica->id)>{{ $practica->student->user->name }} · {{ $practica->academic_period }} {{ $practica->academic_year }}</option>@endforeach</select>
    @include('academic.followup-fields', ['seguimiento' => null])
</div><div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-primary" @disabled($practicas->isEmpty())>Guardar seguimiento</button></div></form></div></div>
@endif
@endsection
@push('scripts')<script src="{{ asset('js/academic-filters.js') }}"></script>@endpush
