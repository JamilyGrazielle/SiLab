<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db_connect.php';

try {
    if (!isset($_POST['id']) || !isset($_POST['perfil'])) {
        echo json_encode(['success' => false, 'message' => 'Dados incompletos.']);
        exit;
    }

    $id = (int) $_POST['id'];
    $perfil = trim($_POST['perfil']);

    $stmt = $pdo->prepare("UPDATE Usuario SET perfil = ? WHERE id = ?");
    $stmt->execute([$perfil, $id]);

    echo json_encode(['success' => true, 'message' => 'Perfil atualizado com sucesso.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
