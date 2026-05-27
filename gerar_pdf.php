<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'config.php';

$ativos = $pdo->query(
    "SELECT a.id_ativo, a.nome_ativo, a.tipo, f.nome AS responsavel
     FROM ativos a
     JOIN funcionarios f ON a.fk_responsavel = f.id_funcionario
     ORDER BY a.id_ativo"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Ativos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0.5cm; }
        }
    </style>
</head>
<body class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h2>Relatório de Ativos</h2>
            <p class="mb-0">Use o recurso de impressão do navegador para salvar como PDF.</p>
        </div>
        <div>
            <button class="btn btn-primary me-2" onclick="window.print()">Gerar PDF</button>
            <a href="index.php" class="btn btn-secondary">Voltar</a>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ativo</th>
                <th>Tipo</th>
                <th>Responsável</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($ativos)): ?>
                <tr>
                    <td colspan="4" class="text-center">Nenhum ativo encontrado.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($ativos as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['id_ativo']) ?></td>
                        <td><?= htmlspecialchars($a['nome_ativo']) ?></td>
                        <td><?= htmlspecialchars($a['tipo']) ?></td>
                        <td><?= htmlspecialchars($a['responsavel']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
