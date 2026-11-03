<?php
session_start();

// Sin sesión: al login
if (!isset($_SESSION['user_id'], $_SESSION['user_name'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/conexion/conexion.php';

$userName = $_SESSION['user_name'];

// Mensajes que deja proc/proc_alumnos.php
$mensaje = $_SESSION['flash_ok'] ?? null;
$error   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

// Todos los alumnos con su ciclo y curso (vista definida en gestio_notes.sql)
$alumnos = $conn->query(
    'SELECT id, nombre, apellidos, email, ciclo, curso
     FROM vista_alumnos
     ORDER BY ciclo, curso, apellidos, nombre'
)->fetchAll(PDO::FETCH_ASSOC);

function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
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
    <main class="alumnos-page">
        <section class="alumnos-card" aria-label="Página de bienvenida y listado de alumnos">
            <div class="alumnos-header">
                <div class="cabecera-izq">
                    <img class="alumnos-logo" src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
                    <div>
                        <p class="eyebrow">GESTIÓN DE NOTAS</p>
                        <h1>Bienvenido, <?= e($userName) ?></h1>
                        <p class="welcome-text"><?= count($alumnos) ?> alumno(s) registrados</p>
                    </div>
                </div>
                <a class="logout-link-small" href="logout.php">Cerrar sesión</a>
            </div>

            <?php if ($mensaje): ?>
                <div class="msg ok" role="status"><?= e($mensaje) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="msg error" role="alert"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if (!$alumnos): ?>
                <p>No hay alumnos registrados.</p>
            <?php else: ?>
                <div class="tabla-wrap">
                    <table class="tabla-alumnos">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Email</th>
                                <th>Ciclo</th>
                                <th>Curso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($alumnos as $a): ?>
                            <tr>
                                <td><?= e($a['apellidos']) ?>, <?= e($a['nombre']) ?></td>
                                <td><?= e($a['email']) ?></td>
                                <td><?= e($a['ciclo']) ?></td>
                                <td><?= e($a['curso']) ?></td>
                                <td>
                                    <div class="acciones">
                                        <a class="btn-tabla btn-notas" href="notas.php?alumno_id=<?= (int) $a['id'] ?>">Ver notas</a>

                                        <form method="post" action="proc/proc_alumnos.php"
                                              onsubmit="return confirm('¿Eliminar a este alumno y todas sus notas?');">
                                            <input type="hidden" name="accion" value="eliminar">
                                            <input type="hidden" name="id" value="<?= (int) $a['id'] ?>">
                                            <button type="submit" class="btn-eliminar">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>