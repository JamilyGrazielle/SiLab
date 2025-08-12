<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$modo_professor = true;
$titulo_pagina = "Agenda Professor - SiLab";
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?></title>
    <link rel="stylesheet" href="Estilo/estilo.css">
    <script src="https://kit.fontawesome.com/0f27c66bcc.js" crossorigin="anonymous"></script>
    <style>
        .principal03 {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .sidebar.recolhida + .principal03 {
            margin-left: 80px;
        }
        .agenda-container {
            overflow-x: auto;
            margin-top: 20px;
        }
        
        .tabela-agenda {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
    </style>
</head>
<body class="painel">
    <?php require_once 'includes/header-logado.php'; ?>
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="principal03">
        <h1>Minha Agenda</h1>
        <?php include 'agenda.php'; ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('php_action/session-info.php')
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert("Sessão expirada. Faça login novamente.");
                        window.location.href = 'login.php';
                    } else {
                        document.getElementById('nome-usuario').textContent = data.nome_completo;
                        document.getElementById('matricula-usuario').textContent = 'Matrícula: ' + data.matricula;
                    }
                })
                .catch(error => {
                    console.error("Erro ao buscar sessão:", error);
                    alert("Erro na autenticação.");
                    window.location.href = 'login.php';
                });

            const toggleButton = document.getElementById('menu-sidebar');
            const sidebar = document.querySelector('.sidebar');
            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('recolhida');
                });
            }
        });
    </script>
</body>
</html>