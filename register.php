<?php
session_start();

// Errores (por campo) y datos escritos que deja proc/proc_register.php
$regErrors = $_SESSION['register_errors'] ?? [];
$regOld = $_SESSION['register_old'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_old']);

function errorPhp(array $errores, string $campo): string
{
    return htmlspecialchars($errores[$campo] ?? '', ENT_QUOTES, 'UTF-8');
}

function valorViejo(array $datos, string $campo): string
{
    return htmlspecialchars($datos[$campo] ?? '', ENT_QUOTES, 'UTF-8');
}
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

            <form id="form-registro" class="auth-form" method="post" action="proc/proc_register.php">
                <div class="field">
                    <label for="first-name">Nombre</label>
                    <input class="input" type="text" id="first-name" name="nombre" value="<?= valorViejo($regOld, 'nombre') ?>" onblur="comprobarNombre()">
                    <span id="error-first-name" class="error-php"><?= errorPhp($regErrors, 'nombre') ?></span>
                </div>

                <div class="field">
                    <label for="last-name">Apellidos</label>
                    <input class="input" type="text" id="last-name" name="apellidos" value="<?= valorViejo($regOld, 'apellidos') ?>" onblur="comprobarApellidos()">
                    <span id="error-last-name" class="error-php"><?= errorPhp($regErrors, 'apellidos') ?></span>
                </div>

                <div class="field">
                    <label for="school-email">Correo de la escuela</label>
                    <input class="input" type="text" id="school-email" name="email" value="<?= valorViejo($regOld, 'email') ?>" onblur="comprobarEmail()">
                    <span id="error-school-email" class="error-php"><?= errorPhp($regErrors, 'email') ?></span>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input class="input" type="password" id="password" name="password" onblur="comprobarPasswordRegistro()">
                    <span id="error-password" class="error-php"><?= errorPhp($regErrors, 'password') ?></span>
                </div>

                <div class="field">
                    <label for="confirm-password">Confirma contraseña</label>
                    <input class="input" type="password" id="confirm-password" name="confirm_password" onblur="comprobarConfirmar()">
                    <span id="error-confirm-password" class="error-php"><?= errorPhp($regErrors, 'confirm_password') ?></span>
                </div>

                <div class="field">
                    <label for="role">Rol</label>
                    <select class="select" id="role" name="role_id" onblur="comprobarRol()">
                        <option value="">Selecciona un rol</option>
                        <option value="1"<?= ($regOld['role_id'] ?? '') === '1' ? ' selected' : '' ?>>admin</option>
                        <option value="2"<?= ($regOld['role_id'] ?? '') === '2' ? ' selected' : '' ?>>profesor</option>
                        <option value="3"<?= ($regOld['role_id'] ?? '') === '3' ? ' selected' : '' ?>>educador</option>
                        <option value="4"<?= ($regOld['role_id'] ?? '') === '4' ? ' selected' : '' ?>>secretaria</option>
                    </select>
                    <span id="error-role" class="error-php"><?= errorPhp($regErrors, 'role_id') ?></span>
                </div>

                <button class="btn-primary btn-block" type="submit" onclick="return validarRegistro()">Registrarse <span aria-hidden="true">→</span></button>
            </form>

            <p class="auth-card__foot">
                ¿Ya tienes cuenta? <a href="index.php">Inicia sesión aquí</a>
            </p>
        </section>

    </main>
    <script src="javascript/validacion.js"></script>
</body>
</html>