<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

header('Content-Type: application/json');

// Verificar se o usuário está logado (exceto para admin)
if (!isset($_SESSION['user_id']) || ($_SESSION['perfil'] !== 'adm' && $_SESSION['perfil'] !== 'Professor')) {
    echo json_encode(['error' => true, 'message' => 'Acesso não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

try {
    // Validar dados
    if (empty($data['laboratorio_id']) || empty($data['data']) || 
        empty($data['hora_inicio']) || empty($data['hora_fim']) || 
        empty($data['motivo'])) {
        throw new Exception('Todos os campos são obrigatórios');
    }

    // Verificar conflitos de horário
    $sql_check = "SELECT id FROM Reserva 
                 WHERE laboratorio_id = :lab_id 
                 AND data_reserva = :data_reserva 
                 AND (
                    (hora_inicio < :hora_fim AND hora_fim > :hora_inicio) OR 
                    (hora_inicio < :hora_fim2 AND hora_fim > :hora_fim2) OR 
                    (hora_inicio >= :hora_inicio2 AND hora_fim <= :hora_fim3)
                 ) 
                 AND status = 'confirmada'";
    
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([
        'lab_id' => $data['laboratorio_id'],
        'data_reserva' => $data['data'],
        'hora_fim' => $data['hora_fim'],
        'hora_inicio' => $data['hora_inicio'],
        'hora_fim2' => $data['hora_fim'],
        'hora_fim3' => $data['hora_fim'],
        'hora_inicio2' => $data['hora_inicio']
    ]);
    
    if ($stmt_check->rowCount() > 0) {
        throw new Exception('Já existe uma reserva confirmada para este horário');
    }

    // Inserir reserva
    $sql = "INSERT INTO Reserva 
            (laboratorio_id, usuario_id, data_reserva, hora_inicio, hora_fim, motivo, status) 
            VALUES (:lab_id, :user_id, :data_reserva, :hora_inicio, :hora_fim, :motivo, 'confirmada')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'lab_id' => $data['laboratorio_id'],
        'user_id' => $_SESSION['user_id'],
        'data_reserva' => $data['data'],
        'hora_inicio' => $data['hora_inicio'],
        'hora_fim' => $data['hora_fim'],
        'motivo' => $data['motivo']
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Reserva criada com sucesso']);
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => $e->getMessage()]);
}