<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

try {
    // Consulta para obter todos os laboratórios com seus equipamentos
    $query = "SELECT l.id, l.nome, l.capacidade, 
                     GROUP_CONCAT(CONCAT(e.nome, ' (', le.quantidade, ')') AS equipamentos
              FROM Laboratorio l
              LEFT JOIN Laboratorio_Equipamento le ON l.id = le.laboratorio_id
              LEFT JOIN Equipamento e ON le.equipamento_id = e.id
              GROUP BY l.id";
    
    $stmt = $pdo->query($query);
    $laboratorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Adicionar status de sucesso
    echo json_encode([
        'success' => true,
        'data' => $laboratorios
    ]);
    
} catch (PDOException $e) {
    // Log do erro para debug
    error_log('Erro no read.php: ' . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao buscar laboratórios',
        'error' => $e->getMessage()
    ]);
}
?>