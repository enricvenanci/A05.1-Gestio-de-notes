<?php
$servername = '127.0.0.1';
$dbname = 'gestio_notes';
$dbusername = 'root';
$dbpassword = '';

try {
    $conn = new PDO(
        "mysql:host=$servername;dbname=$dbname;charset=utf8mb4",
        $dbusername,
        $dbpassword
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('No se pudo conectar con la base de datos.');
}
