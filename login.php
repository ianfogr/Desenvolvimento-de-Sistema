<?php
session_start();

// Se já está logado, redireciona para index
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}

require 'config.php';

$erro = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (!$email || !$senha) {
        $erro = 'Email e senha são obrigatórios.';
    } else {
        $stmt = $pdo->prepare("SELECT id_usuario, nome, email, senha, perfil FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_perfil'] = $usuario['perfil'];

            header('Location: index.php');
            exit();
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SafeAudit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            width: 100%;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
            color: white;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>SafeAudit</h2>
        
        <?php if ($erro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-4">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-login btn-primary">Entrar</button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <small>Não tem conta? <a href="register.php" style="color: #667eea; text-decoration: none;">Criar conta aqui</a></small>
        </div>

        <hr>
        <p class="text-center text-muted small">
            Credenciais de teste:<br>
            <strong>admin@safeaudit.com</strong><br>
            <strong>admin123</strong>
        </p>
    </div>
</body>
</html>
