<?php
require_once __DIR__ . '/../conexion/conexion.php';

function error(string $mensaje): void
{
    http_response_code(400);
    exit(htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') . '<br><a href="../register.php">Volver</a>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$nombre = isset($_POST['nombre']) && is_string($_POST['nombre']) ? trim($_POST['nombre']) : '';
$apellidos = isset($_POST['apellidos']) && is_string($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirm_password']) && is_string($_POST['confirm_password'])
    ? $_POST['confirm_password']
    : '';
$roleId = filter_var($_POST['role_id'] ?? '', FILTER_VALIDATE_INT);

if (
    $nombre === '' ||
    $apellidos === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $password === '' ||
    $password !== $confirmPassword ||
    $roleId === false
) {
    error('Completa todos los campos correctamente y asegúrate de que las contraseñas coincidan.');
}

$roleStmt = $conn->prepare('SELECT id FROM roles WHERE id = ?');
$roleStmt->execute([$roleId]);
$roleId = $roleStmt->fetchColumn();

if ($roleId === false) {
    error('El rol seleccionado no existe.');
}

$userStmt = $conn->prepare(
    'INSERT INTO usuarios (nombre, apellidos, email, password_hash, rol_id) VALUES (?, ?, ?, ?, ?)'
);

try {
    $userStmt->execute([
        $nombre,
        $apellidos,
        $email,
        password_hash($password, PASSWORD_DEFAULT),
        $roleId,
    ]);
} catch (PDOException $exception) {
    if (($exception->errorInfo[1] ?? null) === 1062) {
        error('El correo electrónico ya está registrado.');
    }

    throw $exception;
}

header('Location: ../index.php');
exit;
