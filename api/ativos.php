<?php
// API REST para Ativos
header('Content-Type: application/json');

session_start();

// Validar autenticação
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit();
}

require '../config.php';

$metodo = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null;

try {
    switch ($metodo) {
        case 'GET':
            if ($id) {
                // Obter um ativo específico
                $stmt = $pdo->prepare(
                    "SELECT a.*, f.nome as responsavel, d.nome_departamento, c.nome_categoria 
                     FROM ativos a
                     LEFT JOIN funcionarios f ON a.fk_responsavel = f.id_funcionario
                     LEFT JOIN departamentos d ON a.fk_departamento = d.id_departamento
                     LEFT JOIN categorias c ON a.fk_categoria = c.id_categoria
                     WHERE a.id_ativo = :id"
                );
                $stmt->execute([':id' => $id]);
                $ativo = $stmt->fetch();
                
                if (!$ativo) {
                    http_response_code(404);
                    echo json_encode(['erro' => 'Ativo não encontrado']);
                } else {
                    echo json_encode($ativo);
                }
            } else {
                // Listar todos os ativos
                $sql = "SELECT a.*, f.nome as responsavel, d.nome_departamento, c.nome_categoria
                        FROM ativos a
                        LEFT JOIN funcionarios f ON a.fk_responsavel = f.id_funcionario
                        LEFT JOIN departamentos d ON a.fk_departamento = d.id_departamento
                        LEFT JOIN categorias c ON a.fk_categoria = c.id_categoria
                        ORDER BY a.id_ativo";
                
                $ativos = $pdo->query($sql)->fetchAll();
                echo json_encode($ativos);
            }
            break;

        case 'POST':
            $dados = json_decode(file_get_contents('php://input'), true);
            
            if (!$dados || !isset($dados['nome_ativo']) || !isset($dados['tipo']) || !isset($dados['fk_responsavel'])) {
                http_response_code(400);
                echo json_encode(['erro' => 'Dados inválidos']);
                exit();
            }

            $stmt = $pdo->prepare(
                "INSERT INTO ativos (nome_ativo, tipo, numero_patrimonio, data_aquisicao, fk_responsavel, fk_departamento, fk_categoria, status, observacoes)
                 VALUES (:nome, :tipo, :patrimonio, :data, :resp, :depart, :categ, :status, :obs)"
            );

            $stmt->execute([
                ':nome' => $dados['nome_ativo'],
                ':tipo' => $dados['tipo'],
                ':patrimonio' => $dados['numero_patrimonio'] ?? null,
                ':data' => $dados['data_aquisicao'] ?? null,
                ':resp' => $dados['fk_responsavel'],
                ':depart' => $dados['fk_departamento'] ?? null,
                ':categ' => $dados['fk_categoria'] ?? null,
                ':status' => $dados['status'] ?? 'ativo',
                ':obs' => $dados['observacoes'] ?? null,
            ]);

            http_response_code(201);
            echo json_encode(['id_ativo' => $pdo->lastInsertId()]);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID não fornecido']);
                exit();
            }

            $dados = json_decode(file_get_contents('php://input'), true);

            $stmt = $pdo->prepare(
                "UPDATE ativos SET nome_ativo = :nome, tipo = :tipo, numero_patrimonio = :patrimonio, 
                 data_aquisicao = :data, fk_responsavel = :resp, fk_departamento = :depart, 
                 fk_categoria = :categ, status = :status, observacoes = :obs
                 WHERE id_ativo = :id"
            );

            $stmt->execute([
                ':nome' => $dados['nome_ativo'] ?? null,
                ':tipo' => $dados['tipo'] ?? null,
                ':patrimonio' => $dados['numero_patrimonio'] ?? null,
                ':data' => $dados['data_aquisicao'] ?? null,
                ':resp' => $dados['fk_responsavel'] ?? null,
                ':depart' => $dados['fk_departamento'] ?? null,
                ':categ' => $dados['fk_categoria'] ?? null,
                ':status' => $dados['status'] ?? null,
                ':obs' => $dados['observacoes'] ?? null,
                ':id' => $id,
            ]);

            http_response_code(200);
            echo json_encode(['mensagem' => 'Ativo atualizado com sucesso']);
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(['erro' => 'ID não fornecido']);
                exit();
            }

            $stmt = $pdo->prepare("DELETE FROM ativos WHERE id_ativo = :id");
            $stmt->execute([':id' => $id]);

            http_response_code(200);
            echo json_encode(['mensagem' => 'Ativo excluído com sucesso']);
            break;

        default:
            http_response_code(405);
            echo json_encode(['erro' => 'Método não permitido']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['erro' => $e->getMessage()]);
}
?>
