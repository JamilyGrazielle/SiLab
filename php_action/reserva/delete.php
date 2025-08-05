<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reserva_id = $_POST['id'];
    $professor_matricula = $_SESSION['user_id'];
    $is_admin = ($_SESSION['user_profile'] == 'adm');

    try {
        // Verificar se o usuário pode cancelar esta reserva
        if (!$is_admin) {
            $stmt = $pdo->prepare("SELECT id FROM Reserva WHERE id = ? AND professor_matricula = ?");
            $stmt->execute([$reserva_id, $professor_matricula]);
            
            if (!$stmt->fetch()) {
                echo json_encode(['error' => true, 'message' => 'Você não tem permissão para cancelar esta reserva']);
                exit();
            }
        }
        
        // Cancelar reserva
        $stmt = $pdo->prepare("DELETE FROM Reserva WHERE id = ?");
        $stmt->execute([$reserva_id]);
        
        echo json_encode(['success' => true, 'message' => 'Reserva cancelada com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['error' => true, 'message' => 'Erro ao cancelar reserva: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Método não permitido']);
}
?>