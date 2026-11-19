<?php
$nombre = isset($_GET['nombre']) && is_string($_GET['nombre'])
    ? trim($_GET['nombre'])
    : '';
$nombre = mb_substr($nombre, 0, 100);

$cicloSeleccionado = 0;
if (isset($_GET['ciclo']) && is_string($_GET['ciclo'])) {
    $valorCiclo = filter_var($_GET['ciclo'], FILTER_VALIDATE_INT);
    if ($valorCiclo !== false && $valorCiclo >= 0) {
        $cicloSeleccionado = $valorCiclo;
    } else {
        $filterError = 'El filtro de ciclo no es válido.';
    }
}

$cursoSeleccionado = 0;
if (isset($_GET['curso']) && is_string($_GET['curso'])) {
    $valorCurso = filter_var($_GET['curso'], FILTER_VALIDATE_INT);
    if ($valorCurso !== false && $valorCurso >= 0) {
        $cursoSeleccionado = $valorCurso;
    } else {
        $filterError = 'El filtro de curso no es válido.';
    }
}

$ciclosStmt = $conn->prepare('SELECT id, nombre FROM ciclos ORDER BY nombre');
$ciclosStmt->execute();
$ciclos = $ciclosStmt->fetchAll(PDO::FETCH_ASSOC);

$cursosStmt = $conn->prepare('SELECT id, nombre FROM niveles ORDER BY id');
$cursosStmt->execute();
$cursos = $cursosStmt->fetchAll(PDO::FETCH_ASSOC);

$promocionesStmt = $conn->prepare('SELECT id, nombre, actual FROM promociones ORDER BY id DESC');
$promocionesStmt->execute();
$promociones = $promocionesStmt->fetchAll(PDO::FETCH_ASSOC);

$promocionActual = 0;
foreach ($promociones as $promocion) {
    if ((int) $promocion['actual'] === 1) {
        $promocionActual = (int) $promocion['id'];
        break;
    }
}

$promocionSeleccionada = $promocionActual;
if (isset($_GET['promocion']) && is_string($_GET['promocion'])) {
    $valorPromocion = filter_var($_GET['promocion'], FILTER_VALIDATE_INT);
    if ($valorPromocion !== false && $valorPromocion >= 0) {
        $promocionSeleccionada = $valorPromocion;
    } else {
        $filterError = 'El filtro de promoción no es válido.';
    }
}

$condiciones = ["u.rol = 'alumno'"];
$parametros = [];

if ($nombre !== '') {
    $condiciones[] = "CONCAT_WS(' ', u.nombre, u.apellidos) LIKE :nombre";
    $parametros['nombre'] = '%' . $nombre . '%';
}
if ($cursoSeleccionado > 0) {
    $condiciones[] = 'a.nivel_id = :curso';
    $parametros['curso'] = $cursoSeleccionado;
}
if ($cicloSeleccionado > 0) {
    $condiciones[] = 'a.ciclo_id = :ciclo';
    $parametros['ciclo'] = $cicloSeleccionado;
}
if ($promocionSeleccionada > 0) {
    $condiciones[] = 'm.promocion_id = :promocion';
    $parametros['promocion'] = $promocionSeleccionada;
}

$sql = 'SELECT u.id, u.nombre, u.apellidos, u.email,
               GROUP_CONCAT(DISTINCT c.nombre ORDER BY c.nombre SEPARATOR ", ") AS ciclo,
               GROUP_CONCAT(DISTINCT n.nombre ORDER BY n.id SEPARATOR ", ") AS curso,
               GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ", ") AS promocion
        FROM usuaris u
        LEFT JOIN matriculas m ON m.alumno_id = u.id
        LEFT JOIN asignaturas a ON a.id = m.asignatura_id
        LEFT JOIN ciclos c ON c.id = a.ciclo_id
        LEFT JOIN niveles n ON n.id = a.nivel_id
        LEFT JOIN promociones p ON p.id = m.promocion_id
        WHERE ' . implode(' AND ', $condiciones) . '
        GROUP BY u.id, u.nombre, u.apellidos, u.email
        ORDER BY u.apellidos, u.nombre';

$alumnosStmt = $conn->prepare($sql);
$alumnosStmt->execute($parametros);
$alumnos = $alumnosStmt->fetchAll(PDO::FETCH_ASSOC);
