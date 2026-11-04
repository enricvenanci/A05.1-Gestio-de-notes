<?php

$nombre = isset($_GET['nombre']) && is_string($_GET['nombre']) ? trim($_GET['nombre']) : '';
$cursoSeleccionado = isset($_GET['curso']) && is_string($_GET['curso']) ? $_GET['curso'] : '';
$cursos = $conn->query('SELECT DISTINCT nombre FROM cursos ORDER BY nombre')->fetchAll(PDO::FETCH_COLUMN);

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

$sql = 'SELECT id, nombre, apellidos, email, ciclo, curso FROM vista_alumnos'
    . ($condiciones ? ' WHERE ' . implode(' AND ', $condiciones) : '')
    . ' ORDER BY ciclo, curso, apellidos, nombre';
$stmt = $conn->prepare($sql);
$stmt->execute($parametros);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
