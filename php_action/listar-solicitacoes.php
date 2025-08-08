<?php
require_once __DIR__ . '/../db_connect.php';

header('Content-Type: application/json');

try {
    // Buscar solicitações pendentes
    $sql = "SELECT * FROM Usuario 
    WHERE perfil = 'Professor' 
    AND status = 'pendente'";

    
    $stmt = $pdo->query($sql);
    $solicitacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($solicitacoes);
} catch (PDOException $e) {
    // Log de erro para depuração
    error_log('Erro em listar-solicitacoes: ' . $e->getMessage());
    
    echo json_encode([]);
}