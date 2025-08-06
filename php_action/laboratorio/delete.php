<?php
header('Content-Type: application/json');

require_once '../conexao.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => true, 'message' => 'ID do laboratório não fornecido']);
    exit;
}

$id = intval($data['id']);

// Verifica se o laboratório existe
$sqlCheck = "SELECT * FROM Laboratorio WHERE id = ?";
$stmtCheck = $mysqli->prepare($sqlCheck);
$stmtCheck->bind_param("i", $id);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => true, 'message' => 'Laboratório não encontrado']);
    exit;
}

// Realiza a exclusão
$sql = "DELETE FROM Laboratorio WHERE id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(['error' => false, 'message' => 'Laboratório excluído com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['error' => true, 'message' => 'Erro ao excluir o laboratório: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
?>
