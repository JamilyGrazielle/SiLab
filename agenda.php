<?php
require_once __DIR__ . '/php_action/db_connect.php';

// Configurações padrão
$modo_admin = isset($modo_admin) ? $modo_admin : false;
$modo_professor = isset($modo_professor) ? $modo_professor : false;
$modo_convidado = isset($modo_convidado) ? $modo_convidado : false;

// Filtros
$filtro_lab = isset($_GET['laboratorio']) ? intval($_GET['laboratorio']) : 0;
$filtro_data = isset($_GET['data']) ? $_GET['data'] : date('d-m-Y');
$semana = isset($_GET['semana']) ? $_GET['semana'] : date('WW-\Y');

// Converter a semana em data de início (segunda-feira)
$inicio_semana = strtotime($semana . '1');
if ($inicio_semana === false) {
    $inicio_semana = strtotime('monday this week');
}
$inicio_semana = strtotime('midnight', $inicio_semana);

// Array com os dias da semana (seg a dom)
$dias_semana = [];
for ($i = 0; $i < 7; $i++) {
    $dias_semana[] = strtotime("+$i days", $inicio_semana);
}

// Buscar laboratórios
$laboratorios = [];
try {
    $sql_labs = "SELECT id, nome FROM Laboratorio";
    $stmt = $pdo->prepare($sql_labs);
    $stmt->execute();
    $laboratorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar laboratórios: " . $e->getMessage());
}

// Definir turnos
$turnos = [
    'matutino' => ['inicio' => '08:00:00', 'fim' => '12:00:00'],
    'vespertino' => ['inicio' => '14:00:00', 'fim' => '18:00:00'],
    'noturno' => ['inicio' => '19:00:00', 'fim' => '22:00:00']
];

// Buscar reservas para a semana
$reservas_por_dia = [];
for ($i = 0; $i < 7; $i++) {
    $dia = date('Y-m-d', $dias_semana[$i]);
    $reservas_por_dia[$i] = [];

    $sql = "SELECT r.*, l.nome as lab_nome, u.nome_completo as usuario_nome, r.motivo 
            FROM Reserva r
            JOIN Laboratorio l ON r.laboratorio_id = l.id
            JOIN Usuario u ON r.usuario_id = u.id
            WHERE r.data_reserva = ? 
              AND r.status = 'confirmada'";
    
    $params = [$dia];
    
    if ($filtro_lab > 0) {
        $sql .= " AND r.laboratorio_id = ?";
        $params[] = $filtro_lab;
    }
    
    if ($modo_professor && isset($_SESSION['user_id'])) {
        $sql .= " AND r.usuario_id = ?";
        $params[] = $_SESSION['user_id'];
    }

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $reservas_por_dia[$i] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar reservas: " . $e->getMessage());
    }
}

// Função para verificar se um horário está reservado
function esta_reservado($reservas, $hora_inicio, $hora_fim) {
    foreach ($reservas as $reserva) {
        $reserva_inicio = strtotime($reserva['hora_inicio']);
        $reserva_fim = strtotime($reserva['hora_fim']);
        $hora_check_inicio = strtotime($hora_inicio);
        $hora_check_fim = strtotime($hora_fim);
        
        if (($hora_check_inicio >= $reserva_inicio && $hora_check_inicio < $reserva_fim) ||
            ($hora_check_fim > $reserva_inicio && $hora_check_fim <= $reserva_fim) ||
            ($hora_check_inicio <= $reserva_inicio && $hora_check_fim >= $reserva_fim)) {
            return $reserva;
        }
    }
    return false;
}

// Obter laboratórios com reservas na semana (para a nova seção de botões)
$data_inicio_semana = date('Y-m-d', $inicio_semana);
$data_fim_semana = date('Y-m-d', strtotime('+6 days', $inicio_semana));

$labs_reservados = [];
if ($filtro_lab == 0) {
    try {
        $sql_labs_reservados = "SELECT DISTINCT l.id, l.nome 
                                FROM Reserva r 
                                JOIN Laboratorio l ON r.laboratorio_id = l.id 
                                WHERE r.data_reserva BETWEEN ? AND ?
                                AND r.status = 'confirmada'";
        $stmt = $pdo->prepare($sql_labs_reservados);
        $stmt->execute([$data_inicio_semana, $data_fim_semana]);
        $labs_reservados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Não interrompe a execução, apenas não mostra os botões
        error_log("Erro ao buscar laboratórios reservados: " . $e->getMessage());
    }
}
?>

<!-- Filtros -->
<div class="filtros-agenda">
    <form method="GET" class="form-filtros">
        <div class="filtro-group">
            <label for="laboratorio">Laboratório:</label>
            <select name="laboratorio" id="laboratorio">
                <option value="0">Todos</option>
                <?php foreach ($laboratorios as $lab): ?>
                    <option value="<?= $lab['id'] ?>" <?= $filtro_lab == $lab['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lab['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filtro-group">
            <label for="semana">Semana:</label>
            <input type="week" name="semana" id="semana" value="<?= date('Y-\WW', $inicio_semana) ?>">
        </div>

        <button type="submit" class="botao-filtrar">
            <i class="fas fa-filter"></i> Filtrar
        </button>
        
        <?php if ($modo_admin || $modo_professor): ?>
            <button type="button" class="botao-nova-reserva" onclick="abrirModalReserva()">
                <i class="fas fa-plus"></i> Nova Reserva
            </button>
        <?php endif; ?>
    </form>
</div>

<!-- Lista de laboratórios com reservas (nova seção) -->
<?php if ($filtro_lab == 0 && !empty($labs_reservados)): ?>
    <div class="labs-disponiveis-container">
        <h3>Laboratórios com reservas nesta semana:</h3>
        <div class="lista-labs">
            <?php foreach ($labs_reservados as $lab): ?>
                <a href="?laboratorio=<?= $lab['id'] ?>&semana=<?= urlencode($semana) ?>" class="botao-lab">
                    <?= htmlspecialchars($lab['nome']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Tabela de Agenda -->
<div class="agenda-container">
    <table class="tabela-agenda">
        <thead>
            <tr>
                <th>Horário</th>
                <?php for ($i = 0; $i < 7; $i++): 
                    $dia_class = date('N', $dias_semana[$i]) >= 6 ? 'fim-de-semana' : '';
                    ?>
                    <th class="<?= $dia_class ?>">
                        <?= date('D', $dias_semana[$i]) ?><br>
                        <small><?= date('d/m', $dias_semana[$i]) ?></small>
                    </th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($turnos as $turno => $horario_turno): 
                $hora_atual = $horario_turno['inicio'];
                $intervalo = 60; // 1 hora
                ?>
                
                <tr class="turno-header">
                    <td colspan="8"><?= ucfirst($turno) ?> (<?= substr($horario_turno['inicio'], 0, 5) ?> - <?= substr($horario_turno['fim'], 0, 5) ?>)</td>
                </tr>
                
                <?php while (strtotime($hora_atual) < strtotime($horario_turno['fim'])): 
                    $hora_fim = date('H:i:s', strtotime($hora_atual) + $intervalo * 60);
                    ?>
                    <tr>
                        <td class="hora-coluna">
                            <?= substr($hora_atual, 0, 5) ?> - <?= substr($hora_fim, 0, 5) ?>
                        </td>
                        
                        <?php for ($i = 0; $i < 7; $i++): 
                            $dia = date('Y-m-d', $dias_semana[$i]);
                            $reserva = esta_reservado($reservas_por_dia[$i], $hora_atual, $hora_fim);
                            $dia_class = date('N', $dias_semana[$i]) >= 6 ? 'fim-de-semana' : '';
                            ?>
                            
                            <td class="<?= $dia_class ?> <?= $reserva ? 'reservado' : 'disponivel' ?>"
                                data-dia="<?= $dia ?>"
                                data-hora-inicio="<?= $hora_atual ?>"
                                data-hora-fim="<?= $hora_fim ?>">
                                
                                <?php if ($reserva): ?>
                                    <div class="reserva-info">
                                        <div class="reserva-lab"><?= $reserva['lab_nome'] ?></div>
                                        <div class="reserva-prof">
                                            <?= $modo_convidado ? 'Reservado' : $reserva['usuario_nome'] ?>
                                        </div>
                                        <!-- Botão para ver detalhes -->
                                        <button class="botao-detalhes" 
                                                data-lab="<?= htmlspecialchars($reserva['lab_nome']) ?>" 
                                                data-professor="<?= htmlspecialchars($reserva['usuario_nome']) ?>" 
                                                data-dia="<?= $dia ?>"
                                                data-hora-inicio="<?= substr($hora_atual, 0, 5) ?>"
                                                data-hora-fim="<?= substr($hora_fim, 0, 5) ?>"
                                                data-motivo="<?= htmlspecialchars($reserva['motivo']) ?>">
                                            <i class="fas fa-info-circle"></i> Detalhes
                                        </button>
                                        <?php if ($modo_admin || $modo_professor): ?>
                                            <div class="reserva-acoes">
                                                <button class="botao-acao botao-cancelar" 
                                                        title="Cancelar reserva"
                                                        data-id="<?= $reserva['id'] ?>">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php elseif ($modo_admin || $modo_professor): ?>
                                    <button class="botao-reservar" 
                                            title="Reservar este horário"
                                            data-dia="<?= $dia ?>"
                                            data-hora-inicio="<?= $hora_atual ?>"
                                            data-hora-fim="<?= $hora_fim ?>">
                                        <i class="fas fa-calendar-plus"></i>
                                    </button>
                                <?php else: ?>
                                    <div class="disponivel-text">Disponível</div>
                                <?php endif; ?>
                            </td>
                        <?php endfor; ?>
                    </tr>
                    
                    <?php $hora_atual = $hora_fim; ?>
                <?php endwhile; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal de Detalhes da Reserva com Laboratórios Disponíveis -->
<div id="modalDetalhesReserva" class="modal">
    <div class="modal-conteudo">
        <span class="fechar" onclick="fecharModalDetalhes()">&times;</span>
        <h2>Detalhes da Reserva</h2>
        <div id="detalhes-reserva-conteudo">
            <p><strong>Laboratório:</strong> <span id="detalhes-lab"></span></p>
            <p><strong>Professor:</strong> <span id="detalhes-professor"></span></p>
            <p><strong>Data:</strong> <span id="detalhes-data"></span></p>
            <p><strong>Horário:</strong> <span id="detalhes-horario"></span></p>
            <p><strong>Motivo:</strong> <span id="detalhes-motivo"></span></p>
            
            <div class="labs-disponiveis-section">
                <h3>Laboratórios Disponíveis neste Horário</h3>
                <div id="lista-labs-disponiveis" class="lista-labs-disponiveis">
                    <!-- Conteúdo será preenchido via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Reserva -->
<div id="modalReserva" class="modal">
    <div class="modal-conteudo">
        <span class="fechar" onclick="fecharModalReserva()">&times;</span>
        <h2 id="modal-reserva-titulo">Nova Reserva</h2>
        <form id="formReserva">
            <input type="hidden" id="reserva_id" name="id" value="">
            
            <div class="form-grupo">
                <label for="reserva_laboratorio">Laboratório:</label>
                <select id="reserva_laboratorio" name="laboratorio_id" required>
                    <?php foreach ($laboratorios as $lab): ?>
                        <option value="<?= $lab['id'] ?>"><?= htmlspecialchars($lab['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-grupo">
                <label for="reserva_data">Data:</label>
                <input type="date" id="reserva_data" name="data" required>
            </div>
            
            <div class="form-grupo-duplo">
                <div>
                    <label for="reserva_hora_inicio">Hora Início:</label>
                    <input type="time" id="reserva_hora_inicio" name="hora_inicio" required>
                </div>
                <div>
                    <label for="reserva_hora_fim">Hora Fim:</label>
                    <input type="time" id="reserva_hora_fim" name="hora_fim" required>
                </div>
            </div>
            
            <div class="form-grupo">
                <label for="reserva_motivo">Motivo:</label>
                <textarea id="reserva_motivo" name="motivo" rows="3" required></textarea>
            </div>
            
            <div class="botoes-modal">
                <button type="button" class="botao-cancelar" onclick="fecharModalReserva()">Cancelar</button>
                <button type="submit" class="botao-salvar">Salvar Reserva</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Laboratórios disponíveis */
    .labs-disponiveis-container {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin: 15px 0;
        border: 1px solid #dee2e6;
    }

    .labs-disponiveis-container h3 {
        margin-top: 0;
        margin-bottom: 10px;
        color: #495057;
        font-size: 1.1em;
    }

    .lista-labs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .botao-lab {
        display: inline-block;
        padding: 8px 15px;
        background-color: #e9ecef;
        border: 1px solid #ced4da;
        border-radius: 4px;
        color: #495057;
        text-decoration: none;
        font-size: 0.9em;
        transition: all 0.2s ease;
    }

    .botao-lab:hover {
        background-color: #d1e7ff;
        border-color: #86b7fe;
        transform: translateY(-2px);
    }

    /* Botão de detalhes */
    .botao-detalhes {
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        margin-top: 5px;
        cursor: pointer;
        font-size: 0.8em;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .botao-detalhes:hover {
        background-color: #2980b9;
    }

    /* Modal de detalhes */
    #detalhes-reserva-conteudo p {
        margin: 10px 0;
        line-height: 1.5;
    }

    #detalhes-motivo {
        white-space: pre-line;
        background-color: #f9f9f9;
        padding: 10px;
        border-radius: 4px;
        display: block;
        margin-top: 5px;
        border: 1px solid #eee;
    }
    
    /* Estilos para a seção de laboratórios disponíveis no modal */
    .labs-disponiveis-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 20px;
        border: 1px solid #dee2e6;
    }
    
    .labs-disponiveis-section h3 {
        margin-top: 0;
        color: #495057;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
    }
    
    .lista-labs-disponiveis {
        max-height: 300px;
        overflow-y: auto;
    }
    
    .lab-disponivel-item {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 12px;
        margin-bottom: 10px;
        transition: all 0.2s ease;
    }
    
    .lab-disponivel-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .lab-disponivel-item h4 {
        margin-top: 0;
        margin-bottom: 8px;
        color: #1a73e8;
    }
    
    .lab-info {
        display: flex;
        justify-content: space-between;
    }
    
    .lab-capacidade {
        background-color: #e8f0fe;
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 0.85em;
    }
    
    .lab-equipamentos {
        font-size: 0.9em;
        color: #5f6368;
        margin-top: 8px;
    }
    
    .qtd-equip {
        background-color: #e8f0fe;
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 0.85em;
        margin-left: 3px;
    }
</style>

<script>
// Funções para manipulação da agenda
function abrirModalReserva(dia = '', hora_inicio = '', hora_fim = '') {
    const modal = document.getElementById("modalReserva");
    const form = document.getElementById("formReserva");
    
    // Resetar o formulário
    form.reset();
    document.getElementById("reserva_id").value = "";
    
    // Preencher valores padrão se for uma nova reserva
    if (dia && hora_inicio && hora_fim) {
        document.getElementById("reserva_data").value = dia;
        document.getElementById("reserva_hora_inicio").value = hora_inicio.substring(0, 5);
        document.getElementById("reserva_hora_fim").value = hora_fim.substring(0, 5);
    }
    
    modal.style.display = "block";
}

function fecharModalReserva() {
    document.getElementById("modalReserva").style.display = "none";
}

// Função para buscar laboratórios disponíveis
async function buscarLabsDisponiveis(dia, hora_inicio, hora_fim) {
    try {
        const response = await fetch(`php_action/disponibilidade.php?dia=${dia}&hora_inicio=${hora_inicio}&hora_fim=${hora_fim}`);
        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.message);
        }
        
        return data.disponiveis;
    } catch (error) {
        console.error("Erro ao buscar laboratórios disponíveis:", error);
        return [];
    }
}

// Função para formatar a lista de equipamentos
function formatarEquipamentos(equipamentos) {
    if (!equipamentos) return 'Nenhum equipamento listado';
    
    // Formato atual: "Monitor (10), Teclado (20)"
    const partes = equipamentos.split(', ');
    if (partes.length === 0) return 'Nenhum equipamento listado';
    
    let html = '';
    partes.forEach(eq => {
        const match = eq.match(/(.*) \((\d+)\)/);
        if (match) {
            const nome = match[1];
            const qtd = match[2];
            html += `<div>${nome} <span class="qtd-equip">${qtd}</span></div>`;
        } else {
            html += `<div>${eq}</div>`;
        }
    });
    
    return html;
}

// Função para mostrar laboratórios disponíveis
async function mostrarLabsDisponiveis(detalhes) {
    const container = document.getElementById('lista-labs-disponiveis');
    container.innerHTML = '<p>Carregando laboratórios disponíveis...</p>';
    
    try {
        const disponiveis = await buscarLabsDisponiveis(
            detalhes.dia, 
            detalhes.hora_inicio + ':00', 
            detalhes.hora_fim + ':00'
        );
        
        if (disponiveis.length === 0) {
            container.innerHTML = '<p>Nenhum laboratório disponível neste horário</p>';
            return;
        }
        
        let html = '';
        disponiveis.forEach(lab => {
            html += `
                <div class="lab-disponivel-item">
                    <h4>${lab.nome}</h4>
                    <div class="lab-info">
                        <div class="lab-capacidade">Capacidade: ${lab.capacidade} pessoas</div>
                    </div>
                    <div class="lab-equipamentos">
                        <strong>Equipamentos:</strong> 
                        ${formatarEquipamentos(lab.equipamentos)}
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    } catch (error) {
        container.innerHTML = `<p class="error">Erro ao carregar laboratórios: ${error.message}</p>`;
    }
}

// Funções para o modal de detalhes
function abrirModalDetalhes(detalhes) {
    const modal = document.getElementById("modalDetalhesReserva");
    
    // Preencher detalhes da reserva
    document.getElementById("detalhes-lab").textContent = detalhes.lab;
    document.getElementById("detalhes-professor").textContent = detalhes.professor;
    document.getElementById("detalhes-data").textContent = detalhes.dia;
    document.getElementById("detalhes-horario").textContent = `${detalhes.hora_inicio} - ${detalhes.hora_fim}`;
    document.getElementById("detalhes-motivo").textContent = detalhes.motivo;
    
    // Buscar e mostrar laboratórios disponíveis
    mostrarLabsDisponiveis(detalhes);
    
    modal.style.display = "block";
}

function fecharModalDetalhes() {
    document.getElementById("modalDetalhesReserva").style.display = "none";
}

// Event listeners para os botões de reserva
document.querySelectorAll('.botao-reservar').forEach(button => {
    button.addEventListener('click', function() {
        const dia = this.getAttribute('data-dia');
        const hora_inicio = this.getAttribute('data-hora-inicio');
        const hora_fim = this.getAttribute('data-hora-fim');
        abrirModalReserva(dia, hora_inicio, hora_fim);
    });
});

// Event listeners para os botões de detalhes
document.querySelectorAll('.botao-detalhes').forEach(button => {
    button.addEventListener('click', function() {
        const detalhes = {
            lab: this.getAttribute('data-lab'),
            professor: this.getAttribute('data-professor'),
            dia: this.getAttribute('data-dia'),
            hora_inicio: this.getAttribute('data-hora-inicio'),
            hora_fim: this.getAttribute('data-hora-fim'),
            motivo: this.getAttribute('data-motivo')
        };
        abrirModalDetalhes(detalhes);
    });
});

// Fechar modais ao clicar fora do conteúdo
window.addEventListener('click', function(event) {
    const modalReserva = document.getElementById("modalReserva");
    const modalDetalhes = document.getElementById("modalDetalhesReserva");
    
    if (event.target === modalReserva) {
        fecharModalReserva();
    }
    
    if (event.target === modalDetalhes) {
        fecharModalDetalhes();
    }
});

// Event listener para o formulário de reserva
document.getElementById("formReserva").addEventListener("submit", async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const reserva = Object.fromEntries(formData.entries());
    
    try {
        const response = await fetch('php_action/reserva/create.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(reserva)
        });
        
        const result = await response.json();
        
        if (result.error) {
            throw new Error(result.message);
        }
        
        alert(result.message);
        fecharModalReserva();
        // Recarregar a agenda
        window.location.reload();
    } catch (error) {
        console.error("Erro ao criar reserva:", error);
        alert("Erro ao criar reserva: " + error.message);
    }
});

// Event listeners para cancelar reservas
document.querySelectorAll('.botao-cancelar').forEach(button => {
    button.addEventListener('click', async function() {
        const reservaId = this.getAttribute('data-id');
        
        if (confirm("Tem certeza que deseja cancelar esta reserva?")) {
            try {
                const response = await fetch('php_action/reserva/delete.php', {
                    method: 'POST',
                    body: JSON.stringify({ id: reservaId }),
                    headers: { 
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.error) {
                    throw new Error(result.message);
                }
                
                alert(result.message);
                // Recarregar a agenda
                window.location.reload();
            } catch (error) {
                console.error("Erro ao cancelar reserva:", error);
                alert("Erro ao cancelar reserva: " + error.message);
            }
        }
    });
});
</script>