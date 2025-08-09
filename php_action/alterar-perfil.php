<?php
session_start();
require_once __DIR__ . '/../db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['perfil'] !== 'adm') {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['id']) || empty($data['novoPerfil'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

$id = intval($data['id']);
$novoPerfil = $data['novoPerfil'] === 'adm' ? 'adm' : 'Professor';

try {
    $stmt = $pdo->prepare("UPDATE Usuario SET perfil = ? WHERE id = ?");
    $stmt->execute([$novoPerfil, $id]);
    
    echo json_encode(['success' => true, 'message' => 'Perfil atualizado']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar perfil: ' . $e->getMessage()]);
}