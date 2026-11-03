<?php
session_start();

/**
 * Comprueba que la petición viene de la página indicada.
 * Si el navegador no envía Referer se permite, porque no se puede comprobar.
 */
function viene_de(string $pagina): bool
{
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    if ($referer === '') {
        return true;
    }

    $partes      = parse_url($referer);
    $hostReferer = $partes['host'] ?? '';
    $hostActual  = explode(':', $_SERVER['HTTP_HOST'] ?? '')[0];
    $paginaRef   = basename($partes['path'] ?? '');

    return $hostReferer === $hostActual && $paginaRef === $pagina;
}

// 1. Solo se entra por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../bienvenida.php');
    exit;
}

// 2. Hay que haber iniciado sesión (lo fija proc_login.php)
if (empty($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// 3. La petición debe venir de bienvenida.php
if (!viene_de('bienvenida.php')) {
    header('Location: ../bienvenida.php');
    exit;
}

// 4. Acción permitida: eliminar alumno
if (($_POST['accion'] ?? '') !== 'eliminar') {
    header('Location: ../bienvenida.php');
    exit;
}

$id = (isset($_POST['id']) && ctype_digit($_POST['id'])) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    $_SESSION['flash_error'] = 'Alumno no válido.';
    header('Location: ../bienvenida.php');
    exit;
}

require_once __DIR__ . '/../conexion/conexion.php';

try {
    $stmt = $conn->prepare('SELECT nombre, apellidos FROM alumnos WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alumno) {
        $_SESSION['flash_error'] = 'El alumno ya no existe.';
    } else {
        // Sus notas se borran solas (ON DELETE CASCADE en la tabla notas)
        $del = $conn->prepare('DELETE FROM alumnos WHERE id = :id');
        $del->execute(['id' => $id]);
        $_SESSION['flash_ok'] = 'Alumno eliminado: ' . $alumno['nombre'] . ' ' . $alumno['apellidos'] . '.';
    }
} catch (PDOException $e) {
    error_log('proc_alumnos.php: ' . $e->getMessage());
    $_SESSION['flash_error'] = 'No se pudo eliminar al alumno. Inténtalo de nuevo.';
}

header('Location: ../bienvenida.php');
exit;