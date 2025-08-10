<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilo/estilo.css">
    <script src="https://kit.fontawesome.com/0f27c66bcc.js" crossorigin="anonymous"></script>
    <title>Login - SiLab</title>
</head>
<body id="login">

    <main id="principal-login">
      <a href="index.php" class="logo-link">
        <img src="Imagens/logo.png" alt="Logo SiLab" class="logo-imagem">
      </a>

      <p>Digite sua matricula e senha.</p>
      <form id="form-login" method="POST" action="php_action/login.php">
        
        <label for="matricula"><strong>Matrícula</strong></label> <br />
        <input id="matricula" name="matricula" type="text" placeholder="Digite sua matrícula..." autofocus /> <br />

        <label for="senha"><strong>Senha</strong></label> <br />
        <div class="senha-mascara">
         <input id="senha" name="senha" type="text" placeholder="Digite sua senha..." />
         <i class="fas fa-eye" id="toggle-senha"></i>
        </div>
        
        <div id="mensagem-erro"></div>

        <button type="submit"><strong>Entrar</strong></button>

        <hr class="separador">

        <div id="login-alternativo">
         <a href="cadastro-professor.php">Cadastrar-se como professor</a>
         <a href="painel-convidado.php">Entrar como convidado</a>
        </div>
      </form>

    </main>
    
    <script>
    document.getElementById('form-login').addEventListener('submit', async function (event) {
        event.preventDefault();

        const matricula = document.getElementById('matricula').value;
        const senha = document.getElementById('senha').value;
        const mensagemErro = document.getElementById('mensagem-erro');

        if (matricula.trim() === '' || senha.trim() === '') {
            mensagemErro.textContent = 'Por favor, preencha todos os campos.';
            return; 
        }

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
                if (result.perfil === 'adm') {
                    window.location.href = 'painel-admin-agenda.php';
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

    document.addEventListener('DOMContentLoaded', function() {

        const senhaInput = document.getElementById('senha');
        const toggleIcon = document.getElementById('toggle-senha');

        if (senhaInput && toggleIcon) {

            toggleIcon.addEventListener('click', function() {
                
                const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
                
                senhaInput.setAttribute('type', type);
                
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });
    </script>

</body>
</html>