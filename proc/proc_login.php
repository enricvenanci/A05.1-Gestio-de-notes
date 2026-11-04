<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

include '../conexion/conexion.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare(
    'SELECT id, nombre, apellidos, password_hash
     FROM usuarios
     WHERE nombre = :nombre OR email = :email
     LIMIT 1'
);
$stmt->execute([
    'nombre' => $username,
    'email' => $username,
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['nombre'];
    $_SESSION['user_full_name'] = $user['nombre'] . ' ' . $user['apellidos'];

    header('Location: ../bienvenida.php');
    exit;
}

header('Location: ../index.php');
exit;
