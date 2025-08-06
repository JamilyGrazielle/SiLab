<?php
session_start();
require_once 'conexao.php';

// Lê dados enviados
$matricula = $_POST['matricula'] ?? '';
$senha = $_POST['senha'] ?? '';

$sql = "SELECT * FROM Usuario WHERE matricula = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['user_id'] = $usuario['matricula'];
        $_SESSION['user_name'] = $usuario['nome_completo'];
        $_SESSION['user_profile'] = $usuario['perfil'];

        echo json_encode(['success' => true]);
        exit;
    }
}

// Login inválido
http_response_code(401);
echo json_encode(['success' => false, 'message' => 'Matrícula ou senha invalidas.']);
exit;
?>
