<?php
session_start();
require_once '../includes/conexao.php';

// Se não estiver logado vai para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Busca agendamentos do utilizador e verifica se já existe uma avaliação
$stmt = $pdo->prepare("
    SELECT 
        a.id, 
        a.data_consulta as data_evento, 
        s.nome as titulo, 
        a.motivo as descricao, 
        an.nome as animal_nome, 
        av.nota, 
        av.comentario, 
        av.resposta_admin  -- Certifique-se que esta coluna está aqui!
    FROM agendamentos a
    JOIN animais an ON a.animal_id = an.id
    JOIN servicos s ON a.servico_id = s.id
    LEFT JOIN avaliacoes av ON a.id = av.agendamento_id -- IMPORTANTE: Ligação pelo agendamento
    WHERE a.usuario_id = ?
    ORDER BY a.data_consulta DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$historico = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Linha de Tempo - Clínica Veterinária CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .stars-display { margin-top: 15px; padding-top: 15px; border-top: 1px dashed #d8e8d8; color: #f5b301; font-size: 18px; display: flex; align-items: center; gap: 10px; }
        .comentario-texto { color: #555; font-size: 14px; font-style: italic; }
        .admin-response { margin-top: 10px; background: #f0f8ff; padding: 10px; border-radius: 5px; border-left: 4px solid #008000; }
        
        .chat-card { max-width: 600px; margin: 0 auto 40px auto; text-align: center; background: #e8f5e9; padding: 25px; border-radius: 15px; border: 2px dashed #a5d6a7; }
        .btn-chat { display: inline-block; padding: 12px 30px; background-color: #008000; color: white; font-size: 16px; font-weight: bold; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 10px rgba(0, 128, 0, 0.2); transition: 0.3s; }
        .btn-chat:hover { background-color: #006600; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0, 128, 0, 0.3); }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <header>
        <h1>Clínica Veterinária CBTregiões</h1>
    </header>

    <nav>
        <ul class="lista">
            <li><a href="index.php">Início</a></li>
            <li><a href="Agendar.php">Agendar</a></li>
            <li><a href="Animais.php">Meus Animais</a></li>
            <li><a href="Cadastro.php">Cadastrar Animal</a></li>
            <li><a href="LinhaTempo.php" class="active">Linha do Tempo</a></li>
            <li><a href="Localizaçao.php">Localização</a></li>
            
            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                <li><a href="admin.php" style="color: #2e7d32; font-weight: bold;">Painel Admin</a></li>
            <?php endif; ?>
            
            <li><a href="../actions/logout.php" style="color: #d32f2f;">Sair</a></li>
        </ul>
    </nav>

    <main class="page-content">
        <section class="timeline-section">
            <h2 style="color: #008000; text-align: center;">Histórico Clínico</h2>
            <p style="text-align: center; margin-bottom: 30px; color: #666;">Acompanhe todos os eventos e consultas do seu companheiro.</p>
            
            <?php if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1): ?>
            <div class="chat-card">
                <h3 style="color: #2e7d32; margin-top: 0;">Canal de Atendimento Direto</h3>
                <p style="color: #555; margin-bottom: 20px;">Precisa de falar com o Doutor, perguntar sobre exames ou tem alguma dúvida sobre a consulta?</p>
                <a href="chat.php" class="btn-chat">Abrir Chat com a Clínica</a>
            </div>
            <?php endif; ?>
            

            <div class="timeline">
                <?php if (count($historico) > 0): ?>
                    <?php foreach ($historico as $item): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-card">
                                <span class="timeline-date"><?= date('d/m/Y', strtotime($item['data_evento'])) ?></span>
                                
                                <h3 style="color: #333;"><?= htmlspecialchars($item['titulo']) ?> <span style="color: #0288d1;">(<?= htmlspecialchars($item['animal_nome']) ?>)</span></h3>
                                <p style="color: #555;"><?= nl2br(htmlspecialchars($item['descricao'])) ?></p>

                                <?php if ($item['nota']): ?>
                                    <div class="stars-display">
                                        <span><?= str_repeat('★', $item['nota']) ?><?= str_repeat('☆', 5 - $item['nota']) ?></span>
                                        <span class="comentario-texto">"<?= htmlspecialchars($item['comentario']) ?>"</span>
                                    </div>
                                    
                                    <?php if (!empty($item['resposta_admin'])): ?>
                                        <div class="admin-response">
                                            <strong style="color: #008000; font-size: 13px;">Resposta da Clínica:</strong>
                                            <p style="margin: 5px 0 0; font-size: 14px; color: #333;"><?= nl2br(htmlspecialchars($item['resposta_admin'])) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                <?php else: ?>
                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #d8e8d8;">
                                        <a href="Avaliar.php?agendamento_id=<?= $item['id'] ?>" style="font-size: 13px; color: #008000; font-weight: bold; text-decoration: none;">⭐ Deixar Avaliação</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; background: white; border-radius: 10px; border: 1px dashed #ccc;">
                        <h3 style="color: #999;">Ainda não existem consultas ou registos clínicos.</h3>
                        <p style="color: #777;">Os registos do seu animal aparecerão aqui após a primeira consulta.</p>
                        <a href="Agendar.php" class="btn btn-primary" style="display: inline-block; margin-top: 15px;">Agendar Primeira Consulta</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer style="text-align: center; padding: 20px; font-size: 14px; color: #666; margin-top: 40px;">
        <p>Projeto desenvolvido para a Unidade Curricular de Programação Web I e II – IEES (2025/2026).</p>
        <p>Desenvolvido por Gabriel Lins e Tiago Silva.</p>
    </footer>
</body>
</html>