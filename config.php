<?php
$host = 'localhost';
$db   = 'safeaudit';
$user = 'postgres'; // Ou root para MySQL
$pass = 'senha';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>