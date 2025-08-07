<?php
header('Content-Type: application/json');
require_once '../db_connect.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['id'])) {
        echo json_encode(['error' => true, 'message' => 'ID da reserva não fornecido.']);
        exit;
    }

    $id = intval($data['id']);

    $stmt = $pdo->prepare("DELETE FROM reserva WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['error' => false, 'message' => 'Reserva cancelada com sucesso.']);

} catch (PDOException $e) {
    echo json_encode(['error' => true, 'message' => 'Erro ao cancelar reserva: ' . $e->getMessage()]);
}
