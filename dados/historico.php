<?php

require_once "../conexao.php";

function formatarTempo($minutos)
{
    $horas = floor($minutos / 60);
    $minutosRestantes = $minutos % 60;

    return sprintf('%02dh %02dmin', $horas, $minutosRestantes);
}

$movimentacoes = [];

$sql = "
    SELECT
        v.placa,
        v.modelo,
        m.data_entrada,
        m.data_saida,
        m.status,
        m.tempo_permanencia_minutos
    FROM movimentacoes m
    INNER JOIN veiculos v
        ON v.id = m.veiculo_id
    ORDER BY m.data_entrada DESC
";

$resultado = $conexao->query($sql);


while ($linha = $resultado->fetch_assoc()) {

    if ($linha['status'] == 'FINALIZADO') {

        $tempo = formatarTempo(
            $linha['tempo_permanencia_minutos']
        );

    } else {

        $entrada = new DateTime($linha['data_entrada']);
        $agora = new DateTime();

        $intervalo = $entrada->diff($agora);

        $minutos =
            ($intervalo->days * 24 * 60) +
            ($intervalo->h * 60) +
            $intervalo->i;

        $tempo = formatarTempo($minutos);
    }

    $movimentacoes[] = [
        'placa' => $linha['placa'],
        'modelo' => $linha['modelo'],
        'entrada' => date(
            'd/m/Y H:i',
            strtotime($linha['data_entrada'])
        ),
        'saida' => $linha['data_saida']
            ? date(
                'd/m/Y H:i',
                strtotime($linha['data_saida'])
            )
            : '—',
        'status' => $linha['status'],
        'tempo' => $tempo
    ];
}

$totalMovimentacoes = count($movimentacoes);

?>
<div class="page-heading">
    <div>
        <p class="eyebrow">CONSULTAS <span>•</span> REGISTROS</p>
        <h1>Histórico de movimentações</h1>
        <p class="page-description">Consulte as entradas e saídas registradas no estacionamento.</p>
    </div>
    <div class="heading-date"><span class="date-icon">◷</span> Histórico completo</div>
</div>

<div class="stats-grid stats-grid-two">
    <article class="stat-card">
        <div class="stat-top"><span class="stat-label">Movimentações exibidas</span><span class="stat-icon blue">↕</span></div>
        <div class="stat-value"><?= $totalMovimentacoes ?></div>
        <div class="stat-foot muted">Registros de exemplo</div>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span class="stat-label">Critério de consulta</span><span class="stat-icon violet">⌕</span></div>
        <div class="stat-value stat-text">Placa do veículo</div>
        <div class="stat-foot muted">Pesquise para filtrar o histórico</div>
    </article>
</div>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Movimentações registradas</h2>
            <p>As movimentações mais recentes aparecem primeiro quando conectadas ao banco.</p>
        </div>
        <span class="count-pill"><?= $totalMovimentacoes ?> registros</span>
    </div>

    <div class="table-toolbar">
        <label class="search-box">
            <span>⌕</span>
            <input type="search" id="historySearch" placeholder="Buscar pela placa..." autocomplete="off">
        </label>
        <span class="table-hint">Pesquise pela placa completa ou por parte dela</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>PLACA</th>
                <th>MODELO</th>
                <th>DATA/HORA DE ENTRADA</th>
                <th>DATA/HORA DE SAÍDA</th>
                <th>STATUS</th>
                <th>TEMPO</th>


            </tr>
            </thead>
            <tbody id="historyTable">
            <?php foreach ($movimentacoes as $movimento): ?>
                <tr data-plate="<?= htmlspecialchars(strtolower($movimento['placa'])) ?>">
                    <td><span class="plate"><?= htmlspecialchars($movimento['placa']) ?></span></td>
                    <td class="primary-cell"><?= htmlspecialchars($movimento['modelo']) ?></td>
                    <td><?= htmlspecialchars($movimento['entrada']) ?></td>
                    <td><?= htmlspecialchars($movimento['saida']) ?></td>
                    <td>
                        <?php if ($movimento['status'] === 'ESTACIONADO'): ?>
                            <span class="status-badge parked"><i></i> Estacionado</span>
                        <?php else: ?>
                            <span class="status-badge finished"><i></i> Finalizado</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($movimento['tempo']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="empty-state" id="historyEmpty" hidden>Nenhuma movimentação encontrada para essa placa.</div>
    </div>
    <div class="panel-footer"><span>Dados ilustrativos para desenvolvimento do front-end.</span><span><?= $totalMovimentacoes ?> registros</span></div>
</section>
