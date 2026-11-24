@extends('layouts.public')
@section('content')
<section class="content-card p-3 p-md-4">
<h1 class="h3">Solicitud de acceso #{{ $application->id }}</h1>
<p><strong>{{ $application->full_name }}</strong> · {{ $application->career->name }}</p>
<p>Estado: <strong>{{ $application->statusLabel() }}</strong></p>
<p>Conserva este enlace privado. No lo compartas: permite consultar tu solicitud y enviar las correcciones que CA solicite.</p>
@if($application->status === 'APPROVED')<div class="alert alert-success">Tu cuenta está habilitada. Ingresa con tu DNI y la contraseña registrada.</div><a class="btn btn-primary" href="{{ route('login') }}">Ingresar</a>@endif
@if($application->rejection_reason)<div class="alert alert-warning">{{ $application->rejection_reason }}</div>@endif
@if($application->isStudent())
<h2 class="h5 mt-4">Asignaturas declaradas</h2><ul>@forelse($application->subjects as $subject)<li>{{ $subject->name }}</li>@empty<li>No declaró asignaturas pendientes para cursar durante la práctica.</li>@endforelse</ul>
<h2 class="h5">Documentos recibidos</h2><ul>@foreach($application->documents as $document)<li>{{ \App\Models\AcademicDocument::ACCESS_TYPES[$document->type] }} · {{ $document->currentVersion?->status }} @foreach($document->currentVersion?->files ?? [] as $index => $file)<a href="{{ route('registro.archivo', [$application, $token, $document->currentVersion, $index]) }}">{{ $file['name'] }}</a> @endforeach</li>@endforeach</ul>
@endif
</section>
@endsection
