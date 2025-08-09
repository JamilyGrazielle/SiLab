<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "
        SELECT 
            l.id, 
            l.nome, 
            l.capacidade, 
            COALESCE(
                GROUP_CONCAT(CONCAT(e.nome, ' (', le.quantidade, ')') SEPARATOR ', '),
                'Nenhum equipamento'
            ) AS equipamentos
        FROM Laboratorio l
        LEFT JOIN Laboratorio_Equipamento le ON l.id = le.laboratorio_id
        LEFT JOIN Equipamento e ON le.equipamento_id = e.id
        GROUP BY l.id, l.nome, l.capacidade
    ";

    $stmt = $pdo->query($query);
    $laboratorios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $laboratorios
    ]);

} catch (PDOException $e) {
    error_log('Erro no read.php: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar laboratórios',
        'error' => $e->getMessage()
    ]);
}
