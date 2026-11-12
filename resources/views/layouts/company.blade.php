<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Mi Práctica Profesional')</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <!-- CSS propio -->
    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >
<link rel="stylesheet" href="{{ asset('css/presentation.css') }}">
</head>


<body>

<div class="app-container student-app">


    <!-- SIDEBAR -->

    <aside
        class="sidebar student-sidebar"
        id="sidebar"
    >

        <div class="sidebar-header">

            <img
                src="{{ asset('assets/img/logo-unicah.png') }}"
                alt="UNICAH"
                class="sidebar-logo"
            >

            <button
                type="button"
                class="sidebar-close"
                id="sidebarClose" aria-label="Cerrar menú"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>


        <div class="sidebar-title">

            <span>
                Portal del jefe inmediato
            </span>

            <strong>
                Prácticas Profesionales
            </strong>

        </div>


        <nav class="sidebar-nav">

            <a href="{{ route('jefe.dashboard') }}"
            class="nav-item {{ request()->routeIs('jefe.dashboard') ? 'active' : '' }}">

                <i class="bi bi-house-door-fill"></i>
                <span>Inicio</span>

            </a>

            <a href="{{ route('jefe.modulo', 'practicante') }}"
            class="nav-item">

                <i class="bi bi-person-badge"></i>
                <span>Practicante</span>

            </a>

            <a href="{{ route('jefe.modulo', 'empresa') }}"
            class="nav-item">

                <i class="bi bi-buildings"></i>
                <span>Empresa</span>

            </a>

            <a href="{{ route('jefe.modulo', 'seguimiento') }}"
            class="nav-item">

                <i class="bi bi-calendar-check"></i>
                <span>Seguimiento</span>

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
                        Jefe Inmediato
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



    <!-- OVERLAY MÓVIL -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>



    <!-- CONTENIDO -->

    <div class="main-wrapper">


        <header class="topbar">


            <div class="topbar-left">

                <button
                    type="button"
                    class="menu-button"
                    id="menuButton" aria-label="Abrir menú" aria-controls="sidebar" aria-expanded="false"
                >
                    <i class="bi bi-list"></i>
                </button>


                <div class="topbar-title">

                    <span>
                        Sistema de Gestión y Seguimiento
                    </span>

                    <strong>
                        Prácticas Profesionales
                    </strong>

                </div>

            </div>



            <div class="topbar-actions">


                <button
                    type="button"
                    class="notification-button"
                >

                    <i class="bi bi-bell"></i>

                    <span class="notification-dot"></span>

                </button>


                <div class="topbar-user">

                    <div class="user-avatar">
                        {{ Str::initials(auth()->user()->name, true) }}
                    </div>


                    <div class="topbar-user-info">

                        <strong>
                            {{ auth()->user()->name }}
                        </strong>

                        <span>
                            JEFE INMEDIATO
                        </span>

                    </div>


                    <i class="bi bi-chevron-down"></i>

                </div>

            </div>


        </header>



        <main class="main-content">

            @yield('content')

        </main>


    </div>

</div>



<script src="{{ asset('js/navigation.js') }}" defer></script>


@stack('scripts')

</body>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>

</html>
