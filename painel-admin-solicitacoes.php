<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['perfil'] !== 'adm') {
    header('Location: login.php');
    exit;
}

$titulo_pagina = "Solicitações de Cadastro - SiLab";
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
        .tabela-solicitacoes {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .tabela-solicitacoes th, 
        .tabela-solicitacoes td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .tabela-solicitacoes th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .tabela-solicitacoes tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .acoes-cell {
            display: flex;
            gap: 5px;
        }
        
        .botao-acao {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
        }
        
        .botao-aprovar {
            background-color: #28a745;
            color: white;
        }
        
        .botao-rejeitar {
            background-color: #dc3545;
            color: white;
        }
        
        .loading-container {
            text-align: center;
            padding: 20px;
        }
        
        .error-container {
            text-align: center;
            padding: 20px;
            color: #dc3545;
        }
    </style>
</head>
<body class="painel">
    <?php require_once 'includes/header-logado.php'; ?>
    <?php require_once 'includes/sidebar.php'; ?>

    <main class="principal03">
        <h1>Solicitações de Cadastro</h1>
        
        <table class="tabela-lab">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Matrícula</th>
                    <th>E-mail</th>
                    <th>Data de Solicitação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-corpo">
                <tr>
                    <td colspan="5" class="loading-container">
                        <i class="fas fa-spinner fa-spin"></i> Carregando solicitações...
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="paginacao-container"></div>
    </main>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        function renderizarPaginacao(paginationInfo) {
            const { currentPage, totalPages } = paginationInfo;
            const container = document.getElementById("paginacao-container");
            container.innerHTML = ''; 

            if (totalPages <= 1) return;

            let paginationHTML = '<nav class="paginacao"><ul>';

            if (currentPage > 1) {
                paginationHTML += `<li class="pagina-item"><a href="#" onclick="event.preventDefault(); carregarSolicitacoes(${currentPage - 1})">Anterior</a></li>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                const activeClass = (i === currentPage) ? 'ativo' : '';
                paginationHTML += `<li class="pagina-item ${activeClass}"><a href="#" onclick="event.preventDefault(); carregarSolicitacoes(${i})">${i}</a></li>`;
            }

            if (currentPage < totalPages) {
                paginationHTML += `<li class="pagina-item"><a href="#" onclick="event.preventDefault(); carregarSolicitacoes(${currentPage + 1})">Próximo</a></li>`;
            }

            paginationHTML += '</ul></nav>';
            container.innerHTML = paginationHTML;
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Carregar informações do usuário
            fetch('php_action/session-info.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('nome-usuario').textContent = data.nome_completo;
                        document.getElementById('matricula-usuario').textContent = 'Matrícula: ' + data.matricula;
                    }
                })
                .catch(error => console.error('Erro ao carregar sessão:', error));

            carregarSolicitacoes();
            
            // Configurar sidebar
            const toggleButton = document.getElementById('menu-sidebar');
            const sidebar = document.querySelector('.sidebar');
            
            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('recolhida');
                });
            }
        });
        
        async function carregarSolicitacoes(pagina = 1) {
            const tbody = document.getElementById('tabela-corpo');
            const paginacaoContainer = document.getElementById('paginacao-container');
            
            if(paginacaoContainer) paginacaoContainer.innerHTML = ''; 
            tbody.innerHTML = '<tr><td colspan="5" class="loading-container"><i class="fas fa-spinner fa-spin"></i> Carregando...</td></tr>';
            
            try {
                const url = `php_action/listar-solicitacoes.php?page=${pagina}`;
                const response = await fetch(url);
                
                const result = await response.json(); 
                
                if (result.success) {
                    const solicitacoes = result.data; 
                    
                    if (solicitacoes.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px;">Nenhuma solicitação pendente</td></tr>';
                        return;
                    }
                    
                    let html = '';
                    solicitacoes.forEach(sol => {
                        html += `
                            <tr>
                                <td>${sol.nome_completo}</td>
                                <td>${sol.matricula}</td>
                                <td>${sol.email}</td>
                                <td>${sol.data_solicitacao_formatada || 'N/A'}</td>
                                <td class="acoes-cell">
                                    <button class="botao-acao botao-aprovar" title="Aprovar" onclick="aprovarCadastro(${sol.id})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="botao-acao botao-rejeitar" title="Rejeitar" onclick="rejeitarCadastro(${sol.id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    
                    tbody.innerHTML = html;
                    
                    renderizarPaginacao(result.pagination);

                } else {
                    throw new Error(result.message || 'Erro ao carregar solicitações');
                }

            } catch (error) {
                console.error('Erro ao carregar solicitações:', error);
                tbody.innerHTML = `<tr><td colspan="5" class="error-container"><i class="fas fa-exclamation-triangle"></i> Erro: ${error.message}</td></tr>`;
            }
        }
        
        async function aprovarCadastro(id) {
            if (!confirm('Tem certeza que deseja aprovar este cadastro?')) return;
            
            try {
                const response = await fetch('php_action/aprovar-cadastro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    carregarSolicitacoes();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                console.error('Erro ao aprovar cadastro:', error);
                alert('Erro ao aprovar cadastro. Tente novamente.');
            }
        }
        
        async function rejeitarCadastro(id) {
            if (!confirm('Tem certeza que deseja rejeitar este cadastro?')) return;
            
            try {
                const response = await fetch('php_action/rejeitar-cadastro.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    carregarSolicitacoes();
                } else {
                    alert('Erro: ' + result.message);
                }
            } catch (error) {
                console.error('Erro ao rejeitar cadastro:', error);
                alert('Erro ao rejeitar cadastro. Tente novamente.');
            }
        }
    </script>
</body>
</html>