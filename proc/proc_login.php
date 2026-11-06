<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

include '../conexion/conexion.php';

// Deja los errores (por campo) y el usuario escrito, y vuelve al mismo formulario
function volverConErrores(array $errores, string $username): void
{
    $_SESSION['login_errors'] = $errores;
    $_SESSION['login_old'] = $username;
    header('Location: ../index.php');
    exit;
}

$username = isset($_POST['username']) && is_string($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) && is_string($_POST['password']) ? $_POST['password'] : '';

// ---------- Validaciones PHP (servidor): se revisan los dos campos a la vez ----------
$errores = [];

if ($username === '') {
    $errores['username'] = 'Introduce tu nombre.';
} elseif (mb_strlen($username) > 100) {
    $errores['username'] = 'El nombre es demasiado largo.';
}

if ($password === '') {
    $errores['password'] = 'La contraseña es obligatoria.';
} elseif (strlen($password) > 255) {
    $errores['password'] = 'La contraseña es demasiado larga.';
}

if ($errores) {
    volverConErrores($errores, $username);
}

// ---------- Consulta preparada (evita inyección SQL) ----------
$stmt = $conn->prepare(
    'SELECT u.id, u.nombre, u.apellidos, u.password_hash, r.nombre AS rol
     FROM usuarios u
     INNER JOIN roles r ON r.id = u.rol_id
     WHERE u.nombre = :nombre OR u.email = :email
     LIMIT 1'
);
$stmt->execute([
    'nombre' => $username,
    'email' => $username,
]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ---------- Usuario no registrado o contraseña incorrecta ----------
// Mensaje genérico a propósito: no revela si el usuario existe o no.
if (!$user || !password_verify($password, $user['password_hash'])) {
    volverConErrores(['general' => 'Usuario o contraseña incorrectos.'], $username);
}

// ---------- Login correcto: crear la sesión ----------
session_regenerate_id(true);
unset($_SESSION['login_errors'], $_SESSION['login_old']);
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['nombre'];
$_SESSION['user_full_name'] = $user['nombre'] . ' ' . $user['apellidos'];
$_SESSION['user_role'] = $user['rol'];   // cargo: admin, profesor, educador, secretaria

header('Location: ../bienvenida.php');
exit;
