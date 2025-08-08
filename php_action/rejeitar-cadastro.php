<?php
require_once __DIR__ . '/../db_connect.php';

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
    // Atualizar status da solicitação
    $stmt = $pdo->prepare("UPDATE Usuario SET status = 'rejeitado' WHERE id = ?");
    $stmt->execute([$id]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Cadastro rejeitado com sucesso!'
    ]);
} catch (PDOException $e) {
    // Log de erro para depuração
    error_log('Erro em rejeitar-cadastro: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao rejeitar cadastro: ' . $e->getMessage()
    ]);
}