<?php
session_start();
require_once '../includes/conexao.php';

if (!isset($_SESSION['usuario_id']) || !isset($_GET['agendamento_id'])) {
    header("Location: index.php");
    exit();
}

$agendamento_id = $_GET['agendamento_id'];
$usuario_logado = $_SESSION['usuario_id'];


$stmt = $pdo->prepare("SELECT id FROM agendamentos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$agendamento_id, $usuario_logado]);

if ($stmt->rowCount() == 0) {
    // Se o agendamento não existir para este utilizador, bloqueia o acesso
    die("Acesso negado: Você não tem permissão para avaliar este agendamento.");
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliar Atendimento - CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Clínica Veterinária CBTregiões</h1>
    </header>

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

    <main class="login-container">
        <div class="login-box" style="max-width: 500px;">
            <h2>Agendamento Concluído!</h2>
            <p style="text-align: center; color: #666; margin-bottom: 20px;">Para terminar, por favor simule a avaliação da sua futura consulta.</p>
            
            <form action="../actions/processa_avaliacao.php" method="POST">
                <input type="hidden" name="agendamento_id" value="<?= htmlspecialchars($agendamento_id) ?>">

                <div class="form-group">
                    <label>A sua nota (1 a 5 estrelas):</label>
                    <div class="rating-container">
                        <input type="radio" id="star5" name="nota" value="5" required>
                        <label for="star5">★</label>
                        
                        <input type="radio" id="star4" name="nota" value="4">
                        <label for="star4">★</label>
                        
                        <input type="radio" id="star3" name="nota" value="3">
                        <label for="star3">★</label>
                        
                        <input type="radio" id="star2" name="nota" value="2">
                        <label for="star2">★</label>
                        
                        <input type="radio" id="star1" name="nota" value="1">
                        <label for="star1">★</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="comentario">Deixe um comentário:</label>
                    <textarea id="comentario" name="comentario" rows="4" placeholder="Deixe o seu feedback aqui..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Enviar Avaliação Final</button>
            </form>
        </div>
    </main>

    <footer>
        <p>Projeto desenvolvido para a Unidade Curricular de Programação Web I e II – IEES (2025/2026).</p>
        <p>Desenvolvido por Gabriel Lins e Tiago Silva  .</p>
    </footer>
</body>
</html>