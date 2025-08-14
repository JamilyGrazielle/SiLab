<?php 
$modo_convidado = true;
$titulo_pagina = "Agenda Pública - SiLab";
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
        .agenda-container {
            margin: 20px auto;
            max-width: 1200px;
        }
        
        .tabela-agenda {
            width: 100%;
            border-collapse: collapse;
        }

    </style>
</head>
<body>
    <?php require_once 'includes/header-principal.php'; ?>

    <main>
        <h1 style="text-align: center;">Agenda de Laboratórios</h1>
        <?php include 'agenda.php'; ?>
    </main>

    <?php require_once 'includes/footer.php'; ?>
</body>
</html>