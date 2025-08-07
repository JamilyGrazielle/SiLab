<?php
// Define o cabeçalho para retornar JSON
header('Content-Type: application/json');

// Importa a conexão com o banco de dados
require_once '../db_connect.php';

try {
    // Recebe os dados em JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Verifica se o ID foi enviado
    if (!isset($data['id'])) {
        echo json_encode([
            'error' => true,
            'message' => 'ID do laboratório não foi fornecido.'
        ]);
        exit;
    }

    $id = intval($data['id']);

    // Prepara e executa a exclusão
    $stmt = $pdo->prepare("DELETE FROM laboratorio WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode([
        'error' => false,
        'message' => 'Laboratório excluído com sucesso.'
    ]);

} catch (PDOException $e) {
    // Verifica se o erro é de integridade (laboratório com reservas)
    if ($e->getCode() === '23000') {
        echo json_encode([
            'error' => true,
            'message' => 'Este laboratório não pode ser excluído pois possui reservas associadas.'
        ]);
    } else {
        echo json_encode([
            'error' => true,
            'message' => 'Erro ao excluir laboratório: ' . $e->getMessage()
        ]);
    }
}
