<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/conexion/conexion.php';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare(
        'SELECT id, nombre, apellidos, password_hash
         FROM usuarios
         WHERE nombre = :nombre OR email = :email
         LIMIT 1'
    );
    $stmt->execute([
        'nombre' => $username,
        'email' => $username,
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_full_name'] = $user['nombre'] . ' ' . $user['apellidos'];

        header('Location: bienvenida.php');
        exit;
    }

    $error = 'El nombre de usuario o la contraseña no son correctos.';
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
    <main class="login-page">
        <section class="login-card" aria-label="Inicio de sesión">
            <div class="brand">
                <img src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
            </div>

            <div class="login-content">
                <p class="eyebrow">GESTIÓN DE NOTAS</p>
                <h1>Bienvenido de nuevo</h1>
                <p class="welcome-text">Accede a tu cuenta para continuar.</p>

                <?php if ($error !== ''): ?>
                    <p class="form-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <form class="login-form" method="post">
                    <div class="form-field">
                        <label for="username">Nombre</label>
                        <input type="text" id="username" name="username" required>
                    </div>

                    <div class="form-field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>

                    <button type="submit">Iniciar sesión <span aria-hidden="true">→</span></button>
                </form>

                <p class="register-text">
                    No tienes cuenta? <a href="register.php">Regístrate aquí</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>