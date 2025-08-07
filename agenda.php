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

    $sql = "SELECT r.*, l.nome as lab_nome, u.nome_completo as usuario_nome 
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

// Event listeners para os botões de reserva
document.querySelectorAll('.botao-reservar').forEach(button => {
    button.addEventListener('click', function() {
        const dia = this.getAttribute('data-dia');
        const hora_inicio = this.getAttribute('data-hora-inicio');
        const hora_fim = this.getAttribute('data-hora-fim');
        abrirModalReserva(dia, hora_inicio, hora_fim);
    });
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