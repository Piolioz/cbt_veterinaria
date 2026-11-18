<?php

session_start();
require_once '../includes/conexao.php';

// Proteção máxima: Expulsa se não estiver logado OU se não for admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Calcula a média das notas (1 a 5 estrelas)
$stmtMedia = $pdo->query("SELECT AVG(nota) as media_notas FROM avaliacoes");
$media_feedback = $stmtMedia->fetch(PDO::FETCH_ASSOC);
$nota_media = round($media_feedback['media_notas'], 1);


// Busca utilizadores que enviaram mensagens, ordenado pelos mais recentes
$stmtChats = $pdo->query("
    SELECT DISTINCT u.id, u.nome, u.email 
    FROM notificacoes n 
    JOIN usuarios u ON n.usuario_id = u.id 
    ORDER BY n.data_envio DESC
");
$lista_chats = $stmtChats->fetchAll();

// 1. Buscar todos os agendamentos
$stmtConsultas = $pdo->query("SELECT a.*, u.nome as tutor, u.email, an.nome as animal, s.nome as servico 
                              FROM agendamentos a 
                              JOIN usuarios u ON a.usuario_id = u.id 
                              JOIN animais an ON a.animal_id = an.id 
                              JOIN servicos s ON a.servico_id = s.id 
                              ORDER BY a.data_consulta DESC");
$consultas = $stmtConsultas->fetchAll();

// 2. Buscar avaliações
$stmtAvaliacoes = $pdo->query("SELECT av.*, u.nome FROM avaliacoes av JOIN usuarios u ON av.usuario_id = u.id ORDER BY av.data_avaliacao DESC");
$avaliacoes = $stmtAvaliacoes->fetchAll();

// 3. Buscar utilizadores
$stmtUsuarios = $pdo->query("SELECT id, nome, email, is_admin FROM usuarios ORDER BY nome ASC");
$usuarios = $stmtUsuarios->fetchAll();

// 4.Buscar dados para o Gráfico (Conta quantos agendamentos tem cada Doutor)
$stmtGrafico = $pdo->query("SELECT doutor, COUNT(id) as total FROM agendamentos GROUP BY doutor");
$dadosGrafico = $stmtGrafico->fetchAll(PDO::FETCH_ASSOC);

$labelsGrafico = [];
$totaisGrafico = [];
foreach ($dadosGrafico as $linha) {
    $labelsGrafico[] = $linha['doutor'];
    $totaisGrafico[] = $linha['total'];
}

// 5.Buscar todos os animais registados no sistema
$stmtAnimais = $pdo->query("SELECT a.*, u.nome as nome_dono FROM animais a JOIN usuarios u ON a.usuario_id = u.id ORDER BY a.nome ASC");
$todos_animais = $stmtAnimais->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração - CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-section { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .admin-table th, .admin-table td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        .admin-table th { background: #008000; color: white; }
        .btn-small { padding: 5px 10px; font-size: 12px; border: none; border-radius: 3px; cursor: pointer; color: white; text-decoration: none;}
        .btn-danger { background: #d32f2f; }
        .btn-success { background: #2e7d32; }
        .btn-warning { background: #ffb300; color: #333; font-weight: bold; }
    </style>
</head>
<body style="background-color: #f4f7f6;">
    <header><h1>Área Restrita - Administração</h1></header>
    <nav>
        <ul class="lista">
            <li><a href="index.php">Voltar ao Site</a></li>
            <li><a href="../actions/logout.php" style="color: #d32f2f;">Sair</a></li>
        </ul>
    </nav>

    <main class="page-content">
        
        <div style="display: flex; flex-wrap: wrap; gap: 20px; margin-bottom: 30px;">
    
    <section class="admin-section" style="flex: 1; min-width: 300px; margin-bottom: 0;">
        <h3 style="color: #0288d1; margin-top: 0;">Consultas por Doutor</h3>
        <canvas id="graficoAgendamentos" width="400" height="250"></canvas>
    </section>
    <section class="admin-section" style="flex: 1; min-width: 300px; margin-bottom: 0; text-align: center;">
        <h3 style="color: #0288d1; margin-top: 0;">Média de Satisfação</h3>
        <h1 style="font-size: 60px; color: #f5b301; margin: 10px 0;"><?= $nota_media ?></h1>
        <p style="color: #666; margin-bottom: 20px;">Nota média baseada nas avaliações</p>
        <canvas id="graficoFeedback" width="400" height="150"></canvas>
    </section>

            <section class="admin-section" style="flex: 2; min-width: 400px; margin-bottom: 0;">
                <h3 style="color: #0288d1; margin-top: 0;">Gestão de Animais Cadastrados</h3>
                
                <?php if(isset($_GET['msg'])) echo "<p style='color: #2e7d32; font-weight: bold; background: #e8f5e9; padding: 10px; border-radius: 5px;'>".$_GET['msg']."</p>"; ?>
                
                <table class="admin-table">
                    <tr>
                        <th style="background: #0288d1;">ID</th>
                        <th style="background: #0288d1;">Animal</th>
                        <th style="background: #0288d1;">Tutor Responsável</th>
                        <th style="background: #0288d1;">Ação</th>
                    </tr>
                    <?php if (count($todos_animais) > 0): ?>
                        <?php foreach($todos_animais as $animal): ?>
                        <tr>
                            <td>#<?= $animal['id'] ?></td>
                            <td><b><?= htmlspecialchars($animal['nome']) ?></b> (<?= htmlspecialchars($animal['especie']) ?>)</td>
                            <td><?= htmlspecialchars($animal['nome_dono']) ?></td>
                            <td>
                                <a href="editar_animal.php?id=<?= $animal['id'] ?>" class="btn-small btn-warning">✏️ Editar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center; color: #666;">Não existem animais registados.</td></tr>
                    <?php endif; ?>
                </table>
            </section>
        </div>
<section class="admin-section">
    <h2 style="color: #008000;">Centro de Mensagens (Chats)</h2>
    <p>Clique num utilizador para retomar a conversa.</p>
    
    <table class="admin-table">
        <thead>
            <tr style="background-color: #0288d1; color: white;">
                <th>Tutor</th>
                <th>Email</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($lista_chats) > 0): ?>
                <?php foreach($lista_chats as $chat): ?>
                <tr>
                    <td><b><?= htmlspecialchars($chat['nome']) ?></b></td>
                    <td><?= htmlspecialchars($chat['email']) ?></td>
                    <td>
                        <a href="chat.php?id_usuario=<?= $chat['id'] ?>&nome=<?= urlencode($chat['nome']) ?>" 
                           class="btn-small" style="background-color: #008000; padding: 8px 15px;">
                           Abrir Conversa
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align: center;">Nenhuma conversa ativa no momento.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
        <section class="admin-section">
            <h2>Todas as Consultas Agendadas</h2>
            <?php if (count($consultas) > 0): ?>
            <table class="admin-table">
                <tr><th>Data / Hora</th><th>Doutor</th><th>Tutor / Contacto</th><th>Animal / Serviço</th><th>Detalhes</th><th>Ações</th></tr>
                <?php foreach($consultas as $c): ?>
                <tr>
                    <td style="white-space: nowrap;">
                        <b><?= date('d/m/Y', strtotime($c['data_consulta'])) ?></b><br>
                        às <?= $c['hora_consulta'] ?>
                    </td>
                    <td><?= htmlspecialchars($c['doutor']) ?></td>
                    <td>
                        <b><?= htmlspecialchars($c['tutor']) ?></b><br>
                        Telefone <?= htmlspecialchars($c['telefone']) ?><br>
<a href="chat.php?id_usuario=<?= $c['usuario_id'] ?>&nome=<?= urlencode($c['tutor']) ?>" class="btn-small btn-success" style="display:inline-block; margin-top: 5px; background-color: #0288d1;">Abrir Chat</a>                    </td>
                    <td>
                        <b><?= htmlspecialchars($c['animal']) ?></b><br>
                        <?= htmlspecialchars($c['servico']) ?>
                    </td>
                    <td style="max-width: 200px;">
                        <details style="cursor: pointer; font-size: 13px; color: #333;">
                            <summary style="color: #008000; font-weight: bold;">Ver Motivo</summary>
                            <div style="margin-top: 5px; padding: 8px; background: #f0f8ff; border-left: 3px solid #008000; border-radius: 3px;">
                                <?= nl2br(htmlspecialchars($c['motivo'])) ?>
                            </div>
                        </details>
                    </td>
                    <td>
                        <a href="editar_consulta.php?id=<?= $c['id'] ?>" class="btn-small btn-success" style="display:inline-block; margin-bottom:5px; text-align: center; width: 60px;">Editar</a>
                        
                        <form action="../actions/processa_admin.php" method="POST" style="display:inline-block;">
                            <input type="hidden" name="acao" value="apagar_consulta">
                            <input type="hidden" name="id_consulta" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn-small btn-danger" style="width: 80px;" onclick="return confirm('ATENÇÃO: Deseja cancelar e excluir este agendamento do sistema?')">Excluir</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
                <p style="padding: 15px; color: #666; background: #f9f9f9; border-radius: 5px;">Ainda não existem consultas agendadas no sistema.</p>
            <?php endif; ?>
        </section>

        <section class="admin-section">
            <h2>Gestão de Comentários e Avaliações</h2>
            
            <?php if (count($avaliacoes) > 0): ?>
            <table class="admin-table">
                <tr><th>Tutor</th><th>Nota</th><th>Comentário</th><th>Sua Resposta</th><th>Ações</th></tr>
                <?php foreach($avaliacoes as $av): ?>
                <tr>
                    <td><?= htmlspecialchars($av['nome']) ?></td>
                    <td><?= $av['nota'] ?> Estrelas</td>
                    <td><?= htmlspecialchars($av['comentario']) ?></td>
                    <td>
                        <?= $av['resposta_admin'] ? htmlspecialchars($av['resposta_admin']) : '<i style="color: #999;">Sem resposta</i>' ?>
                    </td>
                    <td>
                        <form action="../actions/processa_admin.php" method="POST" style="display:inline-block; margin-bottom:5px;">
                            <input type="hidden" name="acao" value="responder_comentario">
                            <input type="hidden" name="id_avaliacao" value="<?= $av['id'] ?>">
                            <input type="text" name="resposta" placeholder="A sua resposta..." required style="padding:5px; border: 1px solid #ccc; border-radius: 3px;">
                            <button type="submit" class="btn-small btn-success">Responder</button>
                        </form>
                        
                        <form action="../actions/processa_admin.php" method="POST" style="display:inline-block;">
                            <input type="hidden" name="acao" value="apagar_comentario">
                            <input type="hidden" name="id_avaliacao" value="<?= $av['id'] ?>">
                            <button type="submit" class="btn-small btn-danger" onclick="return confirm('Tem a certeza que deseja apagar este comentário?')">Apagar</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
                <p style="padding: 15px; color: #666; background: #f9f9f9; border-radius: 5px;">Ainda não existem avaliações registadas no sistema.</p>
            <?php endif; ?>
        </section>

        <section class="admin-section">
            <h2>Gestão de Utilizadores</h2>
            <table class="admin-table">
                <tr><th>Nome</th><th>Email</th><th>Estatuto</th><th>Ações</th></tr>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['is_admin'] ? '<b style="color: #2e7d32;">Admin</b>' : 'Cliente' ?></td>
                    <td>
                        <?php if ($u['id'] == $_SESSION['usuario_id']): ?>
                            <i style="color: #999;">(Você)</i>
                        <?php else: ?>
                            
                            <?php if (!$u['is_admin']): ?>
                                <form action="../actions/processa_admin.php" method="POST" style="display:inline-block; margin-right: 5px;">
                                    <input type="hidden" name="acao" value="promover_admin">
                                    <input type="hidden" name="id_usuario" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn-small btn-success" onclick="return confirm('Tornar este utilizador Administrador?')">Tornar Admin</button>
                                </form>

                                <form action="../actions/processa_admin.php" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="acao" value="apagar_usuario">
                                    <input type="hidden" name="id_usuario" value="<?= $u['id'] ?>">
                                    <button type="submit" class="btn-small btn-danger" onclick="return confirm('ATENÇÃO: Apagar esta conta vai eliminar também todos os animais e histórico clínico desta pessoa. Deseja mesmo continuar?')">Apagar Conta</button>
                                </form>

                            <?php else: ?>
                                <?php if ($_SESSION['usuario_id'] == 1): ?>
                                    <form action="../actions/processa_admin.php" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="acao" value="rebaixar_admin">
                                        <input type="hidden" name="id_usuario" value="<?= $u['id'] ?>">
                                        <button type="submit" class="btn-small btn-danger" onclick="return confirm('Remover os privilégios de Administrador?')">Remover Admin</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #d32f2f; font-size: 12px; font-weight: bold;">Admin (Sem permissão para remover)</span>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

    </main>
    
    <footer>
        <p>Projeto desenvolvido para a Unidade Curricular de Programação Web I e II – IEES (2025/2026).</p>
        <p>Desenvolvido por Gabriel Lins e Tiago Silva.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?= json_encode($labelsGrafico) ?>;
        const dados = <?= json_encode($totaisGrafico) ?>;

        const ctx = document.getElementById('graficoAgendamentos').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Número de Consultas Marcadas',
                    data: dados,
                    backgroundColor: ['#008000', '#0288d1', '#ffb300', '#d32f2f'],
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { stepSize: 1 } 
                    }
                }
            }
        });
    </script>
</body>
</html>