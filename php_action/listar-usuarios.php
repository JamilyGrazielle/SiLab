<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db_connect.php'; // conexão

try {
    $pesquisa = isset($_GET['pesquisa']) ? trim($_GET['pesquisa']) : '';

    $sql = "SELECT id, matricula, nome_completo, status, perfil 
            FROM Usuario
            WHERE matricula != '20251DC0000'";

    $params = [];
    if ($pesquisa !== '') {
        $sql .= " AND (matricula LIKE :pesquisa OR nome_completo LIKE :pesquisa)";
        $params[':pesquisa'] = "%{$pesquisa}%";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $usuarios
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar usuários: ' . $e->getMessage()
    ]);
}
