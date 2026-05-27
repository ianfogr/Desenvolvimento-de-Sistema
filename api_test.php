<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}
require 'config.php';

$funcionarios = $pdo->query("SELECT id_funcionario, nome FROM funcionarios ORDER BY nome")->fetchAll();
$departamentos = $pdo->query("SELECT id_departamento, nome_departamento FROM departamentos ORDER BY nome_departamento")->fetchAll();
$categorias = $pdo->query("SELECT id_categoria, nome_categoria FROM categorias ORDER BY nome_categoria")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teste da API - SafeAudit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .response-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 0.9em;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .method-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: bold;
            margin-right: 10px;
        }
        .get { background: #0d6efd; color: white; }
        .post { background: #198754; color: white; }
        .put { background: #ffc107; color: black; }
        .delete { background: #dc3545; color: white; }
    </style>
</head>
<body class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Teste da API REST</h2>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Operações CRUD</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label class="form-label"><span class="method-badge get">GET</span>Listar Ativos</label>
                        <button class="btn btn-sm btn-primary" onclick="listarAtivos()">Testar</button>
                    </div>

                    <div class="mb-4">
                        <label class="form-label"><span class="method-badge get">GET</span>Obter Ativo por ID</label>
                        <input type="number" id="getAtivosId" class="form-control mb-2" placeholder="ID do ativo" value="1">
                        <button class="btn btn-sm btn-primary" onclick="obterAtivo()">Testar</button>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="form-label"><span class="method-badge post">POST</span>Criar Ativo</label>
                        <input type="text" id="criarNome" class="form-control mb-2" placeholder="Nome do ativo" value="Novo Ativo Teste">
                        <input type="text" id="criarTipo" class="form-control mb-2" placeholder="Tipo" value="Notebook">
                        <input type="text" id="criarPatrimonio" class="form-control mb-2" placeholder="Número de patrimônio" value="PAT-999">
                        <select id="criarResponsavel" class="form-select mb-2">
                            <option value="">Selecione responsável</option>
                            <?php foreach ($funcionarios as $f): ?>
                                <option value="<?= $f['id_funcionario'] ?>"><?= htmlspecialchars($f['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="criarDepartamento" class="form-select mb-2">
                            <option value="">Selecione departamento (opcional)</option>
                            <?php foreach ($departamentos as $d): ?>
                                <option value="<?= $d['id_departamento'] ?>"><?= htmlspecialchars($d['nome_departamento']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="criarCategoria" class="form-select mb-2">
                            <option value="">Selecione categoria (opcional)</option>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id_categoria'] ?>"><?= htmlspecialchars($c['nome_categoria']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-success" onclick="criarAtivo()">Testar</button>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="form-label"><span class="method-badge put">PUT</span>Atualizar Ativo</label>
                        <input type="number" id="updateId" class="form-control mb-2" placeholder="ID do ativo" value="1">
                        <input type="text" id="updateNome" class="form-control mb-2" placeholder="Novo nome (opcional)">
                        <input type="text" id="updateStatus" class="form-control mb-2" placeholder="Status (ativo/manutenção/inativo)" value="ativo">
                        <button class="btn btn-sm btn-warning" onclick="atualizarAtivo()">Testar</button>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <label class="form-label"><span class="method-badge delete">DELETE</span>Excluir Ativo</label>
                        <input type="number" id="deleteId" class="form-control mb-2" placeholder="ID do ativo">
                        <button class="btn btn-sm btn-danger" onclick="excluirAtivo()">Testar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Resposta da API</h5>
                </div>
                <div class="card-body">
                    <div id="response" class="response-box">Nenhuma requisição feita ainda...</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const apiUrl = 'api/ativos.php';
        const responseDiv = document.getElementById('response');

        function mostrarResposta(data, status = 'OK') {
            responseDiv.textContent = `[${status}]\n\n${JSON.stringify(data, null, 2)}`;
        }

        async function listarAtivos() {
            try {
                const response = await fetch(apiUrl);
                const data = await response.json();
                mostrarResposta(data, `GET 200 - Listar Ativos`);
            } catch (error) {
                mostrarResposta({ erro: error.message }, 'ERRO');
            }
        }

        async function obterAtivo() {
            const id = document.getElementById('getAtivosId').value;
            if (!id) {
                alert('Forneça um ID');
                return;
            }
            try {
                const response = await fetch(`${apiUrl}?id=${id}`);
                const data = await response.json();
                mostrarResposta(data, `GET 200 - Obter Ativo ${id}`);
            } catch (error) {
                mostrarResposta({ erro: error.message }, 'ERRO');
            }
        }

        async function criarAtivo() {
            const nome = document.getElementById('criarNome').value;
            const tipo = document.getElementById('criarTipo').value;
            const patrimonio = document.getElementById('criarPatrimonio').value;
            const responsavel = document.getElementById('criarResponsavel').value;
            const departamento = document.getElementById('criarDepartamento').value;
            const categoria = document.getElementById('criarCategoria').value;

            if (!nome || !tipo || !responsavel) {
                alert('Nome, tipo e responsável são obrigatórios');
                return;
            }

            const dados = {
                nome_ativo: nome,
                tipo: tipo,
                numero_patrimonio: patrimonio,
                fk_responsavel: parseInt(responsavel),
                fk_departamento: departamento ? parseInt(departamento) : null,
                fk_categoria: categoria ? parseInt(categoria) : null
            };

            try {
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });
                const data = await response.json();
                mostrarResposta(data, `POST 201 - Ativo Criado`);
            } catch (error) {
                mostrarResposta({ erro: error.message }, 'ERRO');
            }
        }

        async function atualizarAtivo() {
            const id = document.getElementById('updateId').value;
            const nome = document.getElementById('updateNome').value;
            const status = document.getElementById('updateStatus').value;

            if (!id) {
                alert('Forneça um ID');
                return;
            }

            const dados = {};
            if (nome) dados.nome_ativo = nome;
            if (status) dados.status = status;

            try {
                const response = await fetch(`${apiUrl}?id=${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dados)
                });
                const data = await response.json();
                mostrarResposta(data, `PUT 200 - Ativo ${id} Atualizado`);
            } catch (error) {
                mostrarResposta({ erro: error.message }, 'ERRO');
            }
        }

        async function excluirAtivo() {
            const id = document.getElementById('deleteId').value;
            if (!id) {
                alert('Forneça um ID');
                return;
            }

            if (!confirm(`Tem certeza que deseja excluir o ativo ${id}?`)) {
                return;
            }

            try {
                const response = await fetch(`${apiUrl}?id=${id}`, {
                    method: 'DELETE'
                });
                const data = await response.json();
                mostrarResposta(data, `DELETE 200 - Ativo ${id} Excluído`);
            } catch (error) {
                mostrarResposta({ erro: error.message }, 'ERRO');
            }
        }
    </script>
</body>
</html>
