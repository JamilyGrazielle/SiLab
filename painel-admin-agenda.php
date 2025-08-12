<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$modo_admin = true;
$titulo_pagina = "Agenda Admin - SiLab";
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
            margin-left: 10px;
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
        
        .tabela-agenda th, .tabela-agenda td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            vertical-align: top;
        }
        
        .tabela-agenda th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .hora-coluna {
            background-color: #e9f7fe;
            font-weight: bold;
            width: 120px;
        }
        
        .turno-header {
            background-color: #d1ecf1;
            font-weight: bold;
        }
        
        .turno-header td {
            padding: 10px;
            text-align: center;
        }
        
        .reservado {
            background-color: #f8d7da;
        }
        
        .disponivel {
            background-color: #d4edda;
        }
        
        .fim-de-semana {
            background-color: #f0f0f0;
        }
        
        .reserva-info {
            padding: 5px;
            font-size: 0.85em;
        }
        
        .reserva-lab {
            font-weight: bold;
        }
        
        .reserva-prof {
            font-size: 0.8em;
            margin: 3px 0;
        }
        
        .reserva-acoes {
            margin-top: 5px;
        }
        
        .botao-reservar {
            background: none;
            border: none;
            cursor: pointer;
            color: #28a745;
            font-size: 1.2em;
        }
        
        .filtros-agenda {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .form-filtros {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .filtro-group {
            display: flex;
            flex-direction: column;
        }
        
        .botao-filtrar, .botao-nova-reserva {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .botao-filtrar {
            background-color: #6c757d;
            color: white;
        }
        
        .botao-nova-reserva {
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body class="painel">
    <?php require_once 'includes/header-logado.php'; ?>
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="principal03">
        <h1>Agenda de Laboratórios</h1>
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