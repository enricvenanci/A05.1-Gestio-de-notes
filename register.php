<?php
$mensajesError = [
    '1' => 'Revisa los datos introducidos.',
    '2' => 'El correo electrónico no es válido.',
    '3' => 'La contraseña no cumple los requisitos.',
    '4' => 'Las contraseñas no coinciden.',
    '5' => 'Ya existe una cuenta con ese correo electrónico.',
    '6' => 'No se pudo completar el registro. Inténtalo de nuevo.',
];
$codigoError = isset($_GET['error']) && is_string($_GET['error']) ? $_GET['error'] : '';
$mensajeError = $mensajesError[$codigoError] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="auth-page">

        <div class="auth-brand">
            <img class="auth-brand__logo" src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
            <h2 class="auth-brand__title">Colegio Internacional San Miguel Arcángel</h2>
            <p class="auth-brand__text">Gestión de notas</p>
        </div>

        <section class="auth-card" aria-label="Crear una cuenta">
            <h1>Crea tu cuenta</h1>
            <p class="auth-card__intro">Completa tus datos para registrarte.</p>
            <?php if ($mensajeError !== ''): ?>
                <p class="msg error" role="alert"><?= htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <form id="form-registro" class="auth-form" method="post" action="proc/proc_register.php">
                <div class="field">
                    <label for="first-name">Nombre</label>
                    <input class="input" type="text" id="first-name" name="nombre" maxlength="50" required onblur="comprobarNombre()">
                    <span id="error-first-name"></span>
                </div>

                <div class="field">
                    <label for="last-name">Apellidos</label>
                    <input class="input" type="text" id="last-name" name="apellidos" maxlength="100" required onblur="comprobarApellidos()">
                    <span id="error-last-name"></span>
                </div>

                <div class="field">
                    <label for="school-email">Correo de la escuela</label>
                    <input class="input" type="email" id="school-email" name="email" maxlength="100" required onblur="comprobarEmail()">
                    <span id="error-school-email"></span>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input class="input" type="password" id="password" name="password" required onblur="comprobarPasswordRegistro()">
                    <span id="error-password"></span>
                </div>

                <div class="field">
                    <label for="confirm-password">Confirma contraseña</label>
                    <input class="input" type="password" id="confirm-password" name="confirm_password" required onblur="comprobarConfirmar()">
                    <span id="error-confirm-password"></span>
                </div>

                <button class="btn-primary btn-block" type="submit" onclick="return validarRegistro()">Registrarse como alumno <span aria-hidden="true">→</span></button>
            </form>

            <p class="auth-card__foot">
                ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
            </p>
        </section>

    </main>
    <script src="javascript/validacion.js"></script>
</body>
</html>