<?php
session_start();
require_once '../includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $nota = $_POST['nota'];
    $comentario = $_POST['comentario'];
    $agendamento_id = !empty($_POST['agendamento_id']) ? $_POST['agendamento_id'] : null;
    $historico_id = !empty($_POST['historico_id']) ? $_POST['historico_id'] : null;

    
    if ($historico_id == '') { $historico_id = null; }

    $stmt = $pdo->prepare("INSERT INTO avaliacoes (usuario_id, agendamento_id, historico_id, nota, comentario) VALUES (?, ?, ?, ?, ?)");
    
    try {
        $stmt->execute([$usuario_id, $agendamento_id, $historico_id, $nota, $comentario]);
        header("Location: ../pages/LinhaTempo.php?sucesso=1");
    } catch (PDOException $e) {
        die("Erro ao salvar avaliação: " . $e->getMessage());
    }
}
?>