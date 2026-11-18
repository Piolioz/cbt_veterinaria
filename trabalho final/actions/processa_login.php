<?php
session_start();
require_once '../includes/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_usuario = trim($_POST['email']);
    $senha = $_POST['senha'];

    //se a pessoa é admin ou não
    $stmt = $pdo->prepare("SELECT id, nome, senha, is_admin FROM usuarios WHERE email = ? OR usuario = ?");
    $stmt->execute([$email_usuario, $email_usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        
        // Guarda a permissão de administrador na sessão
        $_SESSION['is_admin'] = $user['is_admin']; 
        
        header("Location: ../pages/index.php");
        exit();
    } else {
        die("Erro: Credenciais incorretas. <a href='../pages/login.php'>Tentar novamente</a>");
    }
}
?>