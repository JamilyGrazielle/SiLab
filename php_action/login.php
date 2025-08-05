<?php
session_start();
include('conexao.php');

$matricula = $_POST['matricula'];
$senha = $_POST['senha'];

// Proteção contra SQL Injection
$matricula = $mysqli->real_escape_string($matricula);

// Busca o usuário com status ativo
$sql = "SELECT * FROM Usuario WHERE matricula = '$matricula' AND status = 'ativo'";
$result = $mysqli->query($sql);

if ($result && $result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    // Verifica a senha com hash
    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['matricula'] = $usuario['matricula'];
        $_SESSION['perfil'] = $usuario['perfil'];
        $_SESSION['nome_completo'] = $usuario['nome_completo'];

        if ($usuario['perfil'] === 'Professor') {
            header("Location: ../painel-professor-agenda.html");
        } elseif ($usuario['perfil'] === 'adm') {
            header("Location: ../painel-admin-agenda.html");
        } else {
            echo "<script>alert('Perfil inválido'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Senha incorreta.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Usuário não encontrado ou inativo.'); window.history.back();</script>";
}
?>
