<?php
function redirigirError(int $codigo): void
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
$confirmPassword = isset($_POST['confirm_password']) && is_string($_POST['confirm_password'])
    ? $_POST['confirm_password']
    : '';

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
