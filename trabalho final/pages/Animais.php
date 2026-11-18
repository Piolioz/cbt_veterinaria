<?php
session_start();
require_once '../includes/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Apagar um animal
if (isset($_GET['apagar'])) {
    $id_apagar = $_GET['apagar'];
    $stmt = $pdo->prepare("DELETE FROM animais WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id_apagar, $usuario_id]);
    header("Location: Animais.php");
    exit();
}

// Buscar os animais
$stmt = $pdo->prepare("SELECT * FROM animais WHERE usuario_id = ? ORDER BY nome ASC");
$stmt->execute([$usuario_id]);
$meus_animais = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Meus Animais - CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .animal-card { background: white; padding: 20px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;}
        .btn-remover { padding: 8px 15px; background: #d32f2f; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <header><h1>Agendamento CBTregiões</h1></header>
    <nav>
        <ul class="lista">
            <li><a href="index.php">Início</a></li>
            <li><a href="Agendar.php">Agendar</a></li>
            <li><a href="Animais.php">Meus Animais</a></li>
            <li><a href="Cadastro.php">Cadastrar Animal</a></li>
            <li><a href="LinhaTempo.php">Linha do Tempo</a></li>
            <li><a href="Localizaçao.php">Localização</a></li>
            
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li><a href="admin.php" style="color: #2e7d32; font-weight: bold;">Painel Admin</a></li>
            <?php endif; ?>
            
            <li><a href="../actions/logout.php" style="color: #d32f2f;">Sair</a></li>
        </ul>
    </nav>

    <main class="page-content" style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
        <h2 style="color: #333; margin-bottom: 20px;">Os Meus Animais</h2>
        
        <?php if(isset($_GET['sucesso'])): ?>
            <div style="background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 5px; margin-bottom: 20px;">✅ Animal cadastrado com sucesso!</div>
        <?php endif; ?>

        <?php if(count($meus_animais) > 0): ?>
            <?php foreach($meus_animais as $animal): ?>
                <div class="animal-card">
                    <div>
                        <h3 style="margin: 0; color: #008000; font-size: 20px;"><?= htmlspecialchars($animal['nome']) ?></h3>
                        <p style="margin: 5px 0 0; color: #666;">
                            <b>Espécie:</b> <?= htmlspecialchars($animal['especie']) ?> | 
                            <b>Idade:</b> <?= $animal['idade'] ?> ano(s)
                        </p>
                    </div>
                    <a href="Animais.php?apagar=<?= $animal['id'] ?>" class="btn-remover" onclick="return confirm('Remover o <?= htmlspecialchars($animal['nome']) ?>?')">Remover</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: white; border-radius: 8px; border: 1px dashed #ccc;">
                <p>Não tem animais registados.</p>
                <a href="Cadastro.php" class="btn btn-primary" style="display:inline-block; margin-top:10px;">Cadastrar Primeiro Animal</a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>