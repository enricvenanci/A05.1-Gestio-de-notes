<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="auth-page">

        <div class="auth-brand">
            <img class="auth-brand__logo" src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
            <h2 class="auth-brand__title">Colegio Internacional San Miguel Arcángel</h2>
            <p class="auth-brand__text">Gestión de notas</p>
        </div>

        <section class="auth-card" aria-label="Inicio de sesión">
            <h1>Bienvenido de nuevo</h1>
            <p class="auth-card__intro">Accede a tu cuenta para continuar.</p>

            <form class="auth-form" method="post" action="proc/proc_login.php" aria-label="Formulario de inicio de sesión">
                <div class="field">
                    <label for="username">Nombre</label>
                    <input class="input" type="text" id="username" name="username" onblur="comprobarUsuario()">
                    <span id="error-username"></span>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input class="input" type="password" id="password" name="password" onblur="comprobarPassword()">
                    <span id="error-password"></span>
                </div>

                <button class="btn-primary btn-block" type="submit" onclick="return validarLogin()">Iniciar sesión <span aria-hidden="true">→</span></button>
            </form>

            <p class="auth-card__foot">
                ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
            </p>
        </section>

    </main>
    <script src="javascript/validacion.js"></script>
</body>
</html>