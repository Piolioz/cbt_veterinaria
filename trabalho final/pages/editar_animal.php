<?php
session_start();
require_once '../includes/conexao.php';

//Só Administradores podem aceder
if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// 1. PROCESSAR A ATUALIZAÇÃO DOS DADOS
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar'])) {
    $id_animal = $_POST['id_animal'];
    $nome = trim($_POST['nome']);
    $especie = trim($_POST['especie']);
    $raca = trim($_POST['raca']);
    $idade = (int)$_POST['idade'];

    $stmt = $pdo->prepare("UPDATE animais SET nome = ?, especie = ?, raca = ?, idade = ? WHERE id = ?");
    $stmt->execute([$nome, $especie, $raca, $idade, $id_animal]);
    
    
    header("Location: admin.php?msg=Animal atualizado com sucesso!");
    exit();
}

// 2. CARREGAR OS DADOS ATUAIS DO ANIMAL
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id_animal = $_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, u.nome as nome_dono FROM animais a JOIN usuarios u ON a.usuario_id = u.id WHERE a.id = ?");
$stmt->execute([$id_animal]);
$animal = $stmt->fetch();

if (!$animal) {
    die("Animal não encontrado na base de dados.");
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Editar Animal - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-box { max-width: 600px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin: 40px auto; border-top: 5px solid #0288d1; }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <main class="page-content">
        <div class="admin-box">
            <h2 style="color: #0288d1; margin-bottom: 10px;">Editar Animal (Modo Admin)</h2>
            <p style="color: #666; margin-bottom: 20px;">Tutor responsável: <b><?= htmlspecialchars($animal['nome_dono']) ?></b></p>
            
            <form action="editar_animal.php" method="POST">
                <input type="hidden" name="atualizar" value="1">
                <input type="hidden" name="id_animal" value="<?= $animal['id'] ?>">
                
                <div class="form-group">
                    <label>Nome do Animal:</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($animal['nome']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Espécie:</label>
                    <select name="especie" required>
                        <option value="Canino" <?= $animal['especie'] == 'Canino' ? 'selected' : '' ?>>Canino</option>
                        <option value="Felino" <?= $animal['especie'] == 'Felino' ? 'selected' : '' ?>>Felino</option>
                        <option value="Ave" <?= $animal['especie'] == 'Ave' ? 'selected' : '' ?>>Ave</option>
                        <option value="Roedor" <?= $animal['especie'] == 'Roedor' ? 'selected' : '' ?>>Roedor</option>
                        <option value="Outro" <?= $animal['especie'] == 'Outro' ? 'selected' : '' ?>>Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Raça:</label>
                    <input type="text" name="raca" value="<?= htmlspecialchars($animal['raca']) ?>">
                </div>
                
                <div class="form-group">
                    <label>Idade (anos):</label>
                    <input type="number" name="idade" value="<?= $animal['idade'] ?>" min="1" max="20" required>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; background-color: #0288d1;">Guardar Alterações</button>
                    <a href="admin.php" class="btn" style="flex: 1; text-align: center; background-color: #999; color: white; text-decoration: none; padding: 12px; border-radius: 5px;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>