<?php
// php_action/login.php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

$matricula = $_POST['matricula'] ?? '';
$senha = $_POST['senha'] ?? '';

if (empty($matricula) || empty($senha)) {
    echo json_encode([
        'success' => false,
        'message' => 'Matrícula e senha são obrigatórias'
    ]);
    exit;
}

try {
    // Tenta achar usuário aprovado
    $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE matricula = ? AND status = 'aprovado' LIMIT 1");
    $stmt->execute([$matricula]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Usuário existe e está aprovado — checar senha
        if (password_verify($senha, $usuario['senha'])) {
            // Login OK
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['perfil'] = $usuario['perfil'];
            $_SESSION['matricula'] = $usuario['matricula'];
            $_SESSION['nome_completo'] = $usuario['nome_completo'];

            echo json_encode([
                'success' => true,
                'perfil' => $usuario['perfil']
            ]);
            exit;
        } else {
            // Usuário aprovado mas senha incorreta -> mensagem de credenciais
            echo json_encode([
                'success' => false,
                'message' => 'Matrícula ou senha incorretas'
            ]);
            exit;
        }
    }

    // Se chegou aqui, não existe usuário aprovado — verificar solicitação (se houver)
    $stmt = $pdo->prepare("SELECT * FROM SolicitacaoCadastro WHERE matricula = ? LIMIT 1");
    $stmt->execute([$matricula]);
    $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($solicitacao) {
        if ($solicitacao['status'] === 'pendente') {
            echo json_encode([
                'success' => false,
                'message' => 'Seu cadastro ainda não foi aprovado pelo administrador.'
            ]);
            exit;
        } elseif ($solicitacao['status'] === 'aprovado') {
            echo json_encode([
                'success' => false,
                'message' => 'Seu cadastro foi aprovado! Por favor, aguarde alguns minutos e tente novamente.'
            ]);
            exit;
        }
    }

    // Nenhum usuário e nenhuma solicitação relevante -> credenciais inválidas
    echo json_encode([
        'success' => false,
        'message' => 'Matrícula ou senha incorretas'
    ]);
    exit;

} catch (PDOException $e) {
    error_log('Erro no login: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro no servidor. Tente novamente mais tarde.'
    ]);
    exit;
}
