<?php
require_once '../includes/conexao.php';

$email = 'gabrielalvarez0706@gmail.com';
$senha_nova = 'adm@-07';
$senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE usuarios SET is_admin = 1, senha = ? WHERE email = ?");
if ($stmt->execute([$senha_hash, $email])) {
    echo "<h1 style='color: green;'>Conta de administrador configurada com sucesso!</h1>";
    echo "<p>O email <b>$email</b> agora é administrador e a palavra-passe foi atualizada.</p>";
} else {
    echo "<h1 style='color: red;'>Erro ao configurar.</h1>";
}
?>