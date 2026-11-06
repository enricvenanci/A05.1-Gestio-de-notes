<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

include '../conexion/conexion.php';

// Deja los errores (por campo) y los datos escritos, y vuelve al mismo formulario
function volverConErrores(array $errores, array $datos): void
{
    $_SESSION['register_errors'] = $errores;
    $_SESSION['register_old'] = $datos;   // nunca se guardan las contraseñas
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

$datos = [
    'nombre' => $nombre,
    'apellidos' => $apellidos,
    'email' => $email,
    'role_id' => $roleId === false ? '' : (string) $roleId,
];

// ---------- Validaciones PHP (servidor): se revisan todos los campos a la vez ----------
$errores = [];

// Nombre
if ($nombre === '') {
    $errores['nombre'] = 'El nombre es obligatorio.';
} elseif (mb_strlen($nombre) < 2) {
    $errores['nombre'] = 'El nombre debe tener al menos 2 letras.';
} elseif (preg_match('/[0-9]/', $nombre)) {
    $errores['nombre'] = 'El nombre no puede contener números.';
}

// Apellidos
if ($apellidos === '') {
    $errores['apellidos'] = 'Los apellidos son obligatorios.';
} elseif (mb_strlen($apellidos) < 2) {
    $errores['apellidos'] = 'Los apellidos deben tener al menos 2 letras.';
} elseif (preg_match('/[0-9]/', $apellidos)) {
    $errores['apellidos'] = 'Los apellidos no pueden contener números.';
}

// Correo (mismo formato que en JavaScript: texto @ texto . al menos 2 letras)
if ($email === '') {
    $errores['email'] = 'El correo es obligatorio.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/', $email)) {
    $errores['email'] = 'El correo electrónico no es válido.';
}

// Contraseña
if ($password === '') {
    $errores['password'] = 'La contraseña es obligatoria.';
} elseif (preg_match_all('/\p{L}/u', $password) < 2) {
    $errores['password'] = 'La contraseña debe tener al menos 2 letras.';
} elseif (!preg_match('/\p{Lu}/u', $password)) {
    $errores['password'] = 'La contraseña debe tener al menos 1 mayúscula.';
} elseif (!preg_match('/[0-9]/', $password)) {
    $errores['password'] = 'La contraseña debe tener al menos 1 número.';
}

// Confirmación
if ($confirmPassword === '') {
    $errores['confirm_password'] = 'Confirma tu contraseña.';
} elseif ($password !== $confirmPassword) {
    $errores['confirm_password'] = 'Las contraseñas no coinciden.';
}

// Rol: tiene que ser un número y existir en la tabla roles
if ($roleId === false) {
    $errores['role_id'] = 'Selecciona un rol.';
} else {
    $roleStmt = $conn->prepare('SELECT id FROM roles WHERE id = ?');
    $roleStmt->execute([$roleId]);
    $roleId = $roleStmt->fetchColumn();

    if ($roleId === false) {
        $errores['role_id'] = 'El rol seleccionado no existe.';
    }
}

if ($errores) {
    volverConErrores($errores, $datos);
}

// ---------- Inserción (consulta preparada + contraseña encriptada) ----------
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
    // 1062 = valor duplicado (el correo es UNIQUE)
    if (($exception->errorInfo[1] ?? null) === 1062) {
        volverConErrores(['email' => 'El correo electrónico ya está registrado.'], $datos);
    }

    throw $exception;
}

unset($_SESSION['register_errors'], $_SESSION['register_old']);
header('Location: ../index.php');
exit;
