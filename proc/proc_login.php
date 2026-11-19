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
