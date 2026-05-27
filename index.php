<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

require 'config.php';

// Filtros
$busca_nome = $_GET['busca_nome'] ?? '';
$filtro_tipo = $_GET['filtro_tipo'] ?? '';
$filtro_responsavel = $_GET['filtro_responsavel'] ?? '';

$funcionarios = $pdo->query("SELECT id_funcionario, nome FROM funcionarios ORDER BY nome")->fetchAll();

$sql = "SELECT a.*, f.nome as responsavel FROM ativos a 
        JOIN funcionarios f ON a.fk_responsavel = f.id_funcionario 
        WHERE LOWER(a.nome_ativo) LIKE LOWER(:nome)";

if ($filtro_tipo) $sql .= " AND a.tipo = :tipo";
if ($filtro_responsavel) $sql .= " AND a.fk_responsavel = :resp";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':nome', "%$busca_nome%");
if ($filtro_tipo) $stmt->bindValue(':tipo', $filtro_tipo);
if ($filtro_responsavel) $stmt->bindValue(':resp', $filtro_responsavel);
$stmt->execute();
$ativos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestão de Ativos</h2>
        <div>
            <span class="me-3">Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?> (<?= htmlspecialchars($_SESSION['usuario_perfil']) ?>)</span>
            <a href="logout.php" class="btn btn-sm btn-danger">Sair</a>
        </div>
    </div>

    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <form class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="busca_nome" class="form-control" placeholder="Nome do ativo..." value="<?= htmlspecialchars($busca_nome) ?>">
        </div>
        <div class="col-md-2">
            <select name="filtro_tipo" class="form-select">
                <option value="">Todos os Tipos</option>
                <option value="Servidor"<?= $filtro_tipo === 'Servidor' ? ' selected' : '' ?>>Servidor</option>
                <option value="Notebook"<?= $filtro_tipo === 'Notebook' ? ' selected' : '' ?>>Notebook</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="filtro_responsavel" class="form-select">
                <option value="">Todos os Responsáveis</option>
                <?php foreach ($funcionarios as $func): ?>
                    <option value="<?= $func['id_funcionario'] ?>"<?= $func['id_funcionario'] == $filtro_responsavel ? ' selected' : '' ?>><?= htmlspecialchars($func['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
        </div>
        <div class="col-md-2">
            <a href="criar.php" class="btn btn-success w-100">Cadastrar</a>
        </div>
        <div class="col-md-1">
            <a href="gerar_pdf.php" class="btn btn-danger w-100">PDF</a>
        </div>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ativo</th>
                <th>Tipo</th>
                <th>Responsável</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ativos as $a): ?>
            <tr>
                <td><?= $a['id_ativo'] ?></td>
                <td><?= htmlspecialchars($a['nome_ativo']) ?></td>
                <td><?= $a['tipo'] ?></td>
                <td><?= $a['responsavel'] ?></td>
                <td>
                    <a href="editar.php?id=<?= $a['id_ativo'] ?>" class="btn btn-sm btn-warning">Editar</a>
                    <a href="deletar.php?id=<?= $a['id_ativo'] ?>" class="btn btn-sm btn-danger">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>