<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../bienvenida.php');
    exit;
}

if (
    !isset($_SESSION['user_id'], $_SESSION['user_role'])
    || $_SESSION['user_role'] !== 'admin'
) {
    header('Location: ../index.php?error=1');
    exit;
}

$id = isset($_POST['id']) && is_string($_POST['id'])
    ? filter_var($_POST['id'], FILTER_VALIDATE_INT)
    : false;
if ($id === false || $id <= 0) {
    header('Location: ../bienvenida.php?error=2');
    exit;
}

include '../sql/conexion/conexion.php';

/** @var PDO $conn */
$rol = 'alumno';

try {
    $stmt = $conn->prepare(
        'SELECT nombre, apellidos
         FROM usuaris
         WHERE id = :id AND rol = :rol
         LIMIT 1'
    );
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':rol', $rol, PDO::PARAM_STR);
    $stmt->execute();
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        $_SESSION['flash_error'] = 'El alumno ya no existe.';
    } else {
        $deleteStmt = $conn->prepare(
            'DELETE FROM usuaris WHERE id = :id AND rol = :rol'
        );
        $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteStmt->bindParam(':rol', $rol, PDO::PARAM_STR);
        $deleteStmt->execute();
        $_SESSION['flash_ok'] = 'Alumno eliminado: '
            . $alumno['nombre'] . ' ' . $alumno['apellidos'] . '.';
    }
} catch (PDOException $exception) {
    error_log('proc_alumnos.php: ' . $exception->getMessage());
    $_SESSION['flash_error'] = 'No se pudo eliminar al alumno. Inténtalo de nuevo.';
}

header('Location: ../bienvenida.php');
exit;
