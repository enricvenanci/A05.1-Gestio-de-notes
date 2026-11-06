<?php
session_start();

// Si ya hay sesión iniciada, directo a la zona privada
if (isset($_SESSION['user_id'], $_SESSION['user_name'])) {
    header('Location: bienvenida.php');
    exit;
}

// Errores (por campo) y usuario escrito que deja proc/proc_login.php
$loginErrors = $_SESSION['login_errors'] ?? [];
$loginOld = $_SESSION['login_old'] ?? '';
unset($_SESSION['login_errors'], $_SESSION['login_old']);

function errorPhp(array $errores, string $campo): string
{
    return htmlspecialchars($errores[$campo] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
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

            <?php if (isset($loginErrors['general'])): ?>
                <div class="auth-alert" role="alert"><?= errorPhp($loginErrors, 'general') ?></div>
            <?php endif; ?>

            <form class="auth-form" method="post" action="proc/proc_login.php" aria-label="Formulario de inicio de sesión">
                <div class="field">
                    <label for="username">Nombre</label>
                    <input class="input" type="text" id="username" name="username" value="<?= htmlspecialchars($loginOld, ENT_QUOTES, 'UTF-8') ?>" onblur="comprobarUsuario()">
                    <span id="error-username" class="error-php"><?= errorPhp($loginErrors, 'username') ?></span>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <input class="input" type="password" id="password" name="password" onblur="comprobarPassword()">
                    <span id="error-password" class="error-php"><?= errorPhp($loginErrors, 'password') ?></span>
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