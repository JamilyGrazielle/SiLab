<?php
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

try {
    $sql = "SELECT 
                id, 
                nome_completo, 
                matricula, 
                email, 
                DATE_FORMAT(data_solicitacao, '%d/%m/%Y %H:%i') AS data_solicitacao_formatada
            FROM SolicitacaoCadastro 
            WHERE status = 'pendente'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Garantir que todos os registros tenham o campo formatado
    foreach ($solicitacoes as &$solicitacao) {
        if (!isset($solicitacao['data_solicitacao_formatada'])) {
            $solicitacao['data_solicitacao_formatada'] = 'N/A';
        }
    }
    
    echo json_encode($solicitacoes);
} catch (PDOException $e) {
    echo json_encode([
        'error' => true,
        'message' => 'Erro ao buscar solicitações: ' . $e->getMessage()
    ]);
}