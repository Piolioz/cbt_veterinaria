<?php
session_start();
require_once '../includes/conexao.php';

$erro = '';
$sucesso = '';

//Atualiza a tabela 'usuarios' para suportar o NIF automaticamente!
try {
    $pdo->exec("ALTER TABLE usuarios ADD COLUMN nif VARCHAR(9) NULL UNIQUE");
} catch (PDOException $e) {
    // A coluna já existe, ignora silenciosamente
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $nif = trim($_POST['nif']);
    $senha = $_POST['senha'];
    $senha_confirma = $_POST['senha_confirma'];

    if (empty($nome)) {
        $erro = "O nome de utilizador não pode estar vazio.";
    } elseif (strlen($nome) > 70) {
        $erro = "O nome não pode ultrapassar 70 caracteres.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "O formato do email fornecido é inválido.";
    } elseif (!preg_match("/^[0-9]{9}$/", $nif)) {
        $erro = "O NIF deve conter exatamente 9 números.";
    } elseif ($senha !== $senha_confirma) {
        $erro = "As senhas não coincidem.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $senha)) {
        $erro = "A senha deve ter no mínimo 8 caracteres, incluindo uma maiúscula, uma minúscula, um número e um caractere especial.";
    } else {
        $stmtCheck = $pdo->prepare("SELECT nome, email, nif FROM usuarios WHERE nome = ? OR email = ? OR nif = ?");
        $stmtCheck->execute([$nome, $email, $nif]);
        
        if ($stmtCheck->rowCount() > 0) {
            $userExistente = $stmtCheck->fetch();
            if (strtolower($userExistente['nome']) == strtolower($nome)) {
                $erro = "Este Nome de Utilizador já se encontra em uso.";
            } elseif (strtolower($userExistente['email']) == strtolower($email)) {
                $erro = "Este Email já está registado.";
            } elseif ($userExistente['nif'] == $nif) {
                $erro = "Erro: Este NIF já se encontra associado a uma conta existente.";
            }
        } else {
            $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, nif, is_admin) VALUES (?, ?, ?, ?, 0)");
                $stmt->execute([$nome, $email, $senha_criptografada, $nif]);
                $sucesso = "Conta criada com sucesso! Redirecionando para o login...";
                header("refresh:3;url=login.php");
            } catch (PDOException $e) {
                $erro = "Erro interno no servidor.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Clínica Veterinária CBTregiões</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px; font-family: Arial, sans-serif; }
        .auth-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; box-sizing: border-box; }
        .auth-card h2 { color: #008000; text-align: center; margin-top: 0; margin-bottom: 25px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; color: #333; font-weight: bold; font-size: 14px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; box-sizing: border-box; }
        
        .btn-auth { width: 100%; padding: 14px; background: #008000; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-auth:hover { background: #006600; }
        
        .msg-erro { background: #ffebee; color: #c62828; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #c62828; font-size: 14px; }
        .msg-sucesso { background: #e8f5e9; color: #2e7d32; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #2e7d32; font-size: 14px; font-weight: bold; text-align: center; }
        .password-rules { font-size: 12px; color: #666; margin-top: 6px; line-height: 1.5; background: #f9f9f9; padding: 10px; border-radius: 4px; border: 1px solid #eee; }
        .form-row { display: flex; gap: 15px; }
        .form-row .form-group { flex: 1; margin-bottom: 15px; }
        
        .password-container { position: relative; display: flex; align-items: center; }
        .password-container input { padding-right: 45px; }
        .toggle-password { position: absolute; right: 15px; cursor: pointer; color: #777; font-size: 16px; }
        
        @media (max-width: 480px) { .form-row { flex-direction: column; gap: 0; } }
    </style>
</head>
<body>

    <div class="auth-card">
        <h2>Criar Conta CBTregiões</h2>
        
        <?php if (!empty($erro)): ?><div class="msg-erro">⚠️ <?= $erro ?></div><?php endif; ?>
        <?php if (!empty($sucesso)): ?><div class="msg-sucesso">✅ <?= $sucesso ?></div><?php else: ?>
        
            <form action="" method="POST">
                <div class="form-group">
                    <label for="nome">Nome Completo:</label>
                    <input type="text" id="nome" name="nome" value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="nif">NIF:</label>
                        <input type="text" id="nif" name="nif" value="<?= isset($_POST['nif']) ? htmlspecialchars($_POST['nif']) : '' ?>" pattern="[0-9]{9}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="senha">Criar Senha:</label>
                    <div class="password-container">
                        <input type="password" id="senha" name="senha" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}" required>
                        <i class="fa fa-eye toggle-password" onclick="mostrarOcultarSenha('senha', this)"></i>
                    </div>
                    <div class="password-rules">Mínimo 8 caracteres, 1 Maiúscula, 1 Minúscula, 1 Número e 1 Símbolo.</div>
                </div>

                <div class="form-group">
                    <label for="senha_confirma">Confirmar Senha:</label>
                    <div class="password-container">
                        <input type="password" id="senha_confirma" name="senha_confirma" required>
                        <i class="fa fa-eye toggle-password" onclick="mostrarOcultarSenha('senha_confirma', this)"></i>
                    </div>
                </div>

                <button type="submit" class="btn-auth">Registar Conta</button>
            </form>
            
            <div style="text-align: center; margin-top: 25px; font-size: 14px; color: #555;">
                Já possui uma conta? <a href="login.php" style="color: #008000; text-decoration: underline; font-weight: bold;">Faça Login aqui</a>.
            </div>
            
        <?php endif; ?>
    </div>

    <script>
        function mostrarOcultarSenha(inputId, iconElement) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                iconElement.classList.remove("fa-eye");
                iconElement.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                iconElement.classList.remove("fa-eye-slash");
                iconElement.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>