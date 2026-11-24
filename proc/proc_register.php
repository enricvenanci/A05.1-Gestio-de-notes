<?php
function redirigirError(int $codigo): void
{
    header('Location: ../register.php?error=' . $codigo);
    exit;
}

function contieneDigito(string $valor): bool
{
    return strpbrk($valor, '0123456789') !== false;
}

function analizarLetras(string $valor): array
{
    $cantidadLetras = 0;
    $tieneMayuscula = false;
    $longitud = mb_strlen($valor, 'UTF-8');

    for ($indice = 0; $indice < $longitud; $indice++) {
        $caracter = mb_substr($valor, $indice, 1, 'UTF-8');
        $mayuscula = mb_strtoupper($caracter, 'UTF-8');
        $minuscula = mb_strtolower($caracter, 'UTF-8');

        if ($mayuscula !== $minuscula) {
            $cantidadLetras++;
        }
        if ($mayuscula === $caracter && $minuscula !== $caracter) {
            $tieneMayuscula = true;
        }
    }

    return [$cantidadLetras, $tieneMayuscula];
}

function correoTieneFormatoValido(string $email): bool
{
    if (strpbrk($email, " \t\n\r\0\x0B\f") !== false) {
        return false;
    }

    $partes = explode('@', $email);
    if (count($partes) !== 2 || $partes[0] === '' || $partes[1] === '') {
        return false;
    }

    $ultimoPunto = strrpos($partes[1], '.');
    return $ultimoPunto !== false
        && $ultimoPunto > 0
        && strlen(substr($partes[1], $ultimoPunto + 1)) >= 2;
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
$rolesPermitidos = ['profesor', 'educador', 'secretaria', 'admin'];
$rol = isset($_POST['rol']) && is_string($_POST['rol']) ? $_POST['rol'] : '';

// ---------- Nombre ----------
if ($nombre === '') {
    redirigirError(1);
}
if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 50) {
    redirigirError(1);
}
if (contieneDigito($nombre)) {
    redirigirError(1);
}

// ---------- Apellidos ----------
if ($apellidos === '') {
    redirigirError(1);
}
if (mb_strlen($apellidos) < 2 || mb_strlen($apellidos) > 100) {
    redirigirError(1);
}
if (contieneDigito($apellidos)) {
    redirigirError(1);
}

// ---------- Correo ----------
if ($email === '') {
    redirigirError(2);
}
if (
    mb_strlen($email) > 100
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || !correoTieneFormatoValido($email)
) {
    redirigirError(2);
}

// ---------- Contraseña ----------
if ($password === '' || !mb_check_encoding($password, 'UTF-8')) {
    redirigirError(3);
}
[$cantidadLetras, $tieneMayuscula] = analizarLetras($password);
if ($cantidadLetras < 2) {
    redirigirError(3);
}
if (!$tieneMayuscula) {
    redirigirError(3);
}
if (!contieneDigito($password)) {
    redirigirError(3);
}
if ($password !== $confirmPassword) {
    redirigirError(4);
}

if (!in_array($rol, $rolesPermitidos, true)) {
    redirigirError(7);
}

include '../sql/conexion/conexion.php';

/** @var PDO $conn */
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
        'rol' => $rol,
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
