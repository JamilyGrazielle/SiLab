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
        /* ... (mantenha os estilos existentes) ... */
        
        /* Adicione este novo estilo para a tabela */
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
        
        <table class="tabela-usuarios">
            <thead>
                <tr>
                    <th>Matrícula</th>
                    <th>Nome</th>
                    <th>Status</th>
                    <th>Perfil</th>
                    <th>Ações</th>
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
        // Função para carregar usuários
        async function carregarUsuarios(termo = '') {
            const tbody = document.getElementById('tabela-corpo');
            tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;"><i class="fas fa-spinner loading"></i> Carregando...</td></tr>';
            
            try {
                const url = termo 
                    ? `php_action/listar-usuarios.php?pesquisa=${encodeURIComponent(termo)}`
                    : 'php_action/listar-usuarios.php';
                
                const response = await fetch(url);
                const usuarios = await response.json();
                
                if (usuarios.error) {
                    throw new Error(usuarios.message || 'Erro ao carregar usuários');
                }
                
                if (usuarios.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Nenhum usuário encontrado</td></tr>';
                    return;
                }
                
                let html = '';
                usuarios.forEach(usuario => {
                    html += `
                        <tr>
                            <td>${usuario.matricula}</td>
                            <td>${usuario.nome_completo}</td>
                            <td>${usuario.status}</td>
                            <td>${usuario.perfil}</td>
                            <td class="coluna-acoes">
                                <button class="botao-perfil" 
                                        onclick="alterarPerfil(${usuario.id}, '${usuario.perfil}')">
                                    Alterar Perfil
                                </button>
                            </td>
                        </tr>
                    `;
                });
                
                tbody.innerHTML = html;
            } catch (error) {
                console.error('Erro ao carregar usuários:', error);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; color: red;">
                            Erro ao carregar usuários: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        // Função para alterar o perfil do usuário
        async function alterarPerfil(id, perfilAtual) {
            const novoPerfil = perfilAtual === 'adm' ? 'Professor' : 'adm';
            
            if (!confirm(`Tem certeza que deseja alterar o perfil para ${novoPerfil}?`)) {
                return;
            }
            
            try {
                const response = await fetch('php_action/alterar-perfil.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id, novoPerfil })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Perfil alterado com sucesso!');
                    // Recarregar a lista de usuários
                    carregarUsuarios(document.getElementById('pesquisa').value);
                } else {
                    throw new Error(result.message || 'Erro ao alterar perfil');
                }
            } catch (error) {
                alert('Erro: ' + error.message);
            }
        }

        // Quando o documento estiver carregado
        document.addEventListener('DOMContentLoaded', function() {
            // Carregar usuários inicialmente
            carregarUsuarios();
            
            // Configurar o botão de pesquisa
            document.getElementById('botao-pesquisar').addEventListener('click', function() {
                const termo = document.getElementById('pesquisa').value;
                carregarUsuarios(termo);
            });
            
            // Permitir pesquisa ao pressionar Enter
            document.getElementById('pesquisa').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    carregarUsuarios(this.value);
                }
            });
            
            // Carregar informações do usuário no sidebar
            fetch('php_action/session-info.php')
                .then(response => response.json())
                .then(data => {
                    if (data.nome_completo && data.matricula) {
                        document.getElementById('nome-usuario').textContent = data.nome_completo;
                        document.getElementById('matricula-usuario').textContent = "Matrícula: " + data.matricula;
                    }
                })
                .catch(error => console.error('Erro ao carregar sessão:', error));
            
            // Configurar sidebar
            const toggleButton = document.getElementById('menu-sidebar');
            const sidebar = document.querySelector('.sidebar');
            
            if (toggleButton && sidebar) {
                toggleButton.addEventListener('click', function() {
                    sidebar.classList.toggle('recolhida');
                });
            }
        });
    </script>
</body>
</html>