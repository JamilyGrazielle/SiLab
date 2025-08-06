<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $matricula = $_POST['matricula'];
    $senha = $_POST['senha'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE matricula = ? AND status = 'ativo'");
        $stmt->execute([$matricula]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['user_id'] = $user['matricula'];
            $_SESSION['user_name'] = $user['nome_completo'];
            $_SESSION['user_profile'] = $user['perfil'];
            
            if ($user['perfil'] == 'adm') {
                header('Location: ../painel-admin-agenda.php');
            } else {
                header('Location: ../painel-professor-agenda.php');
            }
            exit();
        } else {
            header('Location: ../login.php?error=1');
            exit();
        }
    } catch (PDOException $e) {
        header('Location: ../login.php?error=2');
        exit();
    }
}
?>