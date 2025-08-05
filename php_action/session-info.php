<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['erro' => 'Sessão não iniciada']);
    exit();
}

echo json_encode([
    'matricula' => $_SESSION['user_id'],
    'nome_completo' => $_SESSION['user_name'],
    'perfil' => $_SESSION['user_profile']
]);
?>