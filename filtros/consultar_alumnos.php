<?php

$nombre = isset($_GET['nombre']) && is_string($_GET['nombre']) ? trim($_GET['nombre']) : '';
$cursoSeleccionado = isset($_GET['curso']) && is_string($_GET['curso']) ? $_GET['curso'] : '';
$cicloSeleccionado = isset($_GET['ciclo']) && is_string($_GET['ciclo']) ? $_GET['ciclo'] : '';

// Obtiene las opciones para los selectores de curso y ciclo.
$cursos = $conn->query('SELECT DISTINCT nombre FROM cursos ORDER BY nombre')->fetchAll(PDO::FETCH_COLUMN);
$ciclos = $conn->query('SELECT nombre FROM ciclos ORDER BY nombre')->fetchAll(PDO::FETCH_COLUMN);

$condiciones = [];
$parametros = [];

if ($nombre !== '') {
    $condiciones[] = "CONCAT_WS(' ', nombre, apellidos) LIKE :nombre";
    $parametros['nombre'] = "%$nombre%";
}
if ($cursoSeleccionado !== '') {
    $condiciones[] = 'curso = :curso';
    $parametros['curso'] = $cursoSeleccionado;
}
if ($cicloSeleccionado !== '') {
    $condiciones[] = 'ciclo = :ciclo';
    $parametros['ciclo'] = $cicloSeleccionado;
}

// Consulta alumnos aplicando los filtros seleccionados y ordenando el resultado.
$sql = 'SELECT id, nombre, apellidos, email, ciclo, curso FROM vista_alumnos'
    . ($condiciones ? ' WHERE ' . implode(' AND ', $condiciones) : '')
    . ' ORDER BY ciclo, curso, apellidos, nombre';
$stmt = $conn->prepare($sql);
$stmt->execute($parametros);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
