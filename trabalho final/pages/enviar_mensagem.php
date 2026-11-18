<?php
session_start();
require_once '../includes/conexao.php';

// Segurança: Apenas Administradores
if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

//Cria a tabela de notificações automaticamente se ela não existir na base de dados!
$pdo->exec("CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    data_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
)");

// Processar o envio da mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $mensagem = trim($_POST['mensagem']);

    try {
        $stmt = $pdo->prepare("INSERT INTO notificacoes (usuario_id, mensagem) VALUES (?, ?)");
        $stmt->execute([$id_usuario, $mensagem]);
        
        echo "<script>alert('Mensagem enviada com sucesso para o cliente!'); window.location.href='admin.php';</script>";
        exit();
    } catch (Exception $e) {
        $erro = "Erro ao enviar: " . $e->getMessage();
    }
}

// Receber os dados do link para mostrar no ecrã
if (!isset($_GET['id_usuario'])) {
    header("Location: admin.php");
    exit();
}
$id_usuario = $_GET['id_usuario'];
$nome_tutor = $_GET['nome'];
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Enviar Mensagem - Administração</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .msg-box { max-width: 500px; margin: 40px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <main>
        <div class="msg-box">
            <h2 style="color: #0288d1; margin-bottom: 20px;">✉️ Nova Mensagem</h2>
            <p>A enviar notificação para: <b><?= htmlspecialchars($nome_tutor) ?></b></p>
            
            <?php if(isset($erro)) echo "<p style='color:red;'>$erro</p>"; ?>

            <form action="enviar_mensagem.php" method="POST" style="margin-top: 20px;">
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($id_usuario) ?>">

                <div class="form-group">
                    <label>Escreva a sua mensagem (sobre a consulta, exames, etc):</label>
                    <textarea name="mensagem" rows="5" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1; background: #0288d1;">Enviar Mensagem</button>
                    <a href="admin.php" class="btn" style="flex: 1; text-align: center; background: #999; color: white;">Cancelar</a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>