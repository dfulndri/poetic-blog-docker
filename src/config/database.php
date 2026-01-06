<?php
// Mengambil variabel dari .env (Docker)
$host = getenv('DB_HOST') ?: 'mysql';
$db   = getenv('DB_NAME') ?: 'poetic_db';
$user = getenv('DB_USER') ?: 'poetic_user';
$pass = getenv('DB_PASS') ?: 'secret';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
