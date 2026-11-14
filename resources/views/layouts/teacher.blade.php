<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Portal del Docente | UNICAH')
    </title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >

<link rel="stylesheet" href="{{ asset('css/presentation.css') }}">
</head>


<body>


<div class="app-container">


    <!-- SIDEBAR -->

    <aside
        class="sidebar"
        id="sidebar"
    >


        <div class="sidebar-header teacher-sidebar-header">
            <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Cerrar menú"><i class="bi bi-x-lg"></i></button>

            <img
                src="{{ asset('assets/img/logo-unicah.png') }}"
                alt="UNICAH"
                class="sidebar-logo teacher-sidebar-logo"
            >

            <div class="teacher-sidebar-brand">

                <span>
                    Portal del docente
                </span>

                <strong>
                    Prácticas Profesionales
                </strong>

            </div>

        </div>



        <nav class="sidebar-nav">


            <div class="nav-section-label">
                PRINCIPAL
            </div>


            <a
                href="{{ route('docente.dashboard') }}"
                class="nav-item {{ request()->routeIs('docente.dashboard') ? 'active' : '' }}"
            >

                <i class="bi bi-house-door-fill"></i>

                <span>
                    Inicio
                </span>

            </a>


            <a
                href="{{ route('docente.practicantes') }}"
                class="nav-item {{ request()->routeIs('docente.practicantes', 'docente.practicante') ? 'active' : '' }}"
            >
                <i class="bi bi-people"></i>

                <span>
                    Mis practicantes
                </span>
            </a>


            <div class="nav-section-label">
                SEGUIMIENTO
            </div>


            <a
                href="{{ route('docente.visitas') }}"
                class="nav-item {{ request()->routeIs('docente.visitas', 'docente.visita') ? 'active' : '' }}"
            >
                <i class="bi bi-calendar-check"></i>
                <span>Visitas</span>
            </a>


            <a
                href="{{ route('docente.seguimientos') }}"
                class="nav-item {{ request()->routeIs('docente.seguimientos') ? 'active' : '' }}"
            >
                <i class="bi bi-clipboard-check"></i>

                <span>
                    Seguimientos
                </span>
            </a>


            <a
                href="{{ route('docente.alertas') }}"
                class="nav-item {{ request()->routeIs('docente.alertas') ? 'active' : '' }}"
            >
                <i class="bi bi-bell"></i>
                <span>Alertas</span>
            </a>

            <div class="sidebar-section-label">
                ACADÉMICO
            </div>

            <a href="{{ route('docente.monografias') }}" class="nav-item {{ request()->routeIs('docente.monografias', 'docente.monografia.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i><span>Monografías</span></a>

            <a href="{{ route('docente.ternas') }}" class="nav-item {{ request()->routeIs('docente.ternas', 'docente.terna.show') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Ternas</span></a>

            <a href="{{ route('docente.defensas') }}" class="nav-item {{ request()->routeIs('docente.defensas', 'docente.defensa.*') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i><span>Defensas</span></a>

            <a
                href="{{ route('docente.carga-academica') }}"
                class="nav-item {{ request()->routeIs('docente.carga-academica') ? 'active' : '' }}"
            >
                <i class="bi bi-calendar-week"></i>
                <span>Carga académica</span>
            </a>


        </nav>

        <div class="sidebar-footer">

            <div class="sidebar-user">

                <div class="user-avatar">
                    {{ Str::initials(auth()->user()->name, true) }}
                </div>

                <div>

                    <strong>
                        {{ auth()->user()->name }}
                    </strong>

                    <span>
                        Docente
                    </span>

                </div>

            </div>

            <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
                @csrf

                <button type="submit" class="sidebar-logout-btn">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Cerrar sesión</span>
                </button>
            </form>

        </div>

    </aside>



    <!-- CONTENIDO -->

    <div class="main-wrapper">


        <header class="topbar">

            <div class="topbar-left">
                <button type="button" class="menu-button" id="menuButton" aria-label="Abrir menú" aria-controls="sidebar" aria-expanded="false"><i class="bi bi-list"></i></button>
                <div class="topbar-title">

                <span>
                    Sistema de Gestión y Seguimiento
                </span>

                <strong>
                    Prácticas Profesionales
                </strong>
                </div>
            </div>


            <div class="topbar-user">

                <button class="notification-button">

                    <i class="bi bi-bell"></i>

                    <span></span>

                </button>


                <div class="user-avatar small">
                    {{ Str::initials(auth()->user()->name, true) }}
                </div>


                <div class="topbar-user-info">

                    <strong>
                        {{ auth()->user()->name }}
                    </strong>

                    <span>
                        DOCENTE
                    </span>

                </div>


                <i class="bi bi-chevron-down"></i>

            </div>

        </header>



        <main class="main-content">

            @yield('content')

        </main>


    </div>

</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<script src="{{ asset('js/navigation.js') }}" defer></script>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>


@stack('scripts')


</body>
</html>
