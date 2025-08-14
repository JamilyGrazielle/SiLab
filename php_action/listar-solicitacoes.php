<?php
require_once __DIR__ . '/db_connect.php';

header('Content-Type: application/json');

try {
    $itens_por_pagina = 5;
    $pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($pagina_atual - 1) * $itens_por_pagina;

    $total_stmt = $pdo->query("SELECT COUNT(id) FROM SolicitacaoCadastro WHERE status = 'pendente'");
    $total_itens = $total_stmt->fetchColumn();
    $total_paginas = ceil($total_itens / $itens_por_pagina);

    $sql = "SELECT 
                id, 
                nome_completo, 
                matricula, 
                email, 
                DATE_FORMAT(data_solicitacao, '%d/%m/%Y %H:%i') AS data_solicitacao_formatada
            FROM SolicitacaoCadastro 
            WHERE status = 'pendente'
            ORDER BY data_solicitacao ASC 
            LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Garantir que todos os registros tenham o campo formatado
    foreach ($solicitacoes as &$solicitacao) {
        if (!isset($solicitacao['data_solicitacao_formatada'])) {
            $solicitacao['data_solicitacao_formatada'] = 'N/A';
        }
    }
    
    echo json_encode([
        'success' => true,
        'data' => $solicitacoes,
        'pagination' => [
            'currentPage' => $pagina_atual,
            'totalPages' => $total_paginas
        ]
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar solicitações: ' . $e->getMessage()
    ]);
}
?>