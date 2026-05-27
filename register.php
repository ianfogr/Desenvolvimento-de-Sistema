<?php
session_start();

// Se já está logado, redireciona para index
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

require 'config.php';

$erro = '';
$sucesso = '';
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    // Validações
    if (!$nome) {
        $erro = 'Nome é obrigatório.';
    } elseif (!$email) {
        $erro = 'Email é obrigatório.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = 'Email inválido.';
    } elseif (!$senha) {
        $erro = 'Senha é obrigatória.';
    } elseif (strlen($senha) < 6) {
        $erro = 'Senha deve ter no mínimo 6 caracteres.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'Senhas não conferem.';
    } else {
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $erro = 'Email já cadastrado no sistema.';
        } else {
            // Criar conta
            $senha_hash = password_hash($senha, PASSWORD_BCRYPT);
            $perfil = 'tecnico'; // Perfil padrão para novos usuários

            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO usuarios (nome, email, senha, perfil) VALUES (:nome, :email, :senha, :perfil)"
                );
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':senha' => $senha_hash,
                    ':perfil' => $perfil,
                ]);

                $sucesso = 'Conta criada com sucesso! Redirecionando para login...';
                header('refresh:2;url=login.php');
            } catch (Exception $e) {
                $erro = 'Erro ao criar conta: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - SafeAudit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .register-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        .register-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            width: 100%;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
        }
        .link-login {
            text-align: center;
            margin-top: 20px;
        }
        .link-login a {
            color: #667eea;
            text-decoration: none;
        }
        .link-login a:hover {
            text-decoration: underline;
        }
        .password-strength {
            font-size: 0.85rem;
            margin-top: 5px;
        }
        .strength-weak { color: #dc3545; }
        .strength-fair { color: #ffc107; }
        .strength-good { color: #28a745; }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Criar Conta</h2>

        <?php if ($erro): ?>
            <div class="alert alert-danger" role="alert"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="alert alert-success" role="alert"><?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome Completo</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($nome) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" minlength="6" required onkeyup="verificarForcaSenha()">
                <small id="force-indicator" class="password-strength"></small>
            </div>

            <div class="mb-4">
                <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" minlength="6" required>
            </div>

            <button type="submit" class="btn btn-register btn-primary mb-3">Criar Conta</button>
        </form>

        <div class="link-login">
            Já tem conta? <a href="login.php">Faça login aqui</a>
        </div>

        <hr>
        <p class="text-center text-muted small">
            <strong>Conta de teste:</strong><br>
            admin@safeaudit.com<br>
            admin123
        </p>
    </div>

    <script>
        function verificarForcaSenha() {
            const senha = document.getElementById('senha').value;
            const indicator = document.getElementById('force-indicator');

            if (!senha) {
                indicator.textContent = '';
                return;
            }

            let forcaTexto = '';
            let forcaClasse = '';
            let forcaPontos = 0;

            // Verificar comprimento
            if (senha.length >= 6) forcaPontos++;
            if (senha.length >= 10) forcaPontos++;
            if (senha.length >= 15) forcaPontos++;

            // Verificar tipos de caracteres
            if (/[a-z]/.test(senha)) forcaPontos++;
            if (/[A-Z]/.test(senha)) forcaPontos++;
            if (/[0-9]/.test(senha)) forcaPontos++;
            if (/[^a-zA-Z0-9]/.test(senha)) forcaPontos++;

            // Classificar força
            if (forcaPontos <= 2) {
                forcaTexto = '⚠️ Senha fraca';
                forcaClasse = 'strength-weak';
            } else if (forcaPontos <= 4) {
                forcaTexto = '⚠️ Senha razoável';
                forcaClasse = 'strength-fair';
            } else {
                forcaTexto = '✓ Senha forte';
                forcaClasse = 'strength-good';
            }

            indicator.textContent = forcaTexto;
            indicator.className = `password-strength ${forcaClasse}`;
        }
    </script>
</body>
</html>
