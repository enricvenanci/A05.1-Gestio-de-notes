<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="login-page">
        <section class="login-card register-card" aria-label="Crear una cuenta">
            <div class="brand">
                <img src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
            </div>

            <div class="login-content register-content">
                <p class="eyebrow">GESTIÓN DE NOTAS</p>
                <h1>Crea tu cuenta</h1>
                <p class="welcome-text">Completa tus datos para registrarte.</p>

                <form class="login-form register-form" method="post" action="proc/proc_register.php">
                    <div class="form-field">
                        <label for="full-name">Nombre y apellidos</label>
                        <input type="text" id="full-name" name="full_name" required>
                    </div>

                    <div class="form-field">
                        <label for="school-email">Correo de la escuela</label>
                        <input type="email" id="school-email" name="email" required>
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <div class="form-field">
                        <label for="confirm-password">Confirma contraseña</label>
                        <input type="password" id="confirm-password" name="confirm_password" required>
                    </div>

                    <div class="form-field">
                        <label for="role">Rol</label>
                        <input type="text" id="role" name="role" value="admin" required>
                    </div>

                    <button type="submit">Registrarse <span aria-hidden="true">→</span></button>
                </form>

                <p class="register-text">
                    ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>