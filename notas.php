<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_role'])) {
    header('Location: index.php');
    exit;
}

$rol = $_SESSION['user_role'];
if ($rol === 'alumno') {
    $idAlumno = (int) $_SESSION['user_id'];
} elseif (
    in_array($rol, ['admin', 'profesor', 'educador', 'secretaria'], true)
    && isset($_GET['idAlumno'])
    && is_string($_GET['idAlumno'])
) {
    $idAlumno = filter_var($_GET['idAlumno'], FILTER_VALIDATE_INT);
    if ($idAlumno === false || $idAlumno <= 0) {
        header('Location: bienvenida.php?error=2');
        exit;
    }
} else {
    header('Location: bienvenida.php');
    exit;
}

include 'sql/conexion/conexion.php';

try {
    $alumnoStmt = $conn->prepare(
        'SELECT id, nombre, apellidos
         FROM usuaris
         WHERE id = :id AND rol = :rol
         LIMIT 1'
    );
    $alumnoStmt->execute(['id' => $idAlumno, 'rol' => 'alumno']);
    $alumno = $alumnoStmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        header($rol === 'alumno' ? 'Location: logout.php' : 'Location: bienvenida.php?error=2');
        exit;
    }

    $matriculasStmt = $conn->prepare(
        'SELECT a.nombre AS asignatura, c.nombre AS ciclo, n.nombre AS curso,
                p.nombre AS promocion, m.nota
         FROM matriculas m
         INNER JOIN asignaturas a ON a.id = m.asignatura_id
         INNER JOIN ciclos c ON c.id = a.ciclo_id
         INNER JOIN niveles n ON n.id = a.nivel_id
         INNER JOIN promociones p ON p.id = m.promocion_id
         WHERE m.alumno_id = :alumno
         ORDER BY p.id DESC, c.nombre, n.id, a.nombre'
    );
    $matriculasStmt->execute(['alumno' => $idAlumno]);
    $matriculas = $matriculasStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log('notas.php: ' . $exception->getMessage());
    http_response_code(500);
    exit('No se pudieron consultar las notas.');
}

function e(?string $valor): string
{
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

$suma = 0.0;
$evaluadas = 0;
foreach ($matriculas as $matricula) {
    if ($matricula['nota'] !== null) {
        $suma += (float) $matricula['nota'];
        $evaluadas++;
    }
}
$media = $evaluadas > 0 ? number_format($suma / $evaluadas, 2, ',', '') : '—';
$nombreCompleto = $alumno['nombre'] . ' ' . $alumno['apellidos'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas del alumno</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="sidebar__brand">
                <img class="sidebar__logo" src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
                <p class="sidebar__name">Colegio Internacional<br>San Miguel Arcángel</p>
            </div>
            <a class="sidebar__logout" href="logout.php">Cerrar sesión</a>
        </aside>

        <div class="main">
            <header class="topbar">
                <span></span>
                <span class="user-pill"><?= e($_SESSION['user_full_name'] ?? $_SESSION['user_name'] ?? '') ?></span>
            </header>

            <div class="page-header">
                <div class="page-icon" aria-hidden="true">&#9670;</div>
                <div>
                    <h1 class="page-title">Notas del alumno</h1>
                    <p class="page-subtitle">Consulta las calificaciones de cada asignatura y promoción.</p>
                </div>
            </div>

            <section class="card">
                <div class="card__head">
                    <span><?= e($nombreCompleto) ?></span>
                    <?php if ($rol !== 'alumno'): ?>
                        <a href="bienvenida.php">&larr; Volver al listado</a>
                    <?php endif; ?>
                </div>
                <?php if (!$matriculas): ?>
                    <p class="empty">Este alumno todavía no tiene matrículas.</p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Promoción</th>
                                    <th>Ciclo</th>
                                    <th>Curso</th>
                                    <th>Asignatura</th>
                                    <th>Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matriculas as $matricula): ?>
                                    <tr>
                                        <td><?= e($matricula['promocion']) ?></td>
                                        <td><?= e($matricula['ciclo']) ?></td>
                                        <td><?= e($matricula['curso']) ?></td>
                                        <td class="cell-name"><?= e($matricula['asignatura']) ?></td>
                                        <td><?= $matricula['nota'] !== null ? number_format((float) $matricula['nota'], 2, ',', '') : '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="cell-name" colspan="4">Media (<?= $evaluadas ?> calificaciones)</td>
                                    <td class="cell-name"><?= $media ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</body>
</html>
