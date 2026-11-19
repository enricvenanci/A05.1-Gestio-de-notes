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
