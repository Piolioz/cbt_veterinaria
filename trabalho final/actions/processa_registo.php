<?php
session_start();
require_once '../includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $usuario = trim($_POST['usuario']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar-senha'];

    if ($senha !== $confirmar_senha) {
        // Atualizado para apontar para a pasta pages
        die("Erro: As palavras-passe não coincidem. <a href='../pages/Registar.php'>Voltar</a>");
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? OR usuario = ?");
    $stmt->execute([$email, $usuario]);
    
    if ($stmt->rowCount() > 0) {
        // Atualizado para apontar para a pasta pages
        die("Erro: Este email ou nome de utilizador já está registado. <a href='../pages/Registar.php'>Voltar</a>");
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, usuario, senha) VALUES (?, ?, ?, ?)");
    
    if ($stmt->execute([$nome, $email, $usuario, $senha_hash])) {
        // Atualizado para apontar para a pasta pages
        header("Location: ../pages/login.php?registo=sucesso");
        exit();
    } else {
        echo "Erro ao criar conta. Por favor, tente novamente.";
    }
}
?>