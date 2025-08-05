<?php
require_once '../db_connect.php';

// Parâmetros para filtragem
$data_inicio = $_GET['data_inicio'] ?? date('Y-m-d');
$data_fim = $_GET['data_fim'] ?? date('Y-m-d', strtotime('+1 week'));
$laboratorio_id = $_GET['laboratorio_id'] ?? null;

try {
    $query = "SELECT r.id, l.nome as laboratorio, u.nome_completo as professor, 
                     d.nome as disciplina, r.data, h.descricao as horario
              FROM Reserva r
              JOIN Laboratorio l ON r.laboratorio_id = l.id
              JOIN Usuario u ON r.professor_matricula = u.matricula
              JOIN Disciplina d ON r.disciplina_id = d.id
              JOIN Horario h ON r.horario_id = h.id
              WHERE r.data BETWEEN ? AND ?";
    
    $params = [$data_inicio, $data_fim];
    
    if ($laboratorio_id) {
        $query .= " AND r.laboratorio_id = ?";
        $params[] = $laboratorio_id;
    }
    
    $query .= " ORDER BY r.data, h.id";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reservas);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao buscar reservas: ' . $e->getMessage()]);
}
?>