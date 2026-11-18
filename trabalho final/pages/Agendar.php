<?php
session_start();
require_once '../includes/conexao.php';

// Se o utilizador não estiver logado, é redirecionado para o login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// VAI BUSCAR OS ANIMAIS DO UTILIZADOR
$stmt = $pdo->prepare("SELECT id, nome, especie FROM animais WHERE usuario_id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$meus_animais = $stmt->fetchAll();

// VAI BUSCAR O NOME E O NIF DO UTILIZADOR LOGADO PARA PREENCHIMENTO AUTOMÁTICO
$stmtUsuario = $pdo->prepare("SELECT nome, nif FROM usuarios WHERE id = ?");
$stmtUsuario->execute([$_SESSION['usuario_id']]);
$dados_usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$nome_tutor_logado = $dados_usuario['nome'];
$nif_tutor_logado = !empty($dados_usuario['nif']) ? $dados_usuario['nif'] : 'Não informado'; 
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Consulta - Clínica Veterinária CBTregiões</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <h1>Agendamento CBTregiões</h1>
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
        <section class="contact-section">
            <h2>Agendar Consulta</h2>
            <div class="location-card">
                <h3>Informações da clínica</h3>
                <p><strong>Localização:</strong> Rua das Flores, 123 - Centro, Fafe</p>
                <p><strong>Telefone:</strong> 000 000 000</p>
                <p><strong>Email:</strong> geral@cbtregioes.pt</p>
                <p><strong>Horário:</strong> Segunda a sexta, 08:00 às 18:00</p>
            </div>

            <div class="contact-form-card">
                <h3>Formulário de Agendamento</h3>
                <p>Preencha os dados abaixo para solicitar uma consulta para o seu animal.</p>
                <div class="price-table-card">
                    <h4>Tabela de Preços</h4>
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th>Serviço</th>
                                <th>Preço</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Consulta Clínica</td>
                                <td>€35,00</td>
                            </tr>
                            <tr>
                                <td>Vacinação</td>
                                <td>€25,00</td>
                            </tr>
                            <tr>
                                <td>Banho e Tosa</td>
                                <td>€40,00</td>
                            </tr>
                            <tr>
                                <td>Tratamento Profilático</td>
                                <td>€30,00</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="note small">Valores indicativos; o preço final poderá variar conforme o atendimento e as necessidades do animal.</p>
                </div>
                
                <?php if (isset($_GET['erro'])): ?>
                    <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #c62828;">
                        ⚠️ <b>Atenção:</b> <?= htmlspecialchars($_GET['erro']) ?>
                    </div>
                <?php endif; ?>
                <form id="agendamentoForm" action="../actions/processa_agendamento.php" method="POST" onsubmit="return enviarAgendamento(event)">
                
                    <div class="form-group">
                        <label for="usuario">Tutor(a) / Dono(a):</label>
                        <input 
                            type="text" 
                            id="usuario" 
                            name="usuario" 
                            value="<?= htmlspecialchars($nome_tutor_logado) ?>" 
                            readonly 
                            required
                            style="background-color: #e9ecef; cursor: not-allowed;"
                        >
                        <p style="font-size: 12px; color: #666; margin-top: 5px;">O agendamento será associado automaticamente à sua conta.</p>
                    </div>

                    <div class="form-group">
                        <label for="nif">NIF (Número de Identificação Fiscal):</label>
                        <input 
                            type="text" 
                            id="nif" 
                            name="nif" 
                            value="<?= htmlspecialchars($nif_tutor_logado) ?>" 
                            readonly 
                            style="background-color: #e9ecef; cursor: not-allowed; font-weight: bold; color: #555;"
                        >
                        <p class="note small">Preenchido automaticamente com os dados fiscais da sua conta.</p>
                    </div>

                    <div class="form-group">
                        <label for="doutor">Veterinário de Preferência:</label>
                        <select id="doutor" name="doutor" required>
                            <option value="">Selecione o doutor</option>
                            <option value="Dr. Gabriel Lins">Dr. Gabriel Lins</option>
                            <option value="Dr. Tiago Silva">Dr. Tiago Silva</option>
                        </select>
                    </div>    
                    
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="tel" id="telefone" name="telefone" placeholder="+351 9XX XXX XXX" required>
                        <p class="note small">Introduza seu telefone.</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="animal_id">Qual animal será atendido?</label>
                        <?php if (count($meus_animais) > 0): ?>
                            <select id="animal_id" name="animal_id" required style="padding: 10px; width: 100%; border: 1px solid #ccc; border-radius: 5px;">
                                <option value="">Selecione um dos seus animais...</option>
                                <?php foreach($meus_animais as $animal): ?>
                                    <option value="<?= $animal['id'] ?>"><?= htmlspecialchars($animal['nome']) ?> (<?= htmlspecialchars($animal['especie']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <div style="padding: 15px; background: #ffebee; color: #c62828; border-left: 4px solid #c62828; border-radius: 4px; font-size: 14px;">
                                ⚠️ <b>Atenção:</b> Precisa de cadastrar um animal antes de agendar uma consulta. <br>
                                <a href="Animais.php" style="color: #c62828; font-weight: bold; text-decoration: underline;">Clique aqui para cadastrar o seu primeiro animal.</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="servico">Tipo de Serviço:</label>
                        <select id="servico" name="servico" required>
                            <option value="">Selecione o serviço</option>
                            <option value="Consulta Clínica">Consulta Clínica - €35,00</option>
                            <option value="Vacinação">Vacinação - €25,00</option>
                            <option value="Banho e Tosa">Banho e Tosa - €40,00</option>
                            <option value="Tratamento Profilático">Tratamento Profilático - €30,00</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input type="date" id="data" name="data" required>
                    </div>

                    <div class="form-group">
                        <label for="hora">Hora:</label>
                        <input type="time" id="hora" name="hora" min="08:00" max="18:00" required>
                        <span class="note small">Horário de funcionamento: 08:00 às 18:00.</span>
                    </div>

                    <div class="form-group">
                        <label for="motivo">Motivo do serviço:</label>
                        <textarea id="motivo" name="motivo" rows="4" placeholder="Descreva o motivo" required></textarea>
                    </div>

                    <?php if (count($meus_animais) > 0): ?>
                        <button type="submit" class="btn btn-primary">Confirmar Agendamento</button>
                    <?php else: ?>
                        <button type="button" class="btn btn-primary" style="background-color: #999; cursor: not-allowed;" disabled>Cadastre um animal primeiro</button>
                    <?php endif; ?>
                </form>
            </div>
        </section>
    </main>

<footer>
        <p>Projeto desenvolvido para a Unidade Curricular de Programação Web I e II – IEES (2025/2026).</p>
        <p>Desenvolvido por Gabriel Lins e Tiago Silva.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', inicializarForm);

        function definirRegrasTempo() {
            const dataInput = document.getElementById('data');
            const horaInput = document.getElementById('hora');
            const hoje = new Date();
            
            let ano = hoje.getFullYear();
            let mes = String(hoje.getMonth() + 1).padStart(2, '0');
            let dia = String(hoje.getDate()).padStart(2, '0');
            let dataMinimaStr = `${ano}-${mes}-${dia}`;
            
            const horaAtualFormatada = String(hoje.getHours()).padStart(2, '0') + ':' + String(hoje.getMinutes()).padStart(2, '0');

            if (horaAtualFormatada > "18:00") {
                hoje.setDate(hoje.getDate() + 1);
                ano = hoje.getFullYear();
                mes = String(hoje.getMonth() + 1).padStart(2, '0');
                dia = String(hoje.getDate()).padStart(2, '0');
                dataMinimaStr = `${ano}-${mes}-${dia}`;
            }

            if(dataInput) {
                dataInput.min = dataMinimaStr;

                dataInput.addEventListener('input', function() {
                    if (this.value) {
                        const dataSelecionada = new Date(this.value + 'T00:00:00');
                        const diaSemana = dataSelecionada.getDay();
                        
                        if (diaSemana === 0 || diaSemana === 6) {
                            alert('A clínica encontra-se encerrada ao fim de semana. Por favor, escolha um dia útil (Segunda a Sexta-feira).');
                            this.value = '';
                            if(horaInput) horaInput.min = "08:00";
                        }
                    }
                });

                dataInput.addEventListener('change', function() {
                    const diaAtualReal = new Date();
                    const diaAtualRealStr = `${diaAtualReal.getFullYear()}-${String(diaAtualReal.getMonth() + 1).padStart(2, '0')}-${String(diaAtualReal.getDate()).padStart(2, '0')}`;
                    
                    if (this.value === diaAtualRealStr) {
                        const novaHoraAtual = String(diaAtualReal.getHours()).padStart(2, '0') + ':' + String(diaAtualReal.getMinutes()).padStart(2, '0');
                        if (novaHoraAtual > "08:00") {
                            if(horaInput) horaInput.min = novaHoraAtual;
                        } else {
                            if(horaInput) horaInput.min = "08:00";
                        }
                    } else {
                        if(horaInput) horaInput.min = "08:00";
                    }
                });
            }
        }

        // Configuração do telefone para limitar a exatos 9 dígitos
        function configurarTelefone() {
            const telefoneInput = document.getElementById('telefone');
            if(!telefoneInput) return;

            telefoneInput.addEventListener('focus', function() {
                if (this.value === '') {
                    this.value = '+351 ';
                }
            });

            telefoneInput.addEventListener('input', function(e) {
                if (!this.value.startsWith('+351 ')) {
                    this.value = '+351 ';
                }
                
                // Remove tudo o que não for número após o prefixo
                let numeros = this.value.substring(5).replace(/[^\d]/g, '');
                
                // Limita a exatos 9 números digitados pelo utilizador
                if (numeros.length > 9) {
                    numeros = numeros.substring(0, 9);
                }
                
                this.value = '+351 ' + numeros;
            });
        }

        //validações
        function inicializarForm() {
            definirRegrasTempo();
            configurarTelefone();
        }

        function enviarAgendamento(event) {
            const form = document.getElementById('agendamentoForm');
            const telefoneInput = form.telefone.value;
            const dataEscolhida = form.data.value;
            const horaEscolhida = form.hora.value;
            
            // 1. VALIDAÇÃO DO TELEFONE
            // Retira o prefixo '+351 ' e verifica quantos números sobram
            const numerosTelefone = telefoneInput.substring(5).trim();
            if (numerosTelefone.length > 0 && numerosTelefone.length !== 9) {
                event.preventDefault();
                alert('Erro: O número de telefone tem de ter exatamente 9 dígitos após o prefixo. Verifique o número introduzido.');
                document.getElementById('telefone').focus();
                return false;
            }

            const agora = new Date();
            const dataHojeStr = `${agora.getFullYear()}-${String(agora.getMonth() + 1).padStart(2, '0')}-${String(agora.getDate()).padStart(2, '0')}`;
            const horaAtualStr = String(agora.getHours()).padStart(2, '0') + ':' + String(agora.getMinutes()).padStart(2, '0');

            // 2. Verifica se a data é anterior a hoje
            if (dataEscolhida < dataHojeStr) {
                event.preventDefault();
                alert('A data de agendamento não pode ser no passado.');
                return false;
            }

            // 3. Verifica se a hora está fora do horário de funcionamento
            if (horaEscolhida < "08:00" || horaEscolhida > "18:00") {
                event.preventDefault();
                alert('O horário de funcionamento é entre as 08:00 e as 18:00.');
                return false;
            }

            // 4. Verifica se a data é hoje e a hora escolhida já passou
            if (dataEscolhida === dataHojeStr && horaEscolhida < horaAtualStr) {
                event.preventDefault();
                alert('Este horário já passou. Por favor, escolha um horário ou data futura.');
                return false;
            }
            
            // 5. VERIFICAÇÃO PARA O FIM DE SEMANA
            const dataObjSubmit = new Date(dataEscolhida + 'T00:00:00');
            const diaSubmit = dataObjSubmit.getDay();
            if (diaSubmit === 0 || diaSubmit === 6) {
                event.preventDefault();
                alert('Agendamento inválido: A clínica não se encontra aberta ao fim de semana.');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>