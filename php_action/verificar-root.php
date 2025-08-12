<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';

try {
    if (!isset($_POST['senha'])) {
        echo json_encode(['success' => false, 'message' => 'Senha não informada.']);
        exit;
    }

    $senha = $_POST['senha'];

    // Busca usuário root
    $stmt = $pdo->prepare("SELECT senha FROM Usuario WHERE matricula = '20251DC0000' LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Usuário root não encontrado.']);
        exit;
    }

    if (password_verify($senha, $user['senha'])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Senha incorreta.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
}
