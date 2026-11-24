<?php
function redirigirError(int $codigo)
{
    header('Location: ../register.php?error=' . $codigo);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$nombre = isset($_POST['nombre']) && is_string($_POST['nombre']) ? trim($_POST['nombre']) : '';
$apellidos = isset($_POST['apellidos']) && is_string($_POST['apellidos']) ? trim($_POST['apellidos']) : '';
$email = isset($_POST['email']) && is_string($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirm_password']) && is_string($_POST['confirm_password'])? $_POST['confirm_password']: '';

// ---------- Nombre ----------
if ($nombre === '') {
    redirigirError(1);
}
if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 50) {
    redirigirError(1);
}
if (preg_match('/[0-9]/', $nombre)) {
    redirigirError(1);
}

// ---------- Apellidos ----------
if ($apellidos === '') {
    redirigirError(1);
}
if (mb_strlen($apellidos) < 2 || mb_strlen($apellidos) > 100) {
    redirigirError(1);
}
if (preg_match('/[0-9]/', $apellidos)) {
    redirigirError(1);
}

// ---------- Correo ----------
if ($email === '') {
    redirigirError(2);
}
if (
    mb_strlen($email) > 100
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/', $email)
) {
    redirigirError(2);
}

// ---------- Contraseña ----------
if ($password === '') {
    redirigirError(3);
}
if (preg_match_all('/\p{L}/u', $password) < 2) {
    redirigirError(3);
}
if (!preg_match('/\p{Lu}/u', $password)) {
    redirigirError(3);
}
if (!preg_match('/[0-9]/', $password)) {
    redirigirError(3);
}
if ($password !== $confirmPassword) {
    redirigirError(4);
}

include '../conexion/conexion.php';

$userStmt = $conn->prepare(
    'INSERT INTO usuaris (nombre, apellidos, email, password_hash, rol)
     VALUES (:nombre, :apellidos, :email, :password_hash, :rol)'
);

try {
    $userStmt->execute([
        'nombre' => $nombre,
        'apellidos' => $apellidos,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'rol' => 'alumno',
    ]);
} catch (PDOException $exception) {
    if (($exception->errorInfo[1] ?? null) === 1062) {
        redirigirError(5);
    }
    error_log('proc_register.php: ' . $exception->getMessage());
    redirigirError(6);
}

header('Location: ../index.php');
exit;
