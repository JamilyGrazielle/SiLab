<?php
require_once 'db_connect.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $matricula = trim($_POST['matricula']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO Usuario (nome_completo, matricula, email, senha, perfil, status) 
                VALUES (?, ?, ?, ?, 'Professor', 'pendente')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $matricula, $email, $senha]);

        $response['success'] = true;
        $response['message'] = 'Solicitação enviada com sucesso! Aguarde a aprovação do administrador.';
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'Erro: ' . $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
