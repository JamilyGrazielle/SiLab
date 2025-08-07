<aside class="sidebar">
    <div class="menu">
        <i class="fas fa-bars" id="menu-sidebar"></i> 
    </div>

    <div class="perfil">
        <img src="Imagens/user.png" alt="Avatar do Usuário" class="avatar">
        <h2 id="nome-usuario">Carregando...</h2>
        <p id="matricula-usuario">Matrícula: ...</p>
    </div>

    <hr class="separador">

    <nav class="navegacao-principal">
        <?php if (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'adm'): ?>
            <a href="painel-admin-agenda.php" class="nav-link" data-tooltip="Agenda Geral">
                <i class="fas fa-calendar-alt"></i> <span>Agenda</span>
            </a>
            <a href="painel-admin-cadastro.php" class="nav-link" data-tooltip="Cadastro">
                <i class="fas fa-clipboard"></i> <span>Laboratórios</span>
            </a>
        <?php elseif (isset($_SESSION['perfil']) && $_SESSION['perfil'] == 'Professor'): ?>
            <a href="painel-professor-agenda.php" class="nav-link" data-tooltip="Agenda Geral">
                <i class="fas fa-calendar-alt"></i> <span>Agenda</span>
            </a>
            <a href="painel-professor-reserva.php" class="nav-link" data-tooltip="Reserva">
                <i class="fas fa-bookmark"></i> <span>Reserva</span>
            </a>
        <?php endif; ?>
    </nav>

    <hr class="separador">

    <a href="index.php" class="nav-link botao-sair" data-tooltip="Sair">
        <i class="fas fa-sign-out-alt"></i>
        <span>Sair</span>
    </a>
</aside>