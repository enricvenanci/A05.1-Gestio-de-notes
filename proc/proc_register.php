<?php
session_start();
require_once __DIR__ . '/../conexion/conexion.php';

function redirectToRegister(string $error): never
{
    $_SESSION['register_error'] = $error;
    header('Location: ../register.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['roles'])) {
    header('Content-Type: application/json; charset=utf-8');

    $roleStmt = $conn->query('SELECT id, nombre FROM roles ORDER BY nombre');
    echo json_encode($roleStmt->fetchAll(PDO::FETCH_ASSOC), JSON_THROW_ON_ERROR);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$roleId = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);

if (
    $nombre === '' ||
    $apellidos === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $password === '' ||
    $password !== $confirmPassword ||
    $roleId === false ||
    $roleId === null
) {
    redirectToRegister('Completa todos los campos correctamente y asegúrate de que las contraseñas coincidan.');
}

$roleStmt = $conn->prepare('SELECT id FROM roles WHERE id = :id LIMIT 1');
$roleStmt->execute(['id' => $roleId]);
$existingRoleId = $roleStmt->fetchColumn();

if ($existingRoleId === false) {
    redirectToRegister('El rol seleccionado no existe.');
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
        'rol_id' => $existingRoleId,
    ]);
} catch (PDOException $exception) {
    if ($exception->errorInfo[1] === 1062) {
        redirectToRegister('El correo electrónico ya está registrado.');
    }

    throw $exception;
}

header('Location: ../index.php');
exit;
