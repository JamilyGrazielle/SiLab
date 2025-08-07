<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erro' => true, 'message' => 'Sessão não iniciada']);
    exit;
}

// Retorna informações básicas da sessão
echo json_encode([
    'nome_completo' => $_SESSION['nome_completo'] ?? '',
    'matricula' => $_SESSION['matricula'] ?? '',
    'perfil' => $_SESSION['perfil'] ?? ''
]);