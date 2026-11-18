<?php
session_start();
require_once '../includes/conexao.php';

// Proteção: Apenas administradores podem executar estas ações
if (!isset($_SESSION['usuario_id']) || $_SESSION['is_admin'] != 1) {
    die("Acesso negado.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acao = $_POST['acao'];

    try {
    if ($acao === 'apagar_consulta') {
            $id = $_POST['id_consulta'];
            $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = ?");
            $stmt->execute([$id]);
        }
        
    if ($acao === 'apagar_comentario') {
            $id = $_POST['id_avaliacao'];
            $stmt = $pdo->prepare("DELETE FROM avaliacoes WHERE id = ?");
            $stmt->execute([$id]);
        } 
        elseif ($acao === 'responder_comentario') {
            $id = $_POST['id_avaliacao'];
            $resposta = trim($_POST['resposta']);
            $stmt = $pdo->prepare("UPDATE avaliacoes SET resposta_admin = ? WHERE id = ?");
            $stmt->execute([$resposta, $id]);
        }
        elseif ($acao === 'promover_admin') {
            $id = $_POST['id_usuario'];
            $stmt = $pdo->prepare("UPDATE usuarios SET is_admin = 1 WHERE id = ?");
            $stmt->execute([$id]);
        }
        elseif ($acao === 'rebaixar_admin') {
            if ($_SESSION['usuario_id'] == 1) {
                $id = $_POST['id_usuario'];
                $stmt = $pdo->prepare("UPDATE usuarios SET is_admin = 0 WHERE id = ?");
                $stmt->execute([$id]);
            } else {
                die("Operação bloqueada: Apenas o Administrador Principal pode remover cargos.");
            }
        }
        //Apagar conta de utilizador comum
        elseif ($acao === 'apagar_usuario') {
            $id = $_POST['id_usuario'];
            
            //Garante que não apaga a própria conta nem o Admin Principal (ID 1)
            if ($id != 1 && $id != $_SESSION['usuario_id']) {
                $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ? AND is_admin = 0");
                $stmt->execute([$id]);
            }
        }

        // Volta para o painel de administração automaticamente
        header("Location: ../pages/admin.php");
        exit();

    } catch (Exception $e) {
        //mostra a mensagem de erro se algo falhar
        die("Erro ao executar ação: " . $e->getMessage());
    }
}
?>