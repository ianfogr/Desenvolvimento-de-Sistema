<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'config.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

$funcionarios = $pdo->query("SELECT id_funcionario, nome FROM funcionarios ORDER BY nome")->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM ativos WHERE id_ativo = :id");
$stmt->execute([':id' => $id]);
$ativo = $stmt->fetch();
if (!$ativo) {
    header('Location: index.php');
    exit();
}

$nome_ativo = $_POST['nome_ativo'] ?? $ativo['nome_ativo'];
$tipo = $_POST['tipo'] ?? $ativo['tipo'];
$fk_responsavel = $_POST['fk_responsavel'] ?? $ativo['fk_responsavel'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$nome_ativo) {
        $errors[] = 'O nome do ativo é obrigatório.';
    }
    if (!$tipo) {
        $errors[] = 'O tipo do ativo é obrigatório.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE ativos SET nome_ativo = :nome, tipo = :tipo, fk_responsavel = :resp WHERE id_ativo = :id");
        $stmt->execute([
            ':nome' => $nome_ativo,
            ':tipo' => $tipo,
            ':resp' => $fk_responsavel ?: null,
            ':id' => $id,
        ]);

        header('Location: index.php?msg=Ativo+atualizado+com+sucesso');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Ativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Editar Ativo</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <div class="mb-3">
            <label class="form-label">Nome do ativo</label>
            <input type="text" name="nome_ativo" class="form-control" value="<?= htmlspecialchars($nome_ativo) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo</label>
            <input type="text" name="tipo" class="form-control" value="<?= htmlspecialchars($tipo) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Responsável</label>
            <select name="fk_responsavel" class="form-select">
                <option value="">Selecione</option>
                <?php foreach ($funcionarios as $func): ?>
                    <option value="<?= $func['id_funcionario'] ?>"<?= $func['id_funcionario'] == $fk_responsavel ? ' selected' : '' ?>><?= htmlspecialchars($func['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </form>
</body>
</html>
