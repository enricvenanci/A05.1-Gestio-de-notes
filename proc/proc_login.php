<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

if (
    !isset($_POST['username'], $_POST['password'])
    || !is_string($_POST['username'])
    || !is_string($_POST['password'])
) {
    header('Location: ../index.php?error=1');
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];
if ($username === '' || $password === '') {
    header('Location: ../index.php?error=1');
    exit;
}

include '../sql/conexion/conexion.php';

try {
    $stmt = $conn->prepare(
        'SELECT id, nombre, apellidos, email, password_hash, rol
         FROM usuaris
         WHERE nombre = :nombre OR email = :email
         LIMIT 1'
    );
    $stmt->execute([
        'nombre' => $username,
        'email' => $username,
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log('proc_login.php: ' . $exception->getMessage());
    header('Location: ../index.php?error=1');
    exit;
}

if (
    !$user
    || $user['password_hash'] === null
    || !password_verify($password, $user['password_hash'])
) {
    header('Location: ../index.php?error=1');
    exit;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['user_role'] = $user['rol'];
$_SESSION['user_name'] = $user['nombre'];
$_SESSION['user_full_name'] = $user['nombre'] . ' ' . $user['apellidos'];
$_SESSION['user_email'] = $user['email'];

if ($user['rol'] === 'alumno') {
    header('Location: ../notas.php');
    exit;
}

header('Location: ../bienvenida.php');
exit;
