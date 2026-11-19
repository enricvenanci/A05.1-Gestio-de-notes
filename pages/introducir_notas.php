<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_role']) || $_SESSION['user_role'] !== 'profesor') {
    header('Location: ../index.php?error=1');
    exit;
}


$mensajesError = [
    '1' => 'La solicitud no es válida.',
    '2' => 'No tienes asignada esa asignatura.',
    '3' => 'No hay una promoción actual configurada.',
    '4' => 'No se pudieron guardar las notas. Inténtalo de nuevo.',
];
$codigoError = isset($_GET['error']) && is_string($_GET['error']) ? $_GET['error'] : '';
$mensajeError = $mensajesError[$codigoError] ?? '';
$guardado = isset($_GET['ok']) && $_GET['ok'] === '1';

include '../conexion/conexion.php';

try {
    $asignaturasStmt = $conn->prepare(
        'SELECT a.id, a.nombre, c.nombre AS ciclo, n.nombre AS curso
         FROM asignatura_profesor ap
         INNER JOIN asignaturas a ON a.id = ap.asignatura_id
         INNER JOIN ciclos c ON c.id = a.ciclo_id
         INNER JOIN niveles n ON n.id = a.nivel_id
         INNER JOIN usuaris u ON u.id = ap.profesor_id
         WHERE ap.profesor_id = :profesor AND u.rol = :rol
         ORDER BY c.nombre, n.id, a.nombre'
    );
    $asignaturasStmt->execute([
        'profesor' => (int) $_SESSION['user_id'],
        'rol' => 'profesor',
    ]);
    $asignaturas = $asignaturasStmt->fetchAll(PDO::FETCH_ASSOC);

    $asignaturaSeleccionada = 0;
    if (isset($_GET['asignatura']) && is_string($_GET['asignatura'])) {
        $valorAsignatura = filter_var($_GET['asignatura'], FILTER_VALIDATE_INT);
        if ($valorAsignatura !== false && $valorAsignatura > 0) {
            $asignaturaSeleccionada = $valorAsignatura;
        }
    }
    if ($asignaturaSeleccionada === 0 && $asignaturas) {
        $asignaturaSeleccionada = (int) $asignaturas[0]['id'];
    }

    $asignaturaActual = null;
    foreach ($asignaturas as $asignatura) {
        if ((int) $asignatura['id'] === $asignaturaSeleccionada) {
            $asignaturaActual = $asignatura;
            break;
        }
    }
    if ($asignaturaSeleccionada > 0 && $asignaturaActual === null && $mensajeError === '') {
        $mensajeError = $mensajesError['2'];
    }

    $promocionActual = null;
    $alumnos = [];
    if ($asignaturaActual !== null) {
        $promocionStmt = $conn->prepare(
            'SELECT id, nombre FROM promociones WHERE actual = :actual LIMIT 1'
        );
        $promocionStmt->execute(['actual' => 1]);
        $promocionActual = $promocionStmt->fetch(PDO::FETCH_ASSOC);

        if ($promocionActual) {
            $alumnosStmt = $conn->prepare(
                'SELECT m.alumno_id, u.nombre, u.apellidos, u.email, m.nota
                 FROM matriculas m
                 INNER JOIN usuaris u ON u.id = m.alumno_id
                 WHERE m.asignatura_id = :asignatura
                   AND m.promocion_id = :promocion
                   AND u.rol = :rol
                 ORDER BY u.apellidos, u.nombre'
            );
            $alumnosStmt->execute([
                'asignatura' => $asignaturaSeleccionada,
                'promocion' => (int) $promocionActual['id'],
                'rol' => 'alumno',
            ]);
            $alumnos = $alumnosStmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $mensajeError = $mensajesError['3'];
        }
    }
} catch (PDOException $exception) {
    error_log('introducir_notas.php: ' . $exception->getMessage());
    http_response_code(500);
    exit('No se pudieron consultar las asignaturas y matrículas.');
}

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
    <title>Introducir notas</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="sidebar__brand">
                <img class="sidebar__logo" src="../img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
                <p class="sidebar__name">Colegio Internacional<br>San Miguel Arcángel</p>
            </div>
            <a class="sidebar__logout" href="../logout.php">Cerrar sesión</a>
        </aside>

        <div class="main">
            <header class="topbar">
                <a href="../bienvenida.php">&larr; Volver</a>
                <span class="user-pill"><?= e($_SESSION['user_full_name'] ?? $_SESSION['user_name'] ?? '') ?></span>
            </header>

            <div class="page-header">
                <div class="page-icon" aria-hidden="true">&#9670;</div>
                <div>
                    <h1 class="page-title">Introducir notas</h1>
                    <p class="page-subtitle">Selecciona una asignatura para consultar sus matrículas.</p>
                </div>
            </div>

            <?php if ($guardado): ?>
                <div class="msg ok" role="status">Las notas se han guardado correctamente.</div>
            <?php endif; ?>
            <?php if ($mensajeError !== ''): ?>
                <div class="msg error" role="alert"><?= e($mensajeError) ?></div>
            <?php endif; ?>
            <?php if (!$asignaturas): ?>
                <section class="card">
                    <p class="empty">No tienes asignaturas asignadas.</p>
                </section>
            <?php else: ?>
                <form class="grade-controls" method="get">
                    <div class="filters__field">
                        <label class="filters__label" for="asignatura">Asignatura</label>
                        <select class="select" id="asignatura" name="asignatura" required>
                            <?php foreach ($asignaturas as $asignatura): ?>
                                <option value="<?= (int) $asignatura['id'] ?>" <?= (int) $asignatura['id'] === $asignaturaSeleccionada ? 'selected' : '' ?>>
                                    <?= e($asignatura['ciclo'] . ' · ' . $asignatura['curso'] . ' · ' . $asignatura['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button class="btn-primary" type="submit">Ver alumnos</button>
                </form>

                <?php if ($asignaturaActual !== null && $promocionActual): ?>
                    <section class="card">
                        <div class="card__head">
                            <span><?= e($asignaturaActual['nombre']) ?> · <?= e($promocionActual['nombre']) ?></span>
                        </div>
                        <?php if (!$alumnos): ?>
                            <p class="empty">No hay alumnos matriculados en esta asignatura y promoción.</p>
                        <?php else: ?>
                            <form method="post" action="../acciones/guardar_notas.php">
                                <input type="hidden" name="asignatura_id" value="<?= (int) $asignaturaSeleccionada ?>">
                                <div class="table-wrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Alumno</th>
                                                <th>Email</th>
                                                <th>Nota (0-10)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($alumnos as $alumno): ?>
                                                <tr>
                                                    <td class="cell-name"><?= e($alumno['apellidos'] . ', ' . $alumno['nombre']) ?></td>
                                                    <td><?= e($alumno['email']) ?></td>
                                                    <td>
                                                        <input class="input grade-input" type="number"
                                                               name="notas[<?= (int) $alumno['alumno_id'] ?>]"
                                                               min="0" max="10" step="0.01"
                                                               value="<?= $alumno['nota'] !== null ? e((string) $alumno['nota']) : '' ?>"
                                                               aria-label="Nota de <?= e($alumno['nombre'] . ' ' . $alumno['apellidos']) ?>">
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="grade-submit">
                                    <button class="btn-primary" type="submit">Guardar notas</button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
