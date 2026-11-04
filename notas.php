<?php
session_start();

// Si no ha hecho login, vuelve al inicio
if (!isset($_SESSION['user_id'], $_SESSION['user_name'])) {
    header('Location: index.php');
    exit();
}

// Si no llega el id del alumno, vuelve al listado de alumnos
if (!isset($_GET['idAlumno'])) {
    header('Location: alumnos.php');
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
        echo "<a href='./alumnos.php'>Volver al listado</a>";
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
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes de l'alumne</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="app-page">
    <div class="app">

        <!-- Menú lateral -->
        <aside class="sidebar">
            <div class="sidebar-top">
                <img src="img/logo_gestion_nota.png" alt="Logo del Colegio Internacional San Miguel Arcángel">
                <p class="sidebar-school">Col·legi Internacional<br>San Miguel Arcángel</p>
            </div>
            <a class="sidebar-logout" href="logout.php">Tancar sessió</a>
        </aside>

        <div class="app-main">

            <!-- Barra de arriba con el nombre del usuario logueado -->
            <header class="topbar">
                <span class="topbar-user">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="8" r="4"></circle><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"></path></svg>
                    Benvingut/da, <?= $userName ?>
                </span>
            </header>

            <main class="notas-content">

                <div class="notas-title">
                    <a class="back-link" href="alumnos.php" aria-label="Tornar al llistat d'alumnes">&larr;</a>
                    <div>
                        <h1>Notes de l'alumne</h1>
                        <p>Consulta el seguiment acadèmic i les qualificacions de cada assignatura.</p>
                    </div>
                </div>

                <!-- Datos del alumno -->
                <section class="student-card">
                    <div>
                        <h2><?= htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellidos'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <p><?= htmlspecialchars($etiquetaCurso, ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <span class="student-id">ID: <?= $codigoAlumno ?></span>
                </section>

                <!-- Media y número de asignaturas evaluadas -->
                <section class="stats">
                    <div class="stat-card">
                        <p class="stat-label">Mitjana general</p>
                        <p class="stat-value stat-value-gold"><?= $media ?></p>
                        <p class="stat-note">Sobre 10</p>
                    </div>
                    <div class="stat-card">
                        <p class="stat-label">Assignatures</p>
                        <p class="stat-value"><?= $evaluadas ?></p>
                        <p class="stat-note">Avaluades</p>
                    </div>
                </section>

                <!-- Tabla de notas -->
                <section class="grades-card">
                    <h3>Qualificacions per assignatura</h3>
                    <p class="grades-sub">Consulta les notes de cada assignatura</p>

                    <div class="grades-scroll">
                        <table class="grades-table">
                            <thead>
                                <tr>
                                    <th>Assignatura</th>
                                    <th class="grade-col">Nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($asignaturas as $fila) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($fila['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="grade-col">
                                            <?= ($fila['nota'] !== null) ? number_format($fila['nota'], 1, ',', '') : '—' ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </section>

            </main>
        </div>
    </div>
</body>
</html>