<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilo/estilo.css">
    <title>Login - SiLab</title>
</head>
<body id="login">

    <main id="principal02">
      <img src="Imagens/logo.png" alt="Logo do SiLab">

      <p>Digite sua matricula e senha.</p>
      <form id="form-login" method="POST" action="php_action/login.php">

      <div id="mensagem-erro" style="color: red; font-weight: bold; margin-top: 10px;"></div>
        
        <label for="usuario"><strong>Usuário</strong></label> <br />
        <input id="matricula" name="matricula" type="text" placeholder="Digite sua matrícula..." autofocus /> <br />

        <label for="senha"><strong>Senha</strong></label> <br />
        <input id="senha" name="senha" type="password" placeholder="Digite sua senha..." /> <br />

        <button type="submit"><strong>Entrar</strong></button>
      </form>

      

      <div id="login-alternativo">
        <a href="painel-convidado.php">Entre como convidado</a>
      </div>

    </main>

    <div id="footer-login">
      <?php require_once 'includes/footer.php';?>
    </div>
    
    <script>
    document.getElementById('form-login').addEventListener('submit', async function (event) {
        event.preventDefault();

        const matricula = document.getElementById('matricula').value;
        const senha = document.getElementById('senha').value;
        const mensagemErro = document.getElementById('mensagem-erro');

        try {
            const response = await fetch('php_action/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `matricula=${encodeURIComponent(matricula)}&senha=${encodeURIComponent(senha)}`
            });

            const result = await response.json();

            if (result.success) {
                // Redirecionar conforme perfil
                if (result.perfil === 'adm') {
                    window.location.href = 'painel-admin-cadastro.php';
                } else {
                    window.location.href = 'painel-professor-agenda.php';
                }
            } else {
                mensagemErro.textContent = result.message || 'Erro ao fazer login.';
            }

        } catch (error) {
            console.error('Erro:', error);
            mensagemErro.textContent = 'Erro de conexão. Tente novamente mais tarde.';
        }
    });
    </script>


    <script>
    document.getElementById('form-login').addEventListener('submit', async function (event) {
        event.preventDefault();

        const matriculaInput = document.getElementById('matricula');
        const senhaInput = document.getElementById('senha');
        const mensagemErro = document.getElementById('mensagem-erro');

        const matricula = matriculaInput.value;
        const senha = senhaInput.value;

        try {
            const response = await fetch('php_action/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `matricula=${encodeURIComponent(matricula)}&senha=${encodeURIComponent(senha)}`
            });

            const result = await response.json();

            if (result.success) {
                // Redirecionar conforme perfil
                if (result.perfil === 'adm') {
                    window.location.href = 'painel-admin-cadastro.php';
                } else {
                    window.location.href = 'painel-professor-agenda.php';
                }
            } else {
                mensagemErro.textContent = result.message || 'Erro ao fazer login.';
                matriculaInput.value = '';
                senhaInput.value = '';
            }

        } catch (error) {
            console.error('Erro:', error);
            mensagemErro.textContent = 'Erro de conexão. Tente novamente mais tarde.';
            matriculaInput.value = '';
            senhaInput.value = '';
            matriculaInput.focus();
        }
    });
    </script>



</body>
</html>