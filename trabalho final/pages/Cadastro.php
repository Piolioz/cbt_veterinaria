<?php
session_start();
require_once '../includes/conexao.php';

// Segurança
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Processar o formulário
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cadastrar'])) {
    $nome = trim($_POST['nome']);
    $especie = trim($_POST['especie']);
    $raca = trim($_POST['raca']);
    $idade = (int)$_POST['idade'];

    
    if ($idade < 1 || $idade > 20) {
        $erro = "A idade do animal deve ser obrigatoriamente entre 1 e 20 anos.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO animais (usuario_id, nome, especie, raca, idade) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$usuario_id, $nome, $especie, $raca, $idade]);
            
            // Redireciona para a lista de animais com sucesso
            header("Location: Animais.php?sucesso=1");
            exit();
        } catch (PDOException $e) {
            $erro = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Animal - CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-box { max-width: 600px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 40px auto; }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <header><h1>Agendamento CBTregiões</h1></header>
    <nav>
        <ul class="lista">
            <li><a href="index.php">Início</a></li>
            <li><a href="Agendar.php">Agendar</a></li>
            <li><a href="Animais.php">Meus Animais</a></li>
            <li><a href="Cadastro.php" class="active">Cadastrar Animal</a></li>
            <li><a href="LinhaTempo.php">Linha do Tempo</a></li>
            <li><a href="Localizaçao.php">Localização</a></li>
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li><a href="admin.php" style="color: #2e7d32; font-weight: bold;">Painel Admin</a></li>
            <?php endif; ?>
            <li><a href="../actions/logout.php" style="color: #d32f2f;">Sair</a></li>
        </ul>
    </nav>

    <main class="page-content">
        <div class="form-box">
            <h2 style="color: #008000; margin-bottom: 15px;">Cadastrar Novo Animal</h2>
            <p style="color: #666; margin-bottom: 20px;">Adicione o seu animal de estimação para poder agendar consultas.</p>

            <?php if(isset($erro)): ?>
                <div style="background: #ffebee; color: #c62828; padding: 10px; border-radius: 5px; margin-bottom: 15px;">❌ <?= $erro ?></div>
            <?php endif; ?>
            
            <form action="Cadastro.php" method="POST">
                <input type="hidden" name="cadastrar" value="1">
                
                <div class="form-group">
                    <label>Nome do Animal:</label>
                    <input type="text" name="nome" placeholder="Ex: Bobby" required pattern="[A-Za-zÀ-ÿ\s]+">
                </div>
                
                <div class="form-group">
                    <label>Espécie:</label>
                    <select name="especie" required>
                        <option value="">Selecione...</option>
                        <option value="Canino">Canino (Cão)</option>
                        <option value="Felino">Felino (Gato)</option>
                        <option value="Ave">Ave</option>
                        <option value="Roedor">Roedor</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Raça (Opcional):</label>
                    <input type="text" name="raca" placeholder="Ex: Labrador">
                </div>
                
                <div class="form-group">
                    <label>Idade (anos):</label>
                
                    <input type="number" name="idade" min="1" max="20" required>
                    <p class="note small" style="margin-top: 5px; color: #888;">Permitido entre 1 e 20 anos.</p>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px;">Gravar Animal</button>
            </form>
        </div>
    </main>
</body>
</html>