<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor - SiLab</title>
    <link rel="stylesheet" href="Estilo/estilo.css">
    <script src="https://kit.fontawesome.com/0f27c66bcc.js" crossorigin="anonymous"></script>
</head>
<body class="login">
    <main class="principal-login">
        <a href="index.php" class="logo-link">
            <img src="Imagens/logo.png" alt="Logo SiLab" class="logo-imagem">
        </a>

        <h2>Cadastro de Professor</h2>

        <p>Preencha os dados abaixo para solicitar o cadastro.</p>

        <form id="form-cadastro" method="POST">

            <label for="nome">Nome Completo</label>
            <input type="text" id="nome" name="nome" placeholder="Digite seu nome completo..." required autofocus>

            <label for="matricula">Matrícula</label>
            <input type="text" id="matricula" name="matricula" placeholder="Digite sua matrícula..." required>

            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="Digite seu e-mail..." required>
            
            <label for="senha">Senha</label>
            <div class="senha-mascara">
                <input type="password" id="senha" name="senha" placeholder="Crie uma senha..." required>
                <i class="fas fa-eye toggle-senha"></i>
            </div>
            
            <label for="confirmar_senha">Confirme a Senha</label>
            <div class="senha-mascara">
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha..." required>
                <i class="fas fa-eye toggle-senha"></i>
            </div>
            
            
            <div id="mensagem-erro"></div>
            
            <button type="submit">
                <span id="submit-text">Solicitar Cadastro</span>
                <span id="submit-loading" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> Enviando...
                </span>
            </button>

            <hr class="separador">
        </form>
        
        
        <div class="login-alternativo">
            <a href="login.php">Voltar para o login</a>
        </div>
    </main>
    
    <script>
        document.getElementById('form-cadastro').addEventListener('submit', async function (event) {
            event.preventDefault();
            
            const nome = document.getElementById('nome').value;
            const matricula = document.getElementById('matricula').value;
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const mensagem = document.getElementById('mensagem-erro');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');
            
            // Resetar mensagem
            mensagem.innerHTML = '';
            mensagem.style.color = '';
            
            // Validar senhas
            if (senha !== confirmarSenha) {
                mensagem.innerHTML = 'As senhas não coincidem!';
                mensagem.style.color = '#E74C3C';
                return;
            }
            
            // Mostrar loading
            submitText.style.display = 'none';
            submitLoading.style.display = 'inline';
            
            try {
                const formData = new FormData();
                formData.append('nome', nome);
                formData.append('matricula', matricula);
                formData.append('email', email);
                formData.append('senha', senha);
                
                const response = await fetch('php_action/solicitar_cadastro.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    mensagem.innerHTML = result.message;
                    mensagem.style.color = '#4CAF50';
                    
                    // Limpar formulário após sucesso
                    document.getElementById('form-cadastro').reset();
                    
                    // Redirecionar após 3 segundos
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 3000);
                } else {
                    mensagem.innerHTML = result.message;
                    mensagem.style.color = '#E74C3C';
                }
            } catch (error) {
                mensagem.innerHTML = 'Erro ao enviar solicitação. Tente novamente mais tarde.';
                mensagem.style.color = '#E74C3C';
                console.error('Erro:', error);
            } finally {
                // Restaurar botão
                submitText.style.display = 'inline';
                submitLoading.style.display = 'none';
            }
        });

        document.addEventListener('DOMContentLoaded', function() {

            const toggleIcons = document.querySelectorAll('.toggle-senha');

            toggleIcons.forEach(icon => {
    
                icon.addEventListener('click', function() {
    
                const senhaInput = this.previousElementSibling;
                const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
                senhaInput.setAttribute('type', type);
        
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');

                });
            });
        });
    </script>
</body>
</html>