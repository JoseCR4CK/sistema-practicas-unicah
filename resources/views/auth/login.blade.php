<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Prácticas Profesionales | UNICAH
    </title>

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- CSS del Login -->
    <link
        rel="stylesheet"
        href="{{ asset('css/login.css') }}"
    >
</head>


<body>

<div class="login-page">


    <!-- ==========================================
         PANEL INSTITUCIONAL
    =========================================== -->

    <section class="institution-panel">

        <div class="institution-overlay"></div>


        <div class="institution-content">

            <img
                src="{{ asset('assets/img/logo-unicah.png') }}"
                alt="Universidad Católica de Honduras"
                class="unicah-logo"
            >


            <div class="gold-line"></div>


            <h1>
                Sistema de Gestión y
                Seguimiento de

                <span>
                    Prácticas Profesionales
                </span>
            </h1>


            <div class="small-gold-line"></div>


            <p>
                Formación integral al servicio
                de la sociedad
            </p>

        </div>

    </section>



    <!-- ==========================================
         PANEL DE LOGIN
    =========================================== -->

    <section class="login-section">


        <div class="login-card">


            <header class="login-header">

                <h2>
                    Bienvenido
                </h2>

                <p>
                    Inicia sesión para acceder al
                    Sistema de Gestión de Prácticas
                    Profesionales.
                </p>

            </header>



            <form>

                <!-- USUARIO -->

                <div class="form-group">

                    <label for="usuario">
                        Usuario o correo institucional
                    </label>


                    <div class="input-wrapper">

                        <i class="bi bi-person input-icon"></i>


                        <input
                            type="text"
                            id="usuario"
                            name="usuario"
                            placeholder="Ingresa tu usuario o correo"
                            autocomplete="username"
                        >

                    </div>

                </div>



                <!-- CONTRASEÑA -->

                <div class="form-group">

                    <label for="password">
                        Contraseña
                    </label>


                    <div class="input-wrapper">

                        <i class="bi bi-lock input-icon"></i>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="Mostrar contraseña"
                        >

                            <i
                                class="bi bi-eye-slash"
                                id="passwordIcon"
                            ></i>

                        </button>

                    </div>

                </div>



                <!-- BOTÓN LOGIN -->

                <button
                    type="submit"
                    class="btn-login"
                >

                    <span>
                        Ingresar
                    </span>

                    <i class="bi bi-arrow-right"></i>

                </button>



                <!-- OPCIONES -->

                <div class="login-options">


                    <label class="remember-option">

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        <span>
                            Recordar sesión
                        </span>

                    </label>


                    <button
                        type="button"
                        class="forgot-password"
                        onclick="alert('La recuperación de contraseña se integrará en la siguiente etapa del proyecto.')"
                    >
                        ¿Olvidaste tu contraseña?
                    </button>


                </div>


            </form>



            <!-- FOOTER -->

            <footer class="login-footer">

                <img
                    src="{{ asset('assets/img/logo-unicah.png') }}"
                    alt="UNICAH"
                >

            </footer>


        </div>

    </section>

</div>



<script>

    const passwordInput =
        document.getElementById('password');

    const passwordToggle =
        document.getElementById('passwordToggle');

    const passwordIcon =
        document.getElementById('passwordIcon');


    passwordToggle.addEventListener('click', function () {

        const hidden =
            passwordInput.type === 'password';


        passwordInput.type =
            hidden ? 'text' : 'password';


        passwordIcon.classList.toggle(
            'bi-eye-slash',
            !hidden
        );

        passwordIcon.classList.toggle(
            'bi-eye',
            hidden
        );

    });

</script>


</body>

</html>