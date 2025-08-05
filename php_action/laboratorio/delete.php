<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_SESSION['user_profile'] == 'adm') {
    $lab_id = $_POST['id'];

    try {
        $pdo->beginTransaction();
        
        // Remover relacionamentos com equipamentos
        $stmt = $pdo->prepare("DELETE FROM Laboratorio_Equipamento WHERE laboratorio_id = ?");
        $stmt->execute([$lab_id]);
        
        // Remover laboratório
        $stmt = $pdo->prepare("DELETE FROM Laboratorio WHERE id = ?");
        $stmt->execute([$lab_id]);
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Laboratório removido com sucesso!']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => true, 'message' => 'Erro ao remover laboratório: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Acesso não autorizado']);
}
?>