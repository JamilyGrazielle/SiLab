<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => true, 'message' => 'Acesso não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validar dados
    if (empty($data['id'])) {
        throw new Exception('ID da reserva não informado');
    }

    // Verificar se o usuário tem permissão para cancelar
    $sql_check = "SELECT usuario_id FROM Reserva WHERE id = :id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['id' => $data['id']]);
    $reserva = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$reserva) {
        throw new Exception('Reserva não encontrada');
    }
    
    // Permitir apenas se for o dono da reserva ou admin
    if ($reserva['usuario_id'] != $_SESSION['user_id'] && $_SESSION['perfil'] !== 'adm') {
        throw new Exception('Você não tem permissão para cancelar esta reserva');
    }

    // Atualizar status para cancelada
    $sql = "UPDATE Reserva SET status = 'cancelada' WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $data['id']]);
    
    echo json_encode(['success' => true, 'message' => 'Reserva cancelada com sucesso']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}