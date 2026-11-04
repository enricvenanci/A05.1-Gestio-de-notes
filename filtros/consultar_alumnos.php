<?php

$nombre = isset($_GET['nombre']) && is_string($_GET['nombre']) ? trim($_GET['nombre']) : '';

$sql = 'SELECT id, nombre, apellidos, email, ciclo, curso FROM vista_alumnos';
if ($nombre !== '') {
    $sql .= " WHERE CONCAT_WS(' ', nombre, apellidos) LIKE :nombre";
}
$sql .= ' ORDER BY ciclo, curso, apellidos, nombre';

$stmt = $conn->prepare($sql);
$stmt->execute($nombre !== '' ? ['nombre' => "%$nombre%"] : []);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
