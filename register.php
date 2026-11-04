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

                <form id="form-registro" class="login-form register-form" method="post" action="proc/proc_register.php">
                    <div class="form-field">
                        <label for="first-name">Nombre</label>
                        <input type="text" id="first-name" name="nombre" onblur="comprobarNombre()">
                        <span id="error-first-name"></span>
                    </div>

                    <div class="form-field">
                        <label for="last-name">Apellidos</label>
                        <input type="text" id="last-name" name="apellidos" onblur="comprobarApellidos()">
                        <span id="error-last-name"></span>
                    </div>

                    <div class="form-field">
                        <label for="school-email">Correo de la escuela</label>
                        <input type="text" id="school-email" name="email" onblur="comprobarEmail()">
                        <span id="error-school-email"></span>
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" onblur="comprobarPasswordRegistro()">
                        <span id="error-password"></span>
                    </div>

                    <div class="form-field">
                        <label for="confirm-password">Confirma contraseña</label>
                        <input type="password" id="confirm-password" name="confirm_password" onblur="comprobarConfirmar()">
                        <span id="error-confirm-password"></span>
                    </div>

                    <div class="form-field">
                        <label for="role">Rol</label>
                        <select id="role" name="role_id" onblur="comprobarRol()">
                            <option value="">Selecciona un rol</option>
                            <option value="1">admin</option>
                            <option value="2">profesor</option>
                            <option value="3">educador</option>
                            <option value="4">secretaria</option>
                        </select>
                        <span id="error-role"></span>
                    </div>

                    <button type="submit" onclick="return validarRegistro()">Registrarse <span aria-hidden="true">→</span></button>
                </form>

                <p class="register-text">
                    ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
                </p>
            </div>
        </section>
    </main>
    <script src="javascript/validacion.js"></script>
</body>
</html>