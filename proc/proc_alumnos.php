<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../bienvenida.php');
    exit;
}

if (
    !isset($_SESSION['user_id'], $_SESSION['user_role'])
    || $_SESSION['user_role'] !== 'admin'
) {
    header('Location: ../index.php?error=1');
    exit;
}

$id = isset($_POST['id']) && is_string($_POST['id'])
    ? filter_var($_POST['id'], FILTER_VALIDATE_INT)
    : false;
if ($id === false || $id <= 0) {
    header('Location: ../bienvenida.php?error=2');
    exit;
}
