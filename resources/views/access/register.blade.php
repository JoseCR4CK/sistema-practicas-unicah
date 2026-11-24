@extends('layouts.public')
@section('title', ($type === 'STUDENT' ? 'Registro de estudiante' : 'Registro de docente').' | UNICAH')
@section('content')
@php
    $isStudent = $type === 'STUDENT';
    $value = fn($key, $default = '') => old($key, $application?->$key ?? $default);
    $selectedSubjects = array_map('intval', old('subjects', $application?->subjects->pluck('id')->all() ?? []));
@endphp
<section class="content-card p-3 p-md-4">
<h1 class="h3">{{ $application ? 'Corregir solicitud de acceso' : ($isStudent ? 'Registro de estudiante' : 'Registro de docente') }}</h1>
<p>Envía tu solicitud para revisión de Coordinación Académica. La cuenta se habilitará únicamente después de su aprobación.</p>
@if($application)<div class="alert alert-warning"><strong>Correcciones solicitadas:</strong> {{ $application->correction_notes }}</div>@endif
<form method="POST" enctype="multipart/form-data" action="{{ $application ? route('registro.corregir', [$application, $token]) : route($isStudent ? 'registro.estudiante.store' : 'registro.docente.store') }}" data-access-registration>
@csrf
@if($application) @method('PUT')<input type="hidden" name="revision" value="{{ $application->revision }}"> @endif
<div class="row g-3">
    <div class="col-md-6"><label class="form-label" for="first_names">Nombres</label><input class="form-control" id="first_names" name="first_names" required maxlength="120" value="{{ $value('first_names') }}" autocomplete="given-name"></div>
    <div class="col-md-6"><label class="form-label" for="last_names">Apellidos</label><input class="form-control" id="last_names" name="last_names" required maxlength="120" value="{{ $value('last_names') }}" autocomplete="family-name"></div>
    <div class="col-md-6"><label class="form-label" for="dni">DNI</label><input class="form-control" id="dni" name="dni" required maxlength="40" value="{{ $value('dni') }}"></div>
    @if($isStudent)<div class="col-md-6"><label class="form-label" for="account_number">Número de cuenta</label><input class="form-control" id="account_number" name="account_number" required maxlength="60" value="{{ $value('account_number') }}"></div>@endif
    <div class="col-md-6"><label class="form-label" for="email">Correo electrónico</label><input class="form-control" id="email" name="email" type="email" required maxlength="255" value="{{ $value('email') }}" autocomplete="email"></div>
    <div class="col-md-6"><label class="form-label" for="phone">Teléfono</label><input class="form-control" id="phone" name="phone" type="tel" required maxlength="40" value="{{ $value('phone') }}" autocomplete="tel"></div>
    <div class="col-12"><label class="form-label" for="academic_career_id">{{ $isStudent ? 'Facultad / Carrera' : 'Facultad / Área' }}</label><select class="form-select" name="academic_career_id" id="academic_career_id" required><option value="">Seleccione una opción</option>@foreach($faculties as $faculty)<option value="{{ $faculty->id }}" @selected((int)$value('academic_career_id') === $faculty->id)>{{ $faculty->name }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label" for="password">{{ $application ? 'Nueva contraseña (opcional)' : 'Contraseña' }}</label><input class="form-control" id="password" name="password" type="password" minlength="8" maxlength="128" @required(!$application) autocomplete="new-password"><small class="text-muted">Mínimo 8 caracteres. No se muestra ni se conserva en texto plano.</small></div>
    <div class="col-md-6"><label class="form-label" for="password_confirmation">Confirmar contraseña</label><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" @required(!$application) autocomplete="new-password"></div>
    @if($isStudent)
    <div class="col-12"><label class="form-label" for="has_pending_subjects">¿Tiene asignaturas pendientes por cursar durante su Práctica Profesional?</label><select class="form-select" id="has_pending_subjects" name="has_pending_subjects" required><option value="0" @selected(!(bool)$value('has_pending_subjects', false))>No</option><option value="1" @selected((bool)$value('has_pending_subjects', false))>Sí</option></select></div>
    <div class="col-12" data-pending-selector><label class="form-label" for="subjects">Asignaturas pendientes</label><select class="form-select" id="subjects" name="subjects[]" multiple size="8" data-selected="{{ json_encode($selectedSubjects) }}" @disabled(!$value('academic_career_id'))>@foreach($faculties->firstWhere('id', (int)$value('academic_career_id'))?->subjects ?? [] as $subject)<option value="{{ $subject->id }}" @selected(in_array($subject->id, $selectedSubjects))>{{ $subject->code ? $subject->code.' · ' : '' }}{{ $subject->name }}</option>@endforeach</select><small class="text-muted">Puedes seleccionar cualquier cantidad. Coordinación Académica revisará tu declaración. En computadora utiliza Ctrl/Cmd para seleccionar varias.</small><p class="text-muted mb-0" data-subject-message aria-live="polite"></p></div>
    <div class="col-12"><h2 class="h5 mt-3">Documentación obligatoria</h2><p>PDF, JPG, JPEG o PNG; hasta 8 MB por archivo (sujeto al límite del servidor). Para documentos de ambos lados, adjunta un PDF que los contenga o dos imágenes. CA revisará su contenido.</p></div>
    @foreach(\App\Models\AcademicDocument::ACCESS_TYPES as $key => $label)
        @php($current = $application?->documents->firstWhere('type', $key)?->currentVersion)
        <div class="col-12"><label class="form-label" for="file_{{ $key }}">{{ $label }}</label>
        @if($current && $current->status !== \App\Models\DocumentVersion::CORRECTION)
            <p class="text-muted">Versión {{ $current->version }} · {{ $current->status }}. Se conserva el archivo recibido.</p>
        @else
            @if($current)<p class="text-warning">{{ $current->observation }}</p>@endif
            <input class="form-control" type="file" id="file_{{ $key }}" name="documents[{{ $key }}][]" accept=".pdf,.jpg,.jpeg,.png" multiple required>
        @endif
        </div>
    @endforeach
    <div class="col-12"><div class="alert alert-info">Puedes enviar la solicitud aunque no hayas asistido a inducción. CA verificará manualmente su lista física antes de aprobar. No debes adjuntar el instructivo de monografía.</div></div>
    @endif
    <div class="col-12"><button class="btn btn-primary" type="submit">{{ $application ? 'Enviar corrección' : 'Enviar solicitud de acceso' }}</button></div>
</div>
</form>
</section>
@endsection
@push('scripts')
<script type="application/json" id="accessCatalog">{!! \Illuminate\Support\Js::encode($faculties->map(fn($faculty) => ['id' => $faculty->id, 'subjects' => $faculty->subjects->map(fn($subject) => ['id' => $subject->id, 'name' => ($subject->code ? $subject->code.' · ' : '').$subject->name])->values()])->values()) !!}</script>
<script src="{{ asset('js/access-registration.js') }}" defer></script>
@endpush
