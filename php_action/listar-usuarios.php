<?php
// php_action/listar-usuarios.php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php'; // conexão

try {

    $itens_por_pagina = 5; 
    $pagina_atual = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($pagina_atual - 1) * $itens_por_pagina;

    $pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';
    
    $sql_count = "SELECT COUNT(id) FROM Usuario WHERE matricula != '20251DC0000'";
    $params_count = [];
    if ($pesquisa !== '') {
        $sql_count .= " AND (matricula LIKE :pesquisa OR nome_completo LIKE :pesquisa)";
        $params_count[':pesquisa'] = "%{$pesquisa}%";
    }
    $total_stmt = $pdo->prepare($sql_count);
    $total_stmt->execute($params_count);
    $total_itens = $total_stmt->fetchColumn();
    $total_paginas = ceil($total_itens / $itens_por_pagina);

    $sql = "SELECT id, matricula, nome_completo, status, perfil 
            FROM Usuario
            WHERE matricula != '20251DC0000'";
    $params = [];
    if ($pesquisa !== '') {
        $sql .= " AND (matricula LIKE :pesquisa OR nome_completo LIKE :pesquisa)";
        $params[':pesquisa'] = "%{$pesquisa}%";
    }
    $sql .= " ORDER BY nome_completo ASC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $itens_por_pagina, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    if ($pesquisa !== '') {
        $stmt->bindValue(':pesquisa', $params[':pesquisa']);
    }

    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        'success' => true,
        'data' => $usuarios,
        'pagination' => [
            'currentPage' => $pagina_atual,
            'totalPages' => $total_paginas,
            'totalItems' => $total_itens
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    
}