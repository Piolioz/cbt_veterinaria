<?php
session_start();

// Se o utilizador não estiver logado, é redirecionado para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Sistema de Veterinário CBTregiões</title>
</head>
<body>
    <header>
        <h1>Sistema Veterinário CBTregiões</h1>
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
        <section class="hero">
            <div class="hero-copy">
                <span class="hero-eyebrow">Clínica Veterinária Digital</span>
                <h2>O cuidado que o seu melhor amigo merece, à distância de um clique.</h2>
                <p>Uma plataforma integrada para tutores e profissionais veterinários. Centralize o histórico clínico e agende serviços com total facilidade.</p>
                <div class="hero-actions">
                    <a href="Agendar.php" class="btn btn-primary">Agendar Consulta</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="../assets/img/Generated_Image_a3v80va3v80va3v8.png" alt="...">
            </div>
        </section>

        <section class="features-section">
            <h3 class="section-title">Os três pilares do nosso sistema</h3>
            <div class="features-grid">
                <article class="feature-card">
                    <h4>Registo de Animais (Fichas Individuais)</h4>
                    <p>Crie o perfil digital do seu animal. Cruze os dados do seu pet com os seus contactos de forma ágil e segura.</p>
                </article>
                <article class="feature-card">
                    <h4>Agendamento Inteligente</h4>
                    <p>Marque consultas clínicas, tratamentos e serviços estéticos sem complicações e evite sobreposições de horários.</p>
                </article>
                <article class="feature-card">
                    <h4>Linha do Tempo de Saúde</h4>
                    <p>Aceda ao registo cronológico de vacinações, alergias e intervenções médicas passadas para salvaguardar a memória clínica.</p>
                </article>
            </div>
        </section>

        <section class="pricing-section">
            <h3 class="section-title">Tabela de Preços</h3>
            <div class="pricing-grid">
                <article class="price-card">
                    <h4>Consulta Clínica</h4>
                    <p>€35,00</p>
                </article>
                <article class="price-card">
                    <h4>Vacinação</h4>
                    <p>€25,00</p>
                </article>
                <article class="price-card">
                    <h4>Banho e Tosa</h4>
                    <p>€40,00</p>
                </article>
                <article class="price-card">
                    <h4>Tratamento Profilático</h4>
                    <p>€30,00</p>
                </article>
            </div>
        </section>

        <section class="timeline-preview">
            <div class="timeline-preview-header">
                <h3>Linha do Tempo de Saúde</h3>
                <a href="LinhaTempo.php" class="btn btn-primary">Ver Linha do Tempo Completa</a>
            </div>
            <div class="timeline-preview-list">
                <article class="timeline-card">
                    <span class="timeline-date">12 fev 2026</span>
                    <h4>Vacinação anual</h4>
                    <p>Vacina antirrábica e múltipla administradas com sucesso.</p>
                </article>
                <article class="timeline-card">
                    <span class="timeline-date">05 jan 2026</span>
                    <h4>Alergia alimentar</h4>
                    <p>Registada alergia a proteína de frango; iniciado protocolo de dieta de eliminação.</p>
                </article>
                <article class="timeline-card">
                    <span class="timeline-date">08 dez 2025</span>
                    <h4>Consulta de rotina</h4>
                    <p>Exame clínico completo, controlo de peso e atualização de historial médico.</p>
                </article>
            </div>
        </section>

        <section class="audience-section">
            <div class="audience-card audience-tutor">
                <h4>Para os Tutores</h4>
                <p>Aceda rapidamente ao histórico do seu animal, peça agendamentos e acompanhe a saúde do seu companheiro de forma natural e sem complicações.</p>
            </div>
            <div class="audience-card audience-clinic">
                <h4>Para os Profissionais / Clínicas</h4>
                <p>Otimize a rotina do seu estabelecimento. Reduza erros de agendamento e tenha um repositório centralizado e inviolável para os seus pacientes.</p>
            </div>
        </section>
    </main>

    <footer>
        <p>Projeto desenvolvido para a Unidade Curricular de Programação Web I e II – IEES (2025/2026).</p>
        <p>Desenvolvido por Gabriel Lins e Tiago Silva  .</p>
    </footer>
</body>
</html>
