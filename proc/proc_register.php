<?php
include '../conexion/conexion.php';

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

// ---------- Nombre ----------
if ($nombre === '') {
    error('El nombre es obligatorio.');
}
if (mb_strlen($nombre) < 2) {
    error('El nombre debe tener al menos 2 letras.');
}
if (preg_match('/[0-9]/', $nombre)) {
    error('El nombre no puede contener números.');
}

// ---------- Apellidos ----------
if ($apellidos === '') {
    error('Los apellidos son obligatorios.');
}
if (mb_strlen($apellidos) < 2) {
    error('Los apellidos deben tener al menos 2 letras.');
}
if (preg_match('/[0-9]/', $apellidos)) {
    error('Los apellidos no pueden contener números.');
}

// ---------- Correo ----------
if ($email === '') {
    error('El correo es obligatorio.');
}
// Mismo formato que en JavaScript: texto @ texto . al menos 2 letras
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/', $email)) {
    error('El correo electrónico no es válido.');
}

// ---------- Contraseña ----------
if ($password === '') {
    error('La contraseña es obligatoria.');
}
if (preg_match_all('/\p{L}/u', $password) < 2) {
    error('La contraseña debe tener al menos 2 letras.');
}
if (!preg_match('/\p{Lu}/u', $password)) {
    error('La contraseña debe tener al menos 1 mayúscula.');
}
if (!preg_match('/[0-9]/', $password)) {
    error('La contraseña debe tener al menos 1 número.');
}
if ($password !== $confirmPassword) {
    error('Las contraseñas no coinciden.');
}

// ---------- Rol ----------
if ($roleId === false) {
    error('Selecciona un rol.');
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