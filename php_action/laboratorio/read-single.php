<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) {
    echo json_encode(['error' => 'ID do laboratório não fornecido']);
    exit();
}

$labId = $_GET['id'];

try {
    // Obter informações básicas do laboratório
    $stmt = $pdo->prepare("SELECT * FROM Laboratorio WHERE id = ?");
    $stmt->execute([$labId]);
    $laboratorio = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$laboratorio) {
        echo json_encode(['error' => 'Laboratório não encontrado']);
        exit();
    }
    
    // Obter equipamentos do laboratório
    $stmt = $pdo->prepare("
        SELECT e.nome, le.quantidade 
        FROM Laboratorio_Equipamento le
        JOIN Equipamento e ON le.equipamento_id = e.id
        WHERE le.laboratorio_id = ?
    ");
    $stmt->execute([$labId]);
    $equipamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $laboratorio['equipamentos'] = $equipamentos;
    
    echo json_encode($laboratorio);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao buscar laboratório: ' . $e->getMessage()]);
}
?>