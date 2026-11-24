<?php
session_start();

if (!isset($_SESSION['user_id'], $_SESSION['user_role'], $_SESSION['user_name'])) {
    header('Location: index.php');
    exit;
}

if ($_SESSION['user_role'] === 'alumno') {
    header('Location: notas.php');
    exit;
}

$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];
$mensaje = $_SESSION['flash_ok'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_ok'], $_SESSION['flash_error']);

$mensajesError = [
    '1' => 'La solicitud no es válida. Inténtalo de nuevo.',
    '2' => 'El alumno seleccionado no es válido.',
];
$codigoError = isset($_GET['error']) && is_string($_GET['error']) ? $_GET['error'] : '';
$mensajeCodigo = $mensajesError[$codigoError] ?? '';

include 'sql/conexion/conexion.php';
include 'filtros/consultar_alumnos.php';

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
    <title>Listado de alumnos</title>
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
                <span class="user-pill">
                    <?= e($userName) ?>
                    <span class="user-role-tag"><?= e($userRole) ?></span>
                </span>
            </header>

            <div class="page-header">
                <div class="page-icon" aria-hidden="true">&#9670;</div>
                <div>
                    <h1 class="page-title">Bienvenido, <?= e($userName) ?></h1>
                    <p class="page-subtitle">Gestión de notas · <?= count($alumnos) ?> alumno(s) registrados</p>
                </div>
            </div>

            <?php if ($mensaje): ?>
                <div class="msg ok" role="status"><?= e($mensaje) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="msg error" role="alert"><?= e($error) ?></div>
            <?php endif; ?>
            <?php if ($mensajeCodigo !== ''): ?>
                <div class="msg error" role="alert"><?= e($mensajeCodigo) ?></div>
            <?php endif; ?>
            <?php if (!empty($filterError)): ?>
                <div class="msg error" role="alert"><?= e($filterError) ?></div>
            <?php endif; ?>

            <?php if ($userRole === 'profesor'): ?>
                <p class="page-action">
                    <a class="btn-primary" href="pages/introducir_notas.php">Introducir o modificar notas</a>
                </p>
            <?php endif; ?>

            <form class="filters" id="filtros-alumnos" method="get">
                <div class="filters__field filters__field--grow" id="filtro-nombre">
                    <label class="filters__label" for="nombre">Buscar alumno</label>
                    <input class="input" type="search" id="nombre" name="nombre" maxlength="100" placeholder="Busca por nombre..." value="<?= e($nombre) ?>">
                </div>
                <div class="filters__field" id="filtro-curso">
                    <label class="filters__label" for="curso">Curso</label>
                    <select class="select" id="curso" name="curso">
                        <option value="0">Todos los cursos</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= (int) $curso['id'] ?>" <?= $cursoSeleccionado === (int) $curso['id'] ? 'selected' : '' ?>>
                                <?= e($curso['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filters__field" id="filtro-ciclo">
                    <label class="filters__label" for="ciclo">Ciclo</label>
                    <select class="select" id="ciclo" name="ciclo">
                        <option value="0">Todos los ciclos</option>
                        <?php foreach ($ciclos as $ciclo): ?>
                            <option value="<?= (int) $ciclo['id'] ?>" <?= $cicloSeleccionado === (int) $ciclo['id'] ? 'selected' : '' ?>>
                                <?= e($ciclo['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filters__field" id="filtro-promocion">
                    <label class="filters__label" for="promocion">Promoción</label>
                    <select class="select" id="promocion" name="promocion">
                        <option value="0" <?= $promocionSeleccionada === 0 ? 'selected' : '' ?>>Todas las promociones</option>
                        <?php foreach ($promociones as $promocion): ?>
                            <option value="<?= (int) $promocion['id'] ?>" <?= $promocionSeleccionada === (int) $promocion['id'] ? 'selected' : '' ?>>
                                <?= e($promocion['nombre']) ?><?= (int) $promocion['actual'] === 1 ? ' (actual)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button class="btn-primary" id="btn-filtrar" type="submit">Filtrar</button>
            </form>

            <section class="card">
                <div class="card__head">
                    <span>Listado de alumnos</span>
                </div>

                <?php if (!$alumnos): ?>
                    <p class="empty"><?= $nombre !== '' || $cursoSeleccionado > 0 || $cicloSeleccionado > 0 || $promocionSeleccionada > 0 ? 'No hay alumnos que coincidan con los filtros.' : 'No hay alumnos registrados.' ?></p>
                <?php else: ?>
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Alumno</th>
                                    <th>Email</th>
                                    <th>Ciclo</th>
                                    <th>Curso</th>
                                    <th>Promoción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($alumnos as $alumno): ?>
                                <tr>
                                    <td class="cell-name"><?= e($alumno['apellidos']) ?>, <?= e($alumno['nombre']) ?></td>
                                    <td><?= e($alumno['email']) ?></td>
                                    <td><?= e($alumno['ciclo']) ?: '—' ?></td>
                                    <td><?= e($alumno['curso']) ?: '—' ?></td>
                                    <td><?= e($alumno['promocion']) ?: '—' ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <a class="btn-outline" href="notas.php?idAlumno=<?= (int) $alumno['id'] ?>">Ver notas</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</body>
</html>
