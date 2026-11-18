<?php
session_start();
require_once '../includes/conexao.php';

// Segurança: Tem de estar logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// Lógica de Identificação: Com quem estamos a falar
if ($is_admin) {
    if (!isset($_GET['id_usuario'])) { header("Location: admin.php"); exit(); }
    $id_chat_usuario = $_GET['id_usuario'];
    $nome_conversa = isset($_GET['nome']) ? $_GET['nome'] : 'Tutor';
} else {
    // Se for o cliente, ele vê o seu próprio chat com a Clínica
    $id_chat_usuario = $_SESSION['usuario_id'];
    $nome_conversa = "Clínica CBTregiões";
}

// TRUQUE MÁGICO 2.0: Atualiza a tabela antiga para suportar o Chat automaticamente!
try {
    $pdo->exec("ALTER TABLE notificacoes ADD COLUMN remetente VARCHAR(20) DEFAULT 'admin'");
} catch (PDOException $e) {
    
}

// 1. Processar o envio de uma Nova Mensagem
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nova_mensagem'])) {
    $mensagem = trim($_POST['mensagem']);
    $remetente = $is_admin ? 'admin' : 'cliente'; // Define quem enviou

    if (!empty($mensagem)) {
        $stmt = $pdo->prepare("INSERT INTO notificacoes (usuario_id, mensagem, remetente) VALUES (?, ?, ?)");
        $stmt->execute([$id_chat_usuario, $mensagem, $remetente]);
    }
    
    // Recarrega a página para limpar o formulário e mostrar a mensagem
    $url = "chat.php" . ($is_admin ? "?id_usuario=$id_chat_usuario&nome=" . urlencode($nome_conversa) : "");
    header("Location: $url");
    exit();
}

// 2. Buscar o Histórico da Conversa
$stmt = $pdo->prepare("SELECT * FROM notificacoes WHERE usuario_id = ? ORDER BY data_envio ASC");
$stmt->execute([$id_chat_usuario]);
$mensagens = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Chat - Clínica CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #e5ddd5; font-family: Arial, sans-serif; }
        .chat-container { max-width: 600px; margin: 30px auto; background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; flex-direction: column; height: 75vh; }
        
        .chat-header { background: #008000; color: white; padding: 15px 20px; border-radius: 10px 10px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .chat-header h2 { margin: 0; font-size: 18px; }
        .btn-voltar { color: white; text-decoration: none; font-size: 14px; background: rgba(0,0,0,0.2); padding: 5px 10px; border-radius: 5px; }
        
        .chat-messages { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 15px; background-image: url('https://www.transparenttextures.com/patterns/cubes.png'); background-color: #efeae2; }
        
        .msg-bubble { max-width: 75%; padding: 10px 15px; border-radius: 10px; position: relative; line-height: 1.4; font-size: 15px; word-wrap: break-word; }
        
        
        .msg-mine { background: #dcf8c6; align-self: flex-end; border-bottom-right-radius: 0; }
        .msg-theirs { background: white; align-self: flex-start; border-bottom-left-radius: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }
        
        .msg-time { font-size: 11px; color: #888; text-align: right; margin-top: 5px; display: block; }
        
        .chat-input-area { padding: 15px; background: #f0f0f0; border-radius: 0 0 10px 10px; display: flex; gap: 10px; align-items: center; }
        .chat-input-area input { flex: 1; padding: 15px; border: none; border-radius: 25px; outline: none; font-size: 15px; }
        .chat-input-area button { background: #008000; color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; font-weight: bold; transition: 0.2s; }
        .chat-input-area button:hover { background: #006600; }
    </style>
</head>
<body>

    <div class="chat-container">
        <div class="chat-header">
            <h2>Conversa com: <?= htmlspecialchars($nome_conversa) ?></h2>
            <a href="<?= $is_admin ? 'admin.php' : 'LinhaTempo.php' ?>" class="btn-voltar">⬅ Voltar</a>
        </div>

        <div class="chat-messages" id="chatBox">
            <?php if(count($mensagens) == 0): ?>
                <div style="text-align: center; color: #888; margin-top: 20px; font-size: 14px;">Nenhuma mensagem encontrada. Inicie a conversa!</div>
            <?php endif; ?>

            <?php foreach($mensagens as $msg): 
                // A magia visual: Determina se a mensagem fui EU que enviei, ou se foi O OUTRO
                $foi_admin = ($msg['remetente'] == 'admin');
                $sou_eu_a_ver = ($is_admin && $foi_admin) || (!$is_admin && !$foi_admin);
                
                $classe_css = $sou_eu_a_ver ? 'msg-mine' : 'msg-theirs';
            ?>
                <div class="msg-bubble <?= $classe_css ?>">
                    <?= nl2br(htmlspecialchars($msg['mensagem'])) ?>
                    <span class="msg-time"><?= date('d/m/Y H:i', strtotime($msg['data_envio'])) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <form action="" method="POST" class="chat-input-area">
            <input type="hidden" name="nova_mensagem" value="1">
            <input type="text" name="mensagem" placeholder="Escreva uma mensagem..." required autocomplete="off" autofocus>
            <button type="submit">Enviar</button>
        </form>
    </div>

    <script>
        const chatBox = document.getElementById('chatBox');
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>