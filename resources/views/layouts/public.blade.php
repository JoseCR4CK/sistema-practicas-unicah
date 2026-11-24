<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>@yield('title', 'Solicitudes de acceso | UNICAH')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="container py-4 text-break" style="max-width: 1000px">
    <header class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <a href="{{ route('login') }}"><img src="{{ asset('assets/img/logo-unicah.png') }}" width="150" alt="UNICAH"></a>
        <a class="btn btn-outline-primary" href="{{ route('login') }}">Volver al ingreso</a>
    </header>
    @if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
