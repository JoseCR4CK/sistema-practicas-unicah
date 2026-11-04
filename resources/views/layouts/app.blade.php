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


    <!-- Bootstrap 5 -->
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
                href="{{ route('coordinacion.solicitudes') }}"
                class="nav-item {{ request()->routeIs('coordinacion.solicitudes', 'coordinacion.solicitud') ? 'active' : '' }}"
            >
                <i class="bi bi-file-earmark-text"></i>

                <span>
                    Solicitudes
                </span>

                <span class="nav-badge" id="sidebarPendingRequests">
                    0
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
                href="{{ route('coordinacion.docentes') }}"
                class="nav-item {{ request()->routeIs('coordinacion.docentes') ? 'active' : '' }}"
            >
                <i class="bi bi-person-video3"></i>

                <span>
                    Docentes
                </span>
            </a>

            <a
                href="{{ route('coordinacion.practicas') }}"
                class="nav-item {{ request()->routeIs('coordinacion.practicas', 'coordinacion.practica') ? 'active' : '' }}"
            >
                <i class="bi bi-briefcase"></i>
                <span>Prácticas</span>
            </a>


            <a
                href="{{ route('coordinacion.empresas') }}"
                class="nav-item {{ request()->routeIs('coordinacion.empresas', 'coordinacion.empresa') ? 'active' : '' }}"
            >
                <i class="bi bi-buildings"></i>
                <span>Empresas</span>
            </a>


            <a
                href="{{ route('coordinacion.documentos') }}"
                class="nav-item {{ request()->routeIs('coordinacion.documentos', 'coordinacion.documento') ? 'active' : '' }}"
            >
                <i class="bi bi-folder2-open"></i>

                <span>
                    Documentos
                </span>

                <span
                    class="nav-badge"
                    id="sidebarPendingDocuments"
                >
                    0
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
                href="{{ route('coordinacion.visitas') }}"
                class="nav-item {{ request()->routeIs('coordinacion.visitas', 'coordinacion.visita') ? 'active' : '' }}"
            >
                <i class="bi bi-calendar-check"></i>
                <span>Visitas</span>
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

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const badge =
        document.getElementById('sidebarPendingRequests');

    if (!badge) {
        return;
    }

    const stored =
        localStorage.getItem('unicah_coord_solicitudes_v1');

    if (!stored) {
        badge.textContent = '0';
        return;
    }

    try {

        const requests =
            JSON.parse(stored) || [];

        const pending =
            requests.filter(request =>
                request.estado === 'Pendiente'
            ).length;

        badge.textContent =
            pending;

        badge.style.display =
            pending > 0
                ? ''
                : 'none';

    } catch (error) {

        badge.textContent = '0';

    }

});
</script>

<script src="{{ asset('js/documents-store.js') }}"></script>

<script>

const documentsBadge =
    document.getElementById(
        'sidebarPendingDocuments'
    );

if (
    documentsBadge &&
    window.DocumentStore
) {

    const documents =
        DocumentStore.getAll();

    const attention =
        documents.filter(document =>
            document.estado === 'Pendiente' ||
            document.estado === 'Corrección requerida'
        ).length;

    documentsBadge.textContent =
        attention;

    documentsBadge.style.display =
        attention > 0
            ? 'inline-flex'
            : 'none';

}

</script>

</body>

</html>