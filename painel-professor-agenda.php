<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor - SiLab</title>
    <link rel="stylesheet" href="Estilo/estilo.css">
    <script src="https://kit.fontawesome.com/0f27c66bcc.js" crossorigin="anonymous"></script>
</head>
<body class="painel">

    <?php require_once 'includes/header-logado.php';?>

    <?php require_once 'includes/sidebar.php';?>


    <main id="principal03" style="margin-bottom: 600px;">
        <!-- <form action=""></form>
            <fieldset id="filtro-calendario">
                <legend><strong>Filtrar por:</strong></legend>

                <div>
                    <input type="radio" id="radio-semana" name="criterio" value="semana">
                    <label for="radio-semana">Semana</label>
                </div>
                    
                <div>
                    <input type="radio" id="radio-mes" name="criterio" value="mes">
                    <label for="radio-mes">Mês</label>
                </div>

                
                <div>
                    <input type="radio" id="radio-laboratorio" name="criterio" value="laboratorio">
                    <label for="radio-laboratorio">
                        <select name="criterio">
                            <option value="segunda">Lab25</option>
                            <option value="terça">Lab27</option>
                            <option value="quarta">MaraMaker</option>
                            <option value="quinta">Lab Synthesis</option>
                        </select>
                    </label>
                </div>

                
            </fieldset>
        </form>

        <table>
            <tr>
                <td>Teste</td>
                Teste
            </tr>
            <tr>Teste</tr>
            <tr>Teste</tr>
        </table> -->

    </main>

    <?php require_once 'includes/footer.php';?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
        const toggleButton = document.getElementById('menu-sidebar');
        const sidebar = document.querySelector('.sidebar');
        const body = document.body;
    
        if (toggleButton && sidebar && body) {
            toggleButton.addEventListener('click', function () {
                sidebar.classList.toggle('recolhida');
                body.classList.toggle('sidebar-recolhida');
            });
        }
    
        // Pega nome e matrícula do PHP
        fetch('php_action/session-info.php')
            .then(response => response.json())
            .then(data => {
                if (data.nome_completo && data.matricula) {
                    document.getElementById('nome-usuario').textContent = data.nome_completo;
                    document.getElementById('matricula-usuario').textContent = "Matrícula: " + data.matricula;
                } else {
                    console.error('Erro ao buscar dados da sessão');
                }
            })
            .catch(error => console.error('Erro:', error));
        });
    </script>
        
</body>
</html>