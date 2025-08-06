<?php session_start() ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin - SiLab</title>
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
        
        .barra-de-acoes {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .form-pesquisa {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .botao-pesquisar, .botao-adicionar {
            padding: 8px 15px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .tabela-lab {
            width: 100%;
            border-collapse: collapse;
        }
        
        .tabela-lab th, .tabela-lab td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .tabela-lab th {
            font-weight: bold;
        }
        
        .coluna-acoes {
            display: flex;
            gap: 10px;
        }
        
        .botao-acao {
            padding: 5px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .botao-editar {
            color: white;
        }
        
        .botao-excluir {
            color: white;
        }
        
        .modal {
            display: none; 
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6); 
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal.ativo {
            opacity: 1;
            visibility: visible;
            animation: fadeIn 0.3s ease;
        }
        
        .modal-conteudo {
            background: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .modal-conteudo {
            background: white;
            padding: 25px; 
            border-radius: 10px;
            width: 90%;
            max-width: 550px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            overflow: hidden; 
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal.ativo .modal-conteudo {
            animation: slideDownFadeIn 0.3s ease forwards;
        }
        
        .fechar {
            background: none;
            border: none;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
            padding: 0 5px;
        }
        .fechar:hover { color: #333; }

        .modal-conteudo {
            margin-bottom: 20px;
        }

        .modal-conteudo label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .modal-conteudo input[type="text"],
        .modal-conteudo input[type="number"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
            transition: box-shadow 0.2s, border-color 0.2s;
        }

        .modal-conteudo input:focus {
            outline: none;
            border-color: var(--cor-primaria);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.3);
        }
        
        .linha-equipamento {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .linha-equipamento input[type="text"] { flex-grow: 1; }
        .linha-equipamento input[type="number"] { max-width: 80px; }
        
        .remover-equipamento {
            background: none;
            border: none;
            color: #f44336;
            cursor: pointer;
            font-size: 16px;
        }
        
        .botao-add-equipamento {
            background: none;
            border: none;
            color: #2196F3;
            cursor: pointer;
            font-size: 16px;
            padding: 8px 0;
            margin: 10px 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .botoes-modal {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        .botoes-modal button {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        
        .botao-cancelar {
            background-color: #f1f1f1;
        }
        
        .botao-salvar {
            background-color: #4CAF50;
            color: white;
        }

        /* Estilo do spinner */
        .loading {
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDownFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="painel">

    <?php require_once 'includes/header-logado.php';?>

    <?php require_once 'includes/sidebar.php';?>

    <main class="principal03">
        <h1>Gerenciamento de Laboratórios</h1>

        <div class="barra-de-acoes">
            <form action="" class="form-pesquisa">
                <label for="pesquisa-lab"><strong>Pesquisar:</strong></label> 
                <input type="text" id="pesquisa-lab" name="pesquisa-lab" placeholder="Digite o nome do laboratório..."> 
                <button type="submit" class="botao-pesquisar"><i class="fas fa-search"></i> Pesquisar</button>
            </form>

            <button onclick="abrirModal()" class="botao-adicionar">
                <i class="fas fa-plus"></i> Adicionar novo Laboratório
            </button>
        </div>
        
        <table class="tabela-lab">
            <thead>
                <tr>
                    <th>Nome do Laboratório</th>
                    <th>Capacidade</th>
                    <th>Equipamento</th>
                    <th class="acoes">Ações</th>
                </tr>
            </thead>
            <tbody id="tabela-corpo">
                <tr>
                    <td colspan="4" style="text-align: center;">
                        <i class="fas fa-spinner loading"></i> Carregando laboratórios...
                    </td>
                </tr>
            </tbody>
        </table>
    </main>

    <?php require_once 'includes/footer.php';?>

    <!-- Modal de Cadastro de Laboratório -->
    <div id="modalCadastroLab" class="modal">
        <div class="modal-conteudo">
            <span class="fechar" onclick="fecharModal()">&times;</span>
            <h2 id="modal-titulo">Cadastrar novo laboratório</h2>
            <form id="formLab">
                <div class="form-grupo">
                    <label for="nome">Nome</label>
                    <input type="text" id="nome" name="nome" placeholder="Digite o nome do laboratório" required>
                </div>
                
                <div class="form-grupo">
                    <label for="capacidade">Capacidade de pessoas:</label>
                    <input type="number" id="capacidade" name="capacidade" min="1" required>
                </div>

                <h3>Equipamentos Disponíveis</h3>
                <div id="equipamentos-container">
                    <!-- Entradas dinâmicas de equipamentos -->
                </div>
        
                <button type="button" onclick="adicionarEquipamento()" class="botao-add-equipamento">
                    <i class="fas fa-plus" style="color: limegreen;"></i> Adicionar novo equipamento
                </button>
        
                <div class="botoes-modal">
                    <button type="button" onclick="fecharModal()" class="botao-cancelar">Cancelar</button>
                    <button type="submit" class="botao-salvar">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variáveis globais
        let equipamentoIndex = 0;
        let laboratorios = [];
        
        // Funções do Modal
        function abrirModal(labId = null) {
            const modal = document.getElementById("modalCadastroLab");
            const form = document.getElementById("formLab");
            const tituloModal = document.getElementById("modal-titulo");
            
            // Resetar formulário
            form.reset();
            document.getElementById("equipamentos-container").innerHTML = "";
            equipamentoIndex = 0;
            
            // Configurar título
            tituloModal.textContent = labId ? "Editar Laboratório" : "Cadastrar novo laboratório";
            
            // Se for edição, carregar dados do laboratório
            if (labId) {
                carregarLaboratorioParaEdicao(labId);
            }
            
            modal.classList.add("ativo"); 
        }
        
        async function carregarLaboratorioParaEdicao(labId) {
            try {
                const response = await fetch(`php_action/laboratorio/read-single.php?id=${labId}`);
                
                if (!response.ok) {
                    throw new Error('Erro ao carregar laboratório');
                }
                
                const laboratorio = await response.json();
                
                document.getElementById("nome").value = laboratorio.nome;
                document.getElementById("capacidade").value = laboratorio.capacidade;
                
                // Adicionar equipamentos
                if (laboratorio.equipamentos && laboratorio.equipamentos.length > 0) {
                    laboratorio.equipamentos.forEach(equip => {
                        adicionarEquipamento(equip.nome, equip.quantidade);
                    });
                } else {
                    adicionarEquipamento();
                }
                
                document.getElementById("formLab").dataset.id = labId;
            } catch (error) {
                console.error("Erro ao carregar laboratório:", error);
                alert("Erro ao carregar dados do laboratório");
            }
        }
        
        function fecharModal() {
            const modal = document.getElementById("modalCadastroLab");
            modal.classList.remove("ativo");
        }
        
        function adicionarEquipamento(nome = "", quantidade = 1) {
            const container = document.getElementById("equipamentos-container");
            const div = document.createElement("div");
            div.classList.add("linha-equipamento");
            div.innerHTML = `
                <input type="text" name="equipamentos[${equipamentoIndex}][nome]" 
                       placeholder="Equipamento" value="${nome}" required>
                <label>Qtd:</label>
                <input type="number" name="equipamentos[${equipamentoIndex}][quantidade]" 
                       min="1" value="${quantidade}" required>
                <button type="button" class="remover-equipamento" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            container.appendChild(div);
            equipamentoIndex++;
        }
        
        // Função para carregar laboratórios com tratamento de erro aprimorado
        async function carregarLaboratorios() {
            const tbody = document.getElementById("tabela-corpo");
            tbody.innerHTML = `<tr><td colspan="4" style="text-align: center;"><i class="fas fa-spinner loading"></i> Carregando...</td></tr>`;
            
            try {
                const response = await fetch('php_action/laboratorio/read.php');
                
                // Verificar se a resposta está vazia
                if (response.status === 204) {
                    laboratorios = [];
                    renderizarLaboratorios();
                    return;
                }
                
                // Tentar parsear como JSON
                const result = await response.json();
                
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'Erro no servidor');
                }
                
                laboratorios = result.data || [];
                renderizarLaboratorios();
                
            } catch (error) {
                console.error("Erro ao carregar laboratórios:", error);
                
                // Verificar se é erro de rede ou de servidor
                const erroMsg = error.message.includes('Failed to fetch') 
                    ? 'Erro de conexão com o servidor' 
                    : error.message;
                
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; color: #d32f2f; padding: 20px;">
                            <div style="margin-bottom: 10px;">
                                <i class="fas fa-exclamation-triangle" style="font-size: 24px;"></i>
                            </div>
                            <strong>Erro ao carregar dados</strong><br>
                            ${erroMsg || 'Tente recarregar a página'}
                            <div style="margin-top: 15px;">
                                <button onclick="carregarLaboratorios()" class="botao-pesquisar">
                                    <i class="fas fa-sync-alt"></i> Tentar novamente
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }

        function renderizarLaboratorios(lista = laboratorios) {
            const tbody = document.getElementById("tabela-corpo");

            if (lista.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">
                            <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 10px;"></i><br>
                            Nenhum laboratório encontrado
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = lista.map(lab => `
                <tr>
                    <td>${lab.nome}</td>
                    <td>${lab.capacidade} alunos</td>
                    <td>${lab.equipamentos || 'Nenhum equipamento'}</td>
                    <td class="coluna-acoes">
                        <button class="botao-acao botao-editar" title="Editar" onclick="abrirModal(${lab.id})">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button class="botao-acao botao-excluir" title="Excluir" onclick="excluirLaboratorio(${lab.id})">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        
        // Função para excluir laboratório
        async function excluirLaboratorio(id) {
            if (confirm("Tem certeza que deseja excluir este laboratório?")) {
                try {
                    const response = await fetch(`php_action/laboratorio/delete.php`, {
                        method: 'POST',
                        body: JSON.stringify({ id: id }),
                        headers: { 
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.error) {
                        throw new Error(result.message);
                    }
                    
                    // Atualizar lista local
                    carregarLaboratorios(); // Atualiza a tabela com os dados mais recentes
                    fecharModal();
                    alert(result.message);
                } catch (error) {
                    console.error("Erro ao excluir laboratório:", error);
                    alert("Erro ao excluir laboratório: " + error.message);
                }
            }
        }
        
        // Manipulador do formulário
        document.getElementById("formLab").addEventListener("submit", async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const equipamentos = [];
            
            // Coletar equipamentos
            document.querySelectorAll(".linha-equipamento").forEach(row => {
                const nome = row.querySelector('input[type="text"]').value;
                const quantidade = row.querySelector('input[type="number"]').value;
                if (nome && quantidade) {
                    equipamentos.push({ 
                        nome: nome.trim(), 
                        quantidade: parseInt(quantidade) 
                    });
                }
            });
            
            const laboratorio = {
                nome: formData.get("nome").trim(),
                capacidade: parseInt(formData.get("capacidade")),
                equipamentos
            };
            
            try {
                // Verificar se é edição ou criação
                const isEdicao = this.dataset.id;
                const url = isEdicao 
                    ? 'php_action/laboratorio/update.php' 
                    : 'php_action/laboratorio/create.php';
                
                if (isEdicao) {
                    laboratorio.id = parseInt(this.dataset.id);
                }
                
                const response = await fetch(url, {
                    method: 'POST',
                    body: JSON.stringify(laboratorio),
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.message);
                }
                
                if (!isEdicao && result.id) {
                    // Após o cadastro, recarrega a lista do backend
                    await carregarLaboratorios();
                } else if (isEdicao) {
                    await carregarLaboratorios();
                }

                fecharModal();
                alert(result.message);

            } catch (error) {
                console.error("Erro ao salvar laboratório:", error);
                alert("Erro ao salvar laboratório: " + error.message);
            }
        });
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById("modalCadastroLab");
            if (event.target === modal) {
                fecharModal();
            }
        }
        
        // Carregar dados quando a página carregar
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelector(".form-pesquisa").addEventListener("submit", function (e) {
                e.preventDefault(); // impede recarregar a página

                const termo = document.getElementById("pesquisa-lab").value.toLowerCase();

                const filtrados = laboratorios.filter(lab =>
                    lab.nome.toLowerCase().includes(termo)
                );

                renderizarLaboratorios(filtrados);
            });

            // Carregar informações do usuário
            fetch('php_action/session-info.php')
                .then(response => response.json())
                .then(data => {
                    if (data.erro) {
                        alert("Sessão expirada. Faça login novamente.");
                        window.location.href = 'login.php';
                    } else {
                        document.getElementById('nome-usuario').textContent = data.nome_completo;
                        document.getElementById('matricula-usuario').textContent = 'Matrícula: ' + data.matricula;
                    }
                })
                .catch(error => {
                    console.error("Erro ao buscar sessão:", error);
                    alert("Erro na autenticação.");
                    window.location.href = 'login.php';
                });
            
            // Carregar laboratórios
            carregarLaboratorios();
            
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