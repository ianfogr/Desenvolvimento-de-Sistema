<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

$stmt = $pdo->prepare("DELETE FROM ativos WHERE id_ativo = :id");
$stmt->execute([':id' => $id]);

header('Location: index.php?msg=Ativo+exclu%C3%ADdo+com+sucesso');
exit();
