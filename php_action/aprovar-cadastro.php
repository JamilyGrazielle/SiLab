<?php
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? 0;

if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID inválido'
    ]);
    exit;
}

try {
    // Buscar dados da solicitação
    $stmt = $pdo->prepare("SELECT * FROM SolicitacaoCadastro WHERE id = ?");
    $stmt->execute([$id]);
    $solicitacao = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$solicitacao) {
        echo json_encode([
            'success' => false,
            'message' => 'Solicitação não encontrada'
        ]);
        exit;
    }
    
    // Inserir novo usuário
    $sql = "INSERT INTO Usuario 
            (matricula, nome_completo, email, senha, perfil, status) 
            VALUES (?, ?, ?, ?, 'Professor', 'aprovado')";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $solicitacao['matricula'],
        $solicitacao['nome_completo'],
        $solicitacao['email'],
        $solicitacao['senha']
    ]);
    
    // Atualizar status da solicitação
    $stmt = $pdo->prepare("UPDATE SolicitacaoCadastro SET status = 'aprovado' WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cadastro aprovado com sucesso!'
    ]);
} catch (PDOException $e) {
    error_log('Erro em aprovar-cadastro: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao aprovar cadastro: ' . $e->getMessage()
    ]);
}