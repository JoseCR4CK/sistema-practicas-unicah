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

</head>


<body>


<div class="app-container">


    <!-- SIDEBAR -->

    <aside
        class="sidebar"
        id="sidebar"
    >


        <div class="sidebar-header teacher-sidebar-header">

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
                    CM
                </div>

                <div>

                    <strong>
                        Msc. Carlos Martínez
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

            <div>

                <span>
                    Sistema de Gestión y Seguimiento
                </span>

                <strong>
                    Prácticas Profesionales
                </strong>

            </div>


            <div class="topbar-user">

                <button class="notification-button">

                    <i class="bi bi-bell"></i>

                    <span></span>

                </button>


                <div class="topbar-avatar">
                    CM
                </div>


                <div>

                    <strong>
                        Msc. Carlos Martínez
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


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>


@stack('scripts')


</body>
</html>