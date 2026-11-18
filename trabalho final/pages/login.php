<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sessão</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <h1>Sistema de Início de Sessão</h1>
    </header>

    <nav>
        <ul class="lista">
            <li><a href="index.php">Início</a></li>
        </ul>
    </nav>

    <main class="login-container">
        <div class="login-box">
            <h2>Iniciar sessão</h2>
            
            <form class="login-form" action="../actions/processa_login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email ou Utilizador:</label>
                    <input type="text" id="email" name="email" placeholder="Introduza o seu email ou utilizador" required>
                </div>

                <div class="form-group">
                    <label for="senha">Palavra-passe:</label>
                    <div class="input-group">
                        <input type="password" id="senha" name="senha" placeholder="Introduza a sua palavra-passe" required>
                        <button type="button" class="btn-show-password" onclick="togglePassword('senha')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>

            <div class="login-links">
                <a href="Registar.php" class="link-criar">Criar conta</a>
            </div>
        </div>
    </main>

<footer>
        <p>Projeto desenvolvido para a Unidade Curricular de Programação Web I e II – IEES (2025/2026).</p>
        <p>Desenvolvido por Gabriel Lins e Tiago Silva  .</p>
    </footer>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = event.target.closest('.btn-show-password');
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
