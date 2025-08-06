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
    
   <!-- <script>
        const form = document.getElementById('form-login');
        const matriculaInput = document.getElementById('matricula');
        const senhaInput = document.getElementById('senha');


        form.addEventListener('submit', function(event) {

            event.preventDefault(); 

            const matricula = matriculaInput.value;
            const senha = senhaInput.value;


            if (matricula === 'prof20251' && senha === 'prof123') {
                window.location.href = 'painel-professor.html';

            } else if (matricula === 'adm20251' && senha === 'adm123') {
                window.location.href = 'painel-admin.html';
            } 
        });
    </script> -->

</body>
</html>