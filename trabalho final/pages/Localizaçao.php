<?php
session_start();

// Se o utilizador não estiver logado, é redirecionado para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localização - Veterinário CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Localização CBTregiões</h1>
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

    <main class="page-content">
        <section class="location-section">
            <h2>Onde nos encontrar</h2>
            <p>O nosso consultório veterinário está situado no centro da região, com fácil acesso e estacionamento próximo.</p>
            <div class="location-card">
                <h3>Clínica Veterinária CBTregiões</h3>
                <p><strong>Endereço:</strong> Rua das Flores, 123 - Centro, Cidade Exemplo</p>
                <p><strong>Localidade:</strong> Vila Saúde</p>
                <p><strong>Código Postal:</strong> 0000-000</p>
                <p><strong>Horário de funcionamento:</strong> Segunda a sexta, 08:00 às 18:00</p>
                <p><strong>Telefone:</strong> +351 XXX XXX XXX</p>
            </div>

            <div class="map-frame">
                <iframe src="https://www.google.com/maps?q=Fafe,Portugal&output=embed" width="100%" height="340" style="border:0; border-radius: 10px;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </section>
    </main>
    <footer>
        <p>Projeto desenvolvido para a Unidade Curricular de Programação Web I e II – IEES (2025/2026).</p>
        <p>Desenvolvido por Gabriel Lins e Tiago Silva  .</p>
    </footer>
</body>
</html>