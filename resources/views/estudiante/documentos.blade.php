@extends('layouts.student')
@section('title', 'Mis documentos | UNICAH')
@section('content')
<div class="page-header"><div><h1>Mis documentos</h1><p>Consulta el estado y las versiones de tus documentos.</p></div></div>
@if ($errors->any())<div class="alert alert-danger">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<section class="content-card mb-4">@include('academic.document-list', ['studentPortal' => true])</section>
<section class="content-card">
    <div class="card-header"><div><h2>Registrar documento o corrección</h2><p>Registra la información y versión correspondiente del documento.</p></div></div>
    <form method="POST" action="{{ route('estudiante.documentos.store') }}" class="p-3">
        @csrf
        <label for="documentPractice" class="form-label">Práctica</label>
        <select id="documentPractice" name="practice_id" class="form-select mb-3" required>@foreach ($practicas as $practica)<option value="{{ $practica->id }}" @selected(old('practice_id', $preferredPracticeId) == $practica->id)>{{ $practica->academic_period }} {{ $practica->academic_year }} · {{ $practica->stage }}</option>@endforeach</select>
        <label for="documentType" class="form-label">Tipo</label>
        <select id="documentType" name="type" class="form-select mb-3" required>@foreach (\App\Models\AcademicDocument::TYPES as $codigo => $tipo)<option value="{{ $codigo }}" @selected(old('type') === $codigo)>{{ $tipo }}</option>@endforeach</select>
        <label for="documentNote" class="form-label">Observación de la versión</label>
        <textarea id="documentNote" name="submission_note" class="form-control mb-3" maxlength="5000">{{ old('submission_note') }}</textarea>
        <button class="btn btn-primary" @disabled($practicas->isEmpty())>Registrar versión</button>
    </form>
</section>
@endsection
