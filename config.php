<?php
$host = 'localhost';
$db   = 'safeaudit';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Mail / Resend configuration
// Set the environment variables RESEND_API_KEY and MAIL_FROM in your server
$RESEND_API_KEY = getenv('RESEND_API_KEY') ?: '';
$MAIL_FROM = getenv('MAIL_FROM') ?: 'no-reply@safeaudit.com';
?>