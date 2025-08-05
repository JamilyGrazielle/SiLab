<?php
session_start();
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lab_id = $_POST['laboratorio_id'];
    $disciplina_id = $_POST['disciplina_id'];
    $data = $_POST['data'];
    $horario_id = $_POST['horario_id'];
    $professor_matricula = $_SESSION['user_id'];

    try {
        // Verificar conflito de reserva
        $stmt = $pdo->prepare("SELECT id FROM Reserva WHERE laboratorio_id = ? AND data = ? AND horario_id = ?");
        $stmt->execute([$lab_id, $data, $horario_id]);
        
        if ($stmt->fetch()) {
            echo json_encode(['error' => true, 'message' => 'Já existe uma reserva para este laboratório no horário selecionado']);
            exit();
        }
        
        // Criar reserva
        $stmt = $pdo->prepare("INSERT INTO Reserva (professor_matricula, laboratorio_id, disciplina_id, data, horario_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$professor_matricula, $lab_id, $disciplina_id, $data, $horario_id]);
        
        echo json_encode(['success' => true, 'message' => 'Reserva criada com sucesso!']);
    } catch (PDOException $e) {
        echo json_encode(['error' => true, 'message' => 'Erro ao criar reserva: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => true, 'message' => 'Método não permitido']);
}
?>