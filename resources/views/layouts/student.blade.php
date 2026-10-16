<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Sistema de Prácticas Profesionales')
    </title>


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


    <!-- CSS general -->
    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}"
    >

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
                id="sidebarClose"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>


        <div class="sidebar-title">

            <span>
                Portal del estudiante
            </span>

            <strong>
                Prácticas Profesionales
            </strong>

        </div>


        <nav class="sidebar-nav">


            <a
                href="{{ route('estudiante.dashboard') }}"
                class="nav-item {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}"
            >

                <i class="bi bi-house-door-fill"></i>

                <span>
                    Inicio
                </span>

            </a>


            <a
                href="{{ route('estudiante.practica') }}"
                class="nav-item {{ request()->routeIs('estudiante.practica') ? 'active' : '' }}"
            >
                <i class="bi bi-briefcase"></i>

                <span>
                    Mi práctica
                </span>
            </a>


            <a
                href="{{ route('estudiante.documentos') }}"
                class="nav-item {{ request()->routeIs('estudiante.documentos') ? 'active' : '' }}"
            >
                <i class="bi bi-folder-check"></i>

                <span>
                    Documentos
                </span>

                <span class="nav-badge">
                    1
                </span>
            </a>


            <a
                href="{{ route('estudiante.empresa') }}"
                class="nav-item {{ request()->routeIs('estudiante.empresa') ? 'active' : '' }}"
            >
                <i class="bi bi-buildings"></i>

                <span>
                    Empresa
                </span>
            </a>


            <a
                href="{{ route('estudiante.visita') }}"
                class="nav-item {{ request()->routeIs('estudiante.visita') ? 'active' : '' }}"
            >
                <i class="bi bi-calendar-check"></i>

                <span>
                    Visita
                </span>
            </a>


            <a
                href="{{ route('estudiante.monografia') }}"
                class="nav-item {{ request()->routeIs('estudiante.monografia') ? 'active' : '' }}"
            >
                <i class="bi bi-journal-text"></i>

                <span>
                    Monografía
                </span>
            </a>


            <a
                href="{{ route('estudiante.defensa') }}"
                class="nav-item {{ request()->routeIs('estudiante.defensa') ? 'active' : '' }}"
            >
                <i class="bi bi-mortarboard"></i>

                <span>
                    Defensa
                </span>
            </a>


            <a
                href="{{ route('estudiante.modulo', 'historial') }}"
                class="nav-item"
            >

                <i class="bi bi-clock-history"></i>

                <span>
                    Historial
                </span>

            </a>


            <a
                href="{{ route('estudiante.modulo', 'perfil') }}"
                class="nav-item"
            >

                <i class="bi bi-person"></i>

                <span>
                    Mi perfil
                </span>

            </a>


        </nav>


        <div class="sidebar-footer">

            <div class="sidebar-user">

                <div class="user-avatar">
                    AM
                </div>

                <div>

                    <strong>
                        Andrea Martínez
                    </strong>

                    <span>
                        Estudiante
                    </span>

                </div>

            </div>

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
                        AM
                    </div>


                    <div class="topbar-user-info">

                        <strong>
                            Andrea Martínez
                        </strong>

                        <span>
                            ESTUDIANTE
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

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>