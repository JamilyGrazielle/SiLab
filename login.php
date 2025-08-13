<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Estilo/estilo.css">
    <script src="https://kit.fontawesome.com/0f27c66bcc.js" crossorigin="anonymous"></script>
    <title>Login - SiLab</title>
</head>
<body class="login">

    <main class="principal-login">
      <a href="index.php" class="logo-link">
        <img src="Imagens/logo.png" alt="Logo SiLab" class="logo-imagem">
      </a>

      <h2>Login</h2>

      <p>Digite sua matricula e senha.</p>

      <form id="form-login" method="POST" action="php_action/login.php">
        
        <label for="matricula">Matrícula</label> <br />
        <input id="matricula" name="matricula" type="text" placeholder="Digite sua matrícula..." autofocus /> <br />

        <label for="senha">Senha</label> <br />
        <div class="senha-mascara">
         <input id="senha" name="senha" type="password" placeholder="Digite sua senha..."/>
         <i class="fas fa-eye toggle-senha"></i>
        </div>
        
        <div id="mensagem-erro"></div>

        <button type="submit">Entrar</button>

        <hr class="separador">

        <div class="login-alternativo">
         <a href="cadastro-professor.php">Cadastrar-se como professor</a>
         <a href="painel-convidado.php">Entrar como convidado</a>
        </div>
      </form>

    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-login');
            const senhaInput = document.getElementById('senha');
            const toggleIcon = document.querySelector('.toggle-senha'); 

            if (senhaInput && toggleIcon) {
                toggleIcon.addEventListener('click', function() {
                    const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    senhaInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }

            if (form) {
                form.addEventListener('submit', async function(event) {
                    event.preventDefault();

                    const matricula = document.getElementById('matricula').value;
                    const senha = document.getElementById('senha').value;
                    const mensagemErro = document.getElementById('mensagem-erro');
                    const submitButton = form.querySelector('button[type="submit"]');

                    if (matricula.trim() === '' || senha.trim() === '') {
                        mensagemErro.textContent = 'Por favor, preencha todos os campos.';
                        return;
                    }

                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Entrando... <i class="fas fa-spinner fa-spin"></i>';
                    mensagemErro.textContent = '';

                    try {
                        const response = await fetch('php_action/login.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `matricula=${encodeURIComponent(matricula)}&senha=${encodeURIComponent(senha)}`
                        });

                        const result = await response.json();

                        if (result.success) {
                            window.location.href = (result.perfil === 'adm') 
                                ? 'painel-admin-agenda.php' 
                                : 'painel-professor-agenda.php';
                        } else {
                            mensagemErro.textContent = result.message || 'Erro ao fazer login.';
                            submitButton.disabled = false; 
                            submitButton.textContent = 'Entrar';
                        }
                    } catch (error) {
                        console.error('Erro:', error);
                        mensagemErro.textContent = 'Erro de conexão. Tente novamente.';
                        submitButton.disabled = false;
                        submitButton.textContent = 'Entrar';
                    }
                });
            }
        });

        window.addEventListener('load', function() {
        
            const elementoAlvo = document.querySelector('.principal-login');
            
            if (elementoAlvo) {
                elementoAlvo.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>

</body>
</html>