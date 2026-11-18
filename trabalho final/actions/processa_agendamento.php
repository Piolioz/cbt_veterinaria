<?php
session_start();
require_once '../includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_id = $_SESSION['usuario_id'];
    $animal_id = $_POST['animal_id'];
    $telefone = trim($_POST['telefone']);
    
    // Busca o ID do serviço
    $nome_servico = $_POST['servico'];
    $stmtServico = $pdo->prepare("SELECT id FROM servicos WHERE nome = ?");
    $stmtServico->execute([$nome_servico]);
    $servico = $stmtServico->fetch();
    $servico_id = $servico ? $servico['id'] : 1; 

    $doutor = trim($_POST['doutor']);
    $data_consulta = $_POST['data'];
    $hora_consulta = $_POST['hora'];
    $motivo = trim($_POST['motivo']);

    
    //VERIFICA DISPONIBILIDADE DO MÉDICO

    $stmtVerifica = $pdo->prepare("SELECT id FROM agendamentos WHERE doutor = ? AND data_consulta = ? AND hora_consulta = ?");
    $stmtVerifica->execute([$doutor, $data_consulta, $hora_consulta]);
    
    if ($stmtVerifica->rowCount() > 0) {
        
        $mensagem_erro = urlencode("O " . $doutor . " já tem uma consulta marcada para essa data e hora. Por favor, escolha outro horário ou outro médico.");
        
        
        header("Location: ../pages/Agendar.php?erro=" . $mensagem_erro);
        exit();
    }
   

    try {
        
        $stmt = $pdo->prepare("INSERT INTO agendamentos (usuario_id, animal_id, servico_id, telefone, data_consulta, hora_consulta, motivo, doutor) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([$usuario_id, $animal_id, $servico_id, $telefone, $data_consulta, $hora_consulta, $motivo, $doutor]);
        
        echo "<script>alert('Agendamento realizado com sucesso!'); window.location.href='../pages/LinhaTempo.php';</script>";
        
    } catch (PDOException $e) {
        echo "Erro ao agendar: " . $e->getMessage();
    }
} else {
    header("Location: ../pages/Agendar.php");
    exit();
}