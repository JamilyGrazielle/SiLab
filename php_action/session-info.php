<?php
session_start();

if (isset($_SESSION['matricula']) && isset($_SESSION['nome_completo'])) {
    echo json_encode([
        'matricula' => $_SESSION['matricula'],
        'nome_completo' => $_SESSION['nome_completo'],
        'perfil' => $_SESSION['perfil']
    ]);
} else {
    echo json_encode(['erro' => 'Usuário não autenticado']);
}
