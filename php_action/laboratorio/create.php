<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

// Verificar se o usuário é admin
if (!isset($_SESSION['perfil']) || $_SESSION['perfil'] !== 'adm') {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

try {
    // Validar dados obrigatórios
    if (empty($data['nome']) || empty($data['capacidade'])) {
        throw new Exception('Nome e capacidade são obrigatórios');
    }

    $pdo->beginTransaction();
    
    // Inserir laboratório
    $stmt = $pdo->prepare("INSERT INTO Laboratorio (nome, capacidade) VALUES (?, ?)");
    $stmt->execute([$data['nome'], $data['capacidade']]);
    $lab_id = $pdo->lastInsertId();
    
    // Inserir equipamentos (se existirem)
    if (!empty($data['equipamentos'])) {
        foreach ($data['equipamentos'] as $equip) {
            // Verificar se o equipamento já existe
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
            $stmt->execute([$lab_id, $equip_id, $equip['quantidade']]);
        }
    }
    
    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Laboratório cadastrado com sucesso!', 'id' => $lab_id]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
?>