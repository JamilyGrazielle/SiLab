<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor - SiLab</title>
    <link rel="stylesheet" href="Estilo/estilo.css">
    <script src="https://kit.fontawesome.com/0f27c66bcc.js" crossorigin="anonymous"></script>
    <style>
        #principal-login {
            width: 500px;
            background-color: #F7F9FC;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-top: 2em;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        #principal-login h2 {
            color: #2D3748;
            margin-bottom: 15px;
        }
        
        .form-grupo {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .form-grupo label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #2D3748;
        }
        
        .form-grupo input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #38A169;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button[type="submit"]:hover {
            background-color: #2d8555;
        }
        
        #mensagem {
            margin: 15px 0;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body id="login">
    <main id="principal-login">
        <a href="index.php" class="logo-link">
            <img src="Imagens/logo.png" alt="Logo SiLab" class="logo-imagem">
        </a>

        <h2>Cadastro de Professor</h2>

        <p>Preencha os dados abaixo para solicitar o cadastro.</p>

        <form id="form-cadastro" method="POST">
            <div class="form-grupo">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" placeholder="Seu nome completo" required>
            </div>
            
            <div class="form-grupo">
                <label for="matricula">Matrícula</label>
                <input type="text" id="matricula" name="matricula" placeholder="Sua matrícula" required>
            </div>
            
            <div class="form-grupo">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Seu e-mail" required>
            </div>
            
            <div class="form-grupo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Crie uma senha" required>
            </div>

            
            
            <div class="form-grupo">
                <label for="confirmar_senha">Confirme a Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha" required>
            </div>
            
            <div id="mensagem"></div>
            
            <button type="submit">
                <span id="submit-text">Solicitar Cadastro</span>
                <span id="submit-loading" style="display:none;">
                    <i class="fas fa-spinner fa-spin"></i> Enviando...
                </span>
            </button>
        </form>
        
        <div id="login-alternativo" style="margin-top: 20px; text-align: center;">
            <a href="login.php">Voltar para o login</a>
        </div>
    </main>
    
    <!-- <div id="footer-login">
        <?php require_once 'includes/footer.php';?>
    </div> -->
    
    <script>
        document.getElementById('form-cadastro').addEventListener('submit', async function (event) {
            event.preventDefault();
            
            const nome = document.getElementById('nome').value;
            const matricula = document.getElementById('matricula').value;
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const confirmarSenha = document.getElementById('confirmar_senha').value;
            const mensagem = document.getElementById('mensagem');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');
            
            // Resetar mensagem
            mensagem.innerHTML = '';
            mensagem.style.color = '';
            
            // Validar senhas
            if (senha !== confirmarSenha) {
                mensagem.innerHTML = 'As senhas não coincidem!';
                mensagem.style.color = 'red';
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
                    mensagem.style.color = 'green';
                    
                    // Limpar formulário após sucesso
                    document.getElementById('form-cadastro').reset();
                    
                    // Redirecionar após 3 segundos
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 3000);
                } else {
                    mensagem.innerHTML = result.message;
                    mensagem.style.color = 'red';
                }
            } catch (error) {
                mensagem.innerHTML = 'Erro ao enviar solicitação. Tente novamente mais tarde.';
                mensagem.style.color = 'red';
                console.error('Erro:', error);
            } finally {
                // Restaurar botão
                submitText.style.display = 'inline';
                submitLoading.style.display = 'none';
            }
        });
    </script>
</body>
</html>