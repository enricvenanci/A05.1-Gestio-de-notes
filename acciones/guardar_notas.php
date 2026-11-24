<?php
session_start();

function volver(int $codigo, int $asignatura = 0)
{
    $url = '../pages/introducir_notas.php?error=' . $codigo;
    if ($asignatura > 0) {
        $url .= '&asignatura=' . $asignatura;
    }
    header('Location: ' . $url);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    volver(1);
}

if (!isset($_SESSION['user_id'], $_SESSION['user_role']) || $_SESSION['user_role'] !== 'profesor') {
    header('Location: ../index.php?error=1');
    exit;
}

$asignaturaId = isset($_POST['asignatura_id']) && is_string($_POST['asignatura_id'])
    ? filter_var($_POST['asignatura_id'], FILTER_VALIDATE_INT)
    : false;
if ($asignaturaId === false || $asignaturaId <= 0) {
    volver(1);
}

if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token'])
    || !is_string($_POST['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    volver(1, $asignaturaId);
}

if (!isset($_POST['notas']) || !is_array($_POST['notas']) || !$_POST['notas']) {
    volver(1, $asignaturaId);
}

include '../conexion/conexion.php';

try {
    $asignaturaStmt = $conn->prepare(
        'SELECT a.id
         FROM asignatura_profesor ap
         INNER JOIN asignaturas a ON a.id = ap.asignatura_id
         INNER JOIN usuaris u ON u.id = ap.profesor_id
         WHERE ap.profesor_id = :profesor
           AND a.id = :asignatura
           AND u.rol = :rol
         LIMIT 1'
    );
    $asignaturaStmt->execute([
        'profesor' => (int) $_SESSION['user_id'],
        'asignatura' => $asignaturaId,
        'rol' => 'profesor',
    ]);
    $asignatura = $asignaturaStmt->fetch(PDO::FETCH_ASSOC);
    if (!$asignatura) {
        volver(2, $asignaturaId);
    }

    $promocionStmt = $conn->prepare(
        'SELECT id FROM promociones WHERE actual = :actual LIMIT 1'
    );
    $promocionStmt->execute(['actual' => 1]);
    $promocion = $promocionStmt->fetch(PDO::FETCH_ASSOC);
    if (!$promocion) {
        volver(3, $asignaturaId);
    }

    $matriculasStmt = $conn->prepare(
        'SELECT m.id, m.alumno_id
         FROM matriculas m
         INNER JOIN usuaris u ON u.id = m.alumno_id
         WHERE m.asignatura_id = :asignatura
           AND m.promocion_id = :promocion
           AND u.rol = :rol'
    );
    $matriculasStmt->execute([
        'asignatura' => $asignaturaId,
        'promocion' => (int) $promocion['id'],
        'rol' => 'alumno',
    ]);
    $matriculas = $matriculasStmt->fetchAll(PDO::FETCH_ASSOC);

    $matriculaPorAlumno = [];
    foreach ($matriculas as $matricula) {
        $matriculaPorAlumno[(int) $matricula['alumno_id']] = (int) $matricula['id'];
    }

    $notasValidadas = [];
    foreach ($_POST['notas'] as $alumnoId => $nota) {
        $alumnoId = filter_var((string) $alumnoId, FILTER_VALIDATE_INT);
        if (
            $alumnoId === false
            || $alumnoId <= 0
            || !array_key_exists($alumnoId, $matriculaPorAlumno)
            || !is_string($nota)
        ) {
            volver(1, $asignaturaId);
        }

        $nota = trim($nota);
        if ($nota !== '' && !preg_match('/^(?:10(?:\.0{1,2})?|[0-9](?:\.\d{1,2})?)$/', $nota)) {
            volver(1, $asignaturaId);
        }
        $notasValidadas[] = [
            'matricula_id' => $matriculaPorAlumno[$alumnoId],
            'nota' => $nota === '' ? null : $nota,
        ];
    }

    $conn->beginTransaction();
    $updateStmt = $conn->prepare('UPDATE matriculas SET nota = :nota WHERE id = :id');
    foreach ($notasValidadas as $notaValidada) {
        $updateStmt->execute([
            'nota' => $notaValidada['nota'],
            'id' => $notaValidada['matricula_id'],
        ]);
    }
    $conn->commit();
} catch (PDOException $exception) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log('guardar_notas.php: ' . $exception->getMessage());
    volver(4, $asignaturaId);
}

header('Location: ../pages/introducir_notas.php?asignatura=' . $asignaturaId . '&ok=1');
exit;
