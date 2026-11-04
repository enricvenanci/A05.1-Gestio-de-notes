<?php
session_start();
 
// Si no ha hecho login, vuelve al inicio
if (!isset($_SESSION['user_id'], $_SESSION['user_name'])) {
    header('Location: index.php');
    exit();
}
 
// Si no llega el id del alumno, vuelve al listado de alumnos
if (!isset($_GET['idAlumno'])) {
    header('Location: bienvenida.php');
    exit();
}
 
include_once('./conexion/conexion.php');
 
// Nombre del usuario que ha hecho login (el de la cabecera)
$userName = htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8');
 
// Id del alumno que viene del listado. Con (int) nos aseguramos de que es un número
$idAlumno = (int) $_GET['idAlumno'];
 
try {
    // PASO 1: datos del alumno (la vista ya une alumno + curso + ciclo)
    $sql = "SELECT id, nombre, apellidos, curso_id, curso, ciclo
            FROM vista_alumnos
            WHERE id = :idAlumno";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idAlumno', $idAlumno, PDO::PARAM_INT);
    $stmt->execute();
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);
 
    // Si no existe ningún alumno con ese id
    if ($alumno === false) {
        echo "No hay ningún alumno con ese ID. ";
        echo "<a href='./bienvenida.php'>Volver al listado</a>";
        exit();
    }
 
    // PASO 2: asignaturas del curso del alumno con su nota.
    // LEFT JOIN: si la asignatura aún no tiene nota, sale NULL
    $sql2 = "SELECT asignaturas.nombre, notas.nota
             FROM asignaturas
             LEFT JOIN notas ON notas.asignatura_id = asignaturas.id
                            AND notas.alumno_id = :idAlumno2
             WHERE asignaturas.curso_id = :idCurso
             ORDER BY asignaturas.id";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bindParam(':idAlumno2', $idAlumno, PDO::PARAM_INT);
    $stmt2->bindParam(':idCurso', $alumno['curso_id'], PDO::PARAM_INT);
    $stmt2->execute();
    $asignaturas = $stmt2->fetchAll(PDO::FETCH_ASSOC);
 
    $stmt = null;
    $stmt2 = null;
    $conn = null;
 
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
 
// Calculamos la media y cuántas asignaturas tienen nota
$suma = 0;
$evaluadas = 0;
foreach ($asignaturas as $fila) {
    if ($fila['nota'] !== null) {
        $suma += $fila['nota'];
        $evaluadas++;
    }
}
$media = ($evaluadas > 0) ? number_format($suma / $evaluadas, 1, ',', '') : '—';
 
// Datos para pintar: id con formato AL-0012 y curso tipo "DAW 2"
$codigoAlumno = sprintf('AL-%04d', $alumno['id']);
 
preg_match('/\((.*?)\)/', $alumno['ciclo'], $siglas);   // lo que hay entre paréntesis: DAW
preg_match('/\d+/', $alumno['curso'], $numero);         // el número del curso: 2
$etiquetaCurso = ($siglas[1] ?? $alumno['ciclo']) . ' ' . ($numero[0] ?? '');
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
                <span class="user-pill"><?= $userName ?></span>
            </header>
 
            <div class="page-header">
                <div class="page-icon">&#9670;</div>
                <div>
                    <h1 class="page-title">Notas del alumno</h1>
                    <p class="page-subtitle">Consulta las calificaciones de cada asignatura.</p>
                </div>
            </div>
 
            <section class="card">
                <div class="card__head">
                    <span><?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($etiquetaCurso, ENT_QUOTES, 'UTF-8') ?> · <?= $codigoAlumno ?></span>
                    <a href="bienvenida.php">&larr; Volver al listado</a>
                </div>
 
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Asignatura</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($asignaturas as $fila) { ?>
                                <tr>
                                    <td class="cell-name"><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= ($fila['nota'] !== null) ? number_format($fila['nota'], 1, ',', '') : '—' ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <td class="cell-name">Media general (<?= $evaluadas ?> evaluadas)</td>
                                <td class="cell-name"><?= $media ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
 
        </div>
    </div>
</body>
</html>