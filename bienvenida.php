<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_name'])) {
    header('Location: index.php');
    exit;
}

$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="login-page">
        <section class="login-card welcome-card" aria-label="Página de bienvenida">
            <div class="brand">
                <img src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
            </div>

            <div class="login-content">
                <p class="eyebrow">GESTIÓN DE NOTAS</p>
                <h1>Bienvenido, <?= $userName ?></h1>
                <p class="welcome-text">Has iniciado sesión correctamente.</p>
                <a class="logout-link" href="logout.php">Cerrar sesión</a>
            </div>
        </section>
    </main>
</body>
</html>
