<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>@yield('title', 'Sistema de Prácticas Profesionales')</title>

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- CSS general -->
    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >
</head>


<body>

<div class="app-container">

    <!-- ======================================
         SIDEBAR
    ======================================= -->

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">

            <img
                src="{{ asset('assets/img/logo-unicah.png') }}"
                alt="UNICAH"
                class="sidebar-logo"
            >

            <button
                type="button"
                class="sidebar-close"
                id="sidebarClose"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>


        <div class="sidebar-title">

            <span>Sistema de</span>

            <strong>
                Prácticas Profesionales
            </strong>

        </div>


        <nav class="sidebar-nav">

            <a
                href="{{ route('coordinacion.dashboard') }}"
                class="nav-item {{ request()->routeIs('coordinacion.dashboard') ? 'active' : '' }}"
            >
                <i class="bi bi-grid-1x2-fill"></i>

                <span>
                    Dashboard
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'solicitudes') }}"
                class="nav-item"
            >
                <i class="bi bi-file-earmark-text"></i>

                <span>
                    Solicitudes
                </span>

                <span class="nav-badge">
                    12
                </span>
            </a>


            <a
                href="{{ route('coordinacion.estudiantes') }}"
                class="nav-item {{ request()->routeIs('coordinacion.estudiantes') ? 'active' : '' }}"
            >
                <i class="bi bi-people"></i>

                <span>
                    Estudiantes
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'practicas') }}"
                class="nav-item"
            >
                <i class="bi bi-briefcase"></i>

                <span>
                    Prácticas
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'empresas') }}"
                class="nav-item"
            >
                <i class="bi bi-buildings"></i>

                <span>
                    Empresas
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'documentos') }}"
                class="nav-item"
            >
                <i class="bi bi-folder-check"></i>

                <span>
                    Documentos
                </span>

                <span class="nav-badge">
                    7
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'ternas') }}"
                class="nav-item"
            >
                <i class="bi bi-people-fill"></i>

                <span>
                    Ternas
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'visitas') }}"
                class="nav-item"
            >
                <i class="bi bi-calendar-check"></i>

                <span>
                    Visitas
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'monografias') }}"
                class="nav-item"
            >
                <i class="bi bi-journal-text"></i>

                <span>
                    Monografías
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'defensas') }}"
                class="nav-item"
            >
                <i class="bi bi-mortarboard"></i>

                <span>
                    Defensas
                </span>
            </a>


            <a
                href="{{ route('coordinacion.modulo', 'reportes') }}"
                class="nav-item"
            >
                <i class="bi bi-bar-chart"></i>

                <span>
                    Reportes
                </span>
            </a>

        </nav>


        <div class="sidebar-footer">

            <div class="sidebar-user">

                <div class="user-avatar">
                    CA
                </div>

                <div>
                    <strong>
                        Coordinación
                    </strong>

                    <span>
                        Académica
                    </span>
                </div>

            </div>

        </div>

    </aside>



    <!-- OVERLAY PARA MÓVIL -->

    <div
        class="sidebar-overlay"
        id="sidebarOverlay"
    ></div>



    <!-- ======================================
         CONTENIDO PRINCIPAL
    ======================================= -->

    <div class="main-wrapper">


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="menu-button"
                    id="menuButton"
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

                    <div class="user-avatar small">
                        CA
                    </div>


                    <div class="topbar-user-info">

                        <strong>
                            Coordinación Académica
                        </strong>

                        <span>
                            COORDINACION
                        </span>

                    </div>


                    <i class="bi bi-chevron-down"></i>

                </div>

            </div>

        </header>



        <!-- ======================================
             CONTENIDO DE CADA PÁGINA
        ======================================= -->

        <main class="main-content">

            @yield('content')

        </main>

    </div>

</div>



<script>

    const sidebar =
        document.getElementById('sidebar');

    const menuButton =
        document.getElementById('menuButton');

    const sidebarClose =
        document.getElementById('sidebarClose');

    const sidebarOverlay =
        document.getElementById('sidebarOverlay');


    function openSidebar() {

        sidebar.classList.add('open');

        sidebarOverlay.classList.add('show');

    }


    function closeSidebar() {

        sidebar.classList.remove('open');

        sidebarOverlay.classList.remove('show');

    }


    menuButton.addEventListener(
        'click',
        openSidebar
    );


    sidebarClose.addEventListener(
        'click',
        closeSidebar
    );


    sidebarOverlay.addEventListener(
        'click',
        closeSidebar
    );

</script>


@stack('scripts')

</body>

</html>