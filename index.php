<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="login-page">
        <section class="login-card" aria-label="Inicio de sesión">
            <div class="brand">
                <img src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
            </div>

            <div class="login-content">
                <p class="eyebrow">GESTIÓN DE NOTAS</p>
                <h1>Bienvenido de nuevo</h1>
                <p class="welcome-text">Accede a tu cuenta para continuar.</p>
                <form class="login-form" method="post" action="proc/proc_login.php" aria-label="Formulario de inicio de sesión">
                    <div class="form-field">
                        <label for="username">Nombre</label>
                        <input type="text" id="username" name="username" onblur="comprobarUsuario()">
                        <span id="error-username"></span>
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" onblur="comprobarPassword()">
                        <span id="error-password"></span>
                    </div>

                    <button type="submit" onclick="return validarLogin()">Iniciar sesión <span aria-hidden="true">→</span></button>
                </form>

                <p class="register-text">
                    No tienes cuenta? <a href="register.php">Regístrate aquí</a>
                </p>
            </div>
        </section>
    </main>
    <script src="javascript/validacion.js"></script>
</body>
</html>