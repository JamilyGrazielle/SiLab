<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['perfil'] !== 'adm') {
    header('Location: login.php');
    exit;
}

$titulo_pagina = "Gerenciar Usuários - SiLab";
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
            margin-left: 80px;
        }
        .tabela-usuarios {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .tabela-usuarios th, .tabela-usuarios td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .tabela-usuarios th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .tabela-usuarios tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .coluna-acoes {
            text-align: center;
        }
        
        .botao-perfil {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .loading {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="painel">
    <?php require_once 'includes/header-logado.php'; ?>
    <?php require_once 'includes/sidebar.php'; ?>
    
    <main class="principal03">
        <h1>Gerenciamento de Usuários</h1>
        
        <div class="barra-de-acoes">
            <form class="form-pesquisa">
                <label for="pesquisa"><strong>Pesquisar:</strong></label> 
                <input type="text" id="pesquisa" name="pesquisa" placeholder="Nome ou matrícula..."> 
                <button type="button" id="botao-pesquisar" class="botao-pesquisar">
                    <i class="fas fa-search"></i> Pesquisar
                </button>
            </form>
        </div>
        
        <table class="tabela-lab">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Perfil</th>
                </tr>
            </thead>
            <tbody id="tabela-corpo">
                <tr>
                    <td colspan="5" style="text-align: center;">
                        <i class="fas fa-spinner loading"></i> Carregando usuários...
                    </td>
                </tr>
            </tbody>
        </table>
    </main>
    
    <?php require_once 'includes/footer.php'; ?>
    
    <script>
    // Escapa HTML para evitar XSS
    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    async function carregarUsuarios(termo = '') {
        const tbody = document.getElementById('tabela-corpo');
        // Estado de carregando
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;"><i class="fas fa-spinner loading"></i> Carregando usuários...</td></tr>';

        try {
            const url = `php_action/listar-usuarios.php?pesquisa=${encodeURIComponent(termo)}`;
            const resp = await fetch(url, { cache: 'no-store' });
            const text = await resp.text();

            // Tenta parsear JSON, senão mostra erro (útil pra ver HTML com <br>)
            let result;
            try {
                result = JSON.parse(text);
            } catch (err) {
                console.error('Resposta inválida de listar-usuarios.php:', text);
                tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:red;">Erro no servidor — resposta inválida. Veja console (F12).</td></tr>`;
                return;
            }

            if (!result.success || !Array.isArray(result.data)) {
                console.error('listar-usuarios.php retornou erro:', result);
                tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:red;">Erro ao carregar usuários: ${escapeHtml(result.message || 'Resposta inesperada')}</td></tr>`;
                return;
            }

            const usuarios = result.data;
            if (usuarios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;">Nenhum usuário encontrado</td></tr>';
                return;
            }

            // Monta tabela com select de perfil
            let html = '';
            usuarios.forEach(user => {
                const perfilAtual = (user.perfil || '').trim();
                // opções: adm -> Administrador , Professor -> Professor
                const options = [
                    { val: 'adm', label: 'Administrador' },
                    { val: 'Professor', label: 'Professor' }
                ].map(o => `<option value="${o.val}" ${perfilAtual === o.val ? 'selected' : ''}>${escapeHtml(o.label)}</option>`).join('');

                html += `
                    <tr>
                        <td>${escapeHtml(user.matricula)}</td>
                        <td>${escapeHtml(user.nome_completo)}</td>
                        <td>${escapeHtml(user.status)}</td>
                        <td>
                            <select class="select-perfil" data-id="${escapeHtml(user.id)}" data-prev="${escapeHtml(perfilAtual)}">
                                ${options}
                            </select>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = html;

            // Adiciona listeners aos selects
            document.querySelectorAll('.select-perfil').forEach(select => {
                select.addEventListener('change', async function () {
    const id = this.dataset.id;
    const novoPerfil = this.value;
    const prevPerfil = this.dataset.prev || '';

    // Solicita senha root
    const senha = prompt("Digite a senha do usuário root para confirmar:");
    if (!senha) {
        this.value = prevPerfil;
        return;
    }

    // Valida senha no servidor
    try {
        const resSenha = await fetch('php_action/verificar-root.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `senha=${encodeURIComponent(senha)}`
        });

        const txtSenha = await resSenha.text();
        let dataSenha;
        try {
            dataSenha = JSON.parse(txtSenha);
        } catch {
            console.error("Resposta inválida de verificar-root.php:", txtSenha);
            alert("Erro ao validar senha do root.");
            this.value = prevPerfil;
            return;
        }

        if (!dataSenha.success) {
            alert(dataSenha.message || "Senha incorreta.");
            this.value = prevPerfil;
            return;
        }
    } catch (err) {
        console.error("Erro na verificação da senha root:", err);
        alert("Erro de comunicação com o servidor.");
        this.value = prevPerfil;
        return;
    }

    // Continua com a alteração de perfil
    this.disabled = true;
    try {
        const res = await fetch('php_action/alterar-perfil.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${encodeURIComponent(id)}&perfil=${encodeURIComponent(novoPerfil)}`
        });

        const txt = await res.text();
        let data;
        try {
            data = JSON.parse(txt);
        } catch (err) {
            console.error('Resposta inválida de alterar-perfil.php:', txt);
            alert('Erro inesperado ao salvar.');
            this.value = prevPerfil;
            return;
        }

        if (!data.success) {
            alert('Erro: ' + (data.message || 'Não foi possível atualizar o perfil.'));
            this.value = prevPerfil;
        } else {
            this.dataset.prev = novoPerfil;
            alert("Perfil alterado com sucesso.");
        }
    } catch (err) {
        console.error('Falha na requisição alterar-perfil.php:', err);
        alert('Erro de comunicação com o servidor.');
        this.value = prevPerfil;
    } finally {
        this.disabled = false;
    }
});

            });

        } catch (error) {
            console.error('Erro em carregarUsuarios:', error);
            tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:red;">Erro ao carregar usuários. Veja o console (F12).</td></tr>`;
        }
    }

    // Conecta botão de pesquisa (se existir)
    document.addEventListener('DOMContentLoaded', function () {
        const botaoPesquisar = document.getElementById('botao-pesquisar');
        const inputPesquisa = document.getElementById('pesquisa');

        if (botaoPesquisar && inputPesquisa) {
            botaoPesquisar.addEventListener('click', function (e) {
                e.preventDefault();
                const termo = inputPesquisa.value.trim();
                carregarUsuarios(termo);
            });

            // Enter no campo
            inputPesquisa.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    carregarUsuarios(this.value.trim());
                }
            });
        }

        // Carrega lista ao abrir a página
        carregarUsuarios();
    });
    </script>

</body>
</html>