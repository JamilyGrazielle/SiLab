<?php
require_once 'db_connect.php';

// Iniciar sessão
session_start();

// Obter dados do formulário
$matricula = $_POST['matricula'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validar entrada
if (empty($matricula) || empty($senha)) {
    echo json_encode(['success' => false, 'message' => 'Matrícula e senha são obrigatórias']);
    exit;
}

try {
    $sql = "SELECT * FROM Usuario WHERE matricula = :matricula";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['matricula' => $matricula]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['nome_completo'] = $usuario['nome_completo'];
        $_SESSION['matricula'] = $usuario['matricula'];
        $_SESSION['perfil'] = $usuario['perfil'];
        
        echo json_encode([
            'success' => true, 
            'perfil' => $usuario['perfil']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Matrícula ou senha incorretas']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no servidor: ' . $e->getMessage()]);
}