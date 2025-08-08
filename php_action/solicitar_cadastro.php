<?php
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

try {
    // Validar campos obrigatórios
    $camposObrigatorios = ['nome', 'matricula', 'email', 'senha'];
    foreach ($camposObrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("O campo '$campo' é obrigatório");
        }
    }

    $nome = trim($_POST['nome']);
    $matricula = trim($_POST['matricula']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    // Validações específicas
    if (strlen($nome) < 5) {
        throw new Exception('Nome deve ter no mínimo 5 caracteres');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Formato de e-mail inválido');
    }

    if (strlen($senha) < 6) {
        throw new Exception('Senha deve ter no mínimo 6 caracteres');
    }

    // Verificar se matrícula já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SolicitacaoCadastro WHERE matricula = ?");
    $stmt->execute([$matricula]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Matrícula já possui solicitação pendente');
    }

    // Verificar se e-mail já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SolicitacaoCadastro WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('E-mail já possui solicitação pendente');
    }

    // Verificar se já existe usuário ativo com mesma matrícula
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Usuario WHERE matricula = ? AND status = 'ativo'");
    $stmt->execute([$matricula]);
    if ($stmt->fetchColumn() > 0) {
        throw new Exception('Matrícula já cadastrada no sistema');
    }

    // Criptografar senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir solicitação
    $sql = "INSERT INTO SolicitacaoCadastro 
            (nome_completo, matricula, email, senha, status, data_solicitacao) 
            VALUES (?, ?, ?, ?, 'pendente', NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $matricula, $email, $senhaHash]);

    echo json_encode([
        'success' => true,
        'message' => 'Solicitação enviada com sucesso! Aguarde aprovação do administrador.'
    ]);

} catch (PDOException $e) {
    error_log('Erro no banco de dados: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Erro no servidor. Tente novamente mais tarde.'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}