<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

require_once __DIR__ . '/../conexion/conexion.php';

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$roleName = trim($_POST['role'] ?? '');

$nameParts = preg_split('/\s+/', $fullName, 2);
$nombre = $nameParts[0] ?? '';
$apellidos = $nameParts[1] ?? '';

if (
    $nombre === '' ||
    $apellidos === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $password === '' ||
    $password !== $confirmPassword ||
    $roleName === ''
) {
    header('Location: ../register.php');
    exit;
}

$roleStmt = $conn->prepare('SELECT id FROM roles WHERE nombre = :nombre LIMIT 1');
$roleStmt->execute(['nombre' => $roleName]);
$roleId = $roleStmt->fetchColumn();

if ($roleId === false) {
    header('Location: ../register.php');
    exit;
}

$userStmt = $conn->prepare(
    'INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol_id)
     VALUES (:nombre, :apellidos, :email, :password_hash, :rol_id)'
);

try {
    $userStmt->execute([
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'rol_id' => $roleId,
    ]);
} catch (PDOException $exception) {
    if ($exception->errorInfo[1] === 1062) {
        header('Location: ../register.php');
        exit;
    }

    throw $exception;
}

header('Location: ../index.php');
exit;
