<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && $_SESSION['user_profile'] == 'adm') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    try {
        $pdo->beginTransaction();
        
        // Atualizar laboratório
        $stmt = $pdo->prepare("UPDATE Laboratorio SET nome = ?, capacidade = ? WHERE id = ?");
        $stmt->execute([$data['nome'], $data['capacidade'], $data['id']]);
        
        // Remover equipamentos antigos
        $stmt = $pdo->prepare("DELETE FROM Laboratorio_Equipamento WHERE laboratorio_id = ?");
        $stmt->execute([$data['id']]);
        
        // Adicionar novos equipamentos
        foreach ($data['equipamentos'] as $equip) {
            // Verificar se equipamento já existe
            $stmt = $pdo->prepare("SELECT id FROM Equipamento WHERE nome = ?");
            $stmt->execute([$equip['nome']]);
            $equip_id = $stmt->fetchColumn();
            
            if (!$equip_id) {
                $stmt = $pdo->prepare("INSERT INTO Equipamento (nome) VALUES (?)");
                $stmt->execute([$equip['nome']]);
                $equip_id = $pdo->lastInsertId();
            }
            
            // Relacionar equipamento com laboratório
            $stmt = $pdo->prepare("INSERT INTO Laboratorio_Equipamento (laboratorio_id, equipamento_id, quantidade) VALUES (?, ?, ?)");
            $stmt->execute([$data['id'], $equip_id, $equip['quantidade']]);
        }
        
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Laboratório atualizado com sucesso!']);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['error' => true, 'message' => 'Erro ao atualizar laboratório: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Acesso não autorizado']);
}
?>