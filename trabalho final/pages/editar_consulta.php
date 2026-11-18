<?php
session_start();
require_once '../includes/conexao.php';

// Segurança: Apenas Administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// 1. Processar a atualização se o formulário for enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_agendamento = $_POST['id_agendamento'];
    $nova_data = $_POST['data_consulta'];
    $nova_hora = $_POST['hora_consulta'];
    $novo_doutor = trim($_POST['doutor']);

    try {
        $stmtUpdate = $pdo->prepare("UPDATE agendamentos SET data_consulta = ?, hora_consulta = ?, doutor = ? WHERE id = ?");
        $stmtUpdate->execute([$nova_data, $nova_hora, $novo_doutor, $id_agendamento]);
        
        // Volta para o admin
        header("Location: admin.php");
        exit();
    } catch (Exception $e) {
        $erro = "Erro ao atualizar: " . $e->getMessage();
    }
}

// 2. Procurar os dados atuais da consulta para preencher o formulário
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, u.nome as tutor, an.nome as animal, s.nome as servico 
                       FROM agendamentos a 
                       JOIN usuarios u ON a.usuario_id = u.id 
                       JOIN animais an ON a.animal_id = an.id 
                       JOIN servicos s ON a.servico_id = s.id 
                       WHERE a.id = ?");
$stmt->execute([$id]);
$consulta = $stmt->fetch();

if (!$consulta) {
    die("Consulta não encontrada.");
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Editar Consulta - Administração</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .edit-box { max-width: 500px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .info-box { background: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; font-size: 14px; color: #333; border-left: 4px solid #008000; }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <main>
        <div class="edit-box">
            <h2 style="color: #008000; margin-bottom: 20px;">Reprogramar Consulta</h2>
            
            <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

            <div class="info-box">
                <b>Tutor:</b> <?= htmlspecialchars($consulta['tutor']) ?><br>
                <b>Animal:</b> <?= htmlspecialchars($consulta['animal']) ?> <br>
                <b>Serviço:</b> <?= htmlspecialchars($consulta['servico']) ?>
            </div>

            <form action="editar_consulta.php?id=<?= $id ?>" method="POST">
                <input type="hidden" name="id_agendamento" value="<?= $consulta['id'] ?>">

                <div class="form-group">
                    <label>Nova Data:</label>
                    <input type="date" name="data_consulta" value="<?= $consulta['data_consulta'] ?>" required>
                </div>

                <div class="form-group">
                    <label>Nova Hora:</label>
                    <input type="time" name="hora_consulta" value="<?= $consulta['hora_consulta'] ?>" min="08:00" max="18:00" required>
                </div>

                <div class="form-group">
                    <label>Doutor Responsável:</label>
                    <select name="doutor" required>
                        <option value="Dr. Gabriel Lins" <?= ($consulta['doutor'] == 'Dr. Gabriel Lins') ? 'selected' : '' ?>>Dr. Gabriel Lins</option>
                        <option value="Dr. Tiago Silva" <?= ($consulta['doutor'] == 'Dr. Tiago Silva') ? 'selected' : '' ?>>Dr. Tiago Silva</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Guardar Alterações</button>
                    <a href="admin.php" class="btn" style="flex: 1; text-align: center; background: #999; color: white;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>