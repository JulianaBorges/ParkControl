<?php
require_once "../conexao.php";

$veiculos = [];

$sql = "
    SELECT
        v.placa,
        v.modelo,
        v.cor,
        m.data_entrada
    FROM movimentacoes m
    INNER JOIN veiculos v
        ON v.id = m.veiculo_id
    WHERE m.data_saida IS NULL
    ORDER BY m.data_entrada DESC
";

$resultado = $conexao->query($sql);


function calcularTempoEstacionado($dataEntrada)
{
    $entrada = new DateTime($dataEntrada);
    $agora = new DateTime();

    $intervalo = $entrada->diff($agora);

    return sprintf(
        '%02dh %02dmin',
        ($intervalo->days * 24) + $intervalo->h,
        $intervalo->i
    );
}

$veiculos = [];

while ($linha = $resultado->fetch_assoc()) {

    $veiculos[] = [
        'placa'   => $linha['placa'],
        'modelo'  => $linha['modelo'],
        'cor'     => $linha['cor'],
        'entrada' => date('d/m/Y H:i', strtotime($linha['data_entrada'])),
        'vaga'    => '---',
        'tempo_estacionado' =>
            calcularTempoEstacionado($linha['data_entrada'])
     ];
}

$totalEstacionados = count($veiculos);

?>
<div class="page-heading">
    <div>
        <p class="eyebrow">CONSULTAS <span>•</span> VISÃO GERAL</p>
        <h1>Veículos estacionados</h1>
        <p class="page-description">Acompanhe os veículos que estão no estacionamento neste momento.</p>
    </div>
    <div class="heading-date"><span class="date-icon">▣</span> <?= date('d/m/Y') ?></div>
</div>

<div class="stats-grid">
    <article class="stat-card">
        <div class="stat-top"><span class="stat-label">Veículos estacionados</span><span class="stat-icon blue">▦</span></div>
        <div class="stat-value"><?= $totalEstacionados ?></div>
        <div class="stat-foot"><span class="status-dot"></span> Registros em aberto</div>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span class="stat-label">Situação do módulo</span><span class="stat-icon green">✓</span></div>
        <div class="stat-value stat-text">Operacional</div>
        <div class="stat-foot muted">Interface de consulta</div>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span class="stat-label">Pesquisa disponível</span><span class="stat-icon violet">⌕</span></div>
        <div class="stat-value stat-text">Por placa</div>
        <div class="stat-foot muted">Localize um veículo rapidamente</div>
    </article>
</div>

<section class="panel">
    <div class="panel-header">
        <div>
            <h2>Lista de veículos</h2>
            <p>Consulte os dados dos veículos que permanecem estacionados.</p>
        </div>
        <span class="count-pill"><?= $totalEstacionados ?> veículos</span>
    </div>

    <div class="table-toolbar">
        <label class="search-box">
            <span>⌕</span>
            <input type="search" id="parkedSearch" placeholder="Buscar pela placa..." autocomplete="off">
        </label>
        <span class="table-hint">Exibindo veículos com entrada registrada e sem saída</span>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>PLACA</th>
                <th>MODELO</th>
                <th>COR</th>
                <th>DATA/HORA DE ENTRADA</th>               
                <th>VAGA</th>
                 <th>TEMPO ESTACIONADO</th>
                <th>STATUS</th>
            </tr>
            </thead>
            <tbody id="parkedTable">
            <?php foreach ($veiculos as $veiculo): ?>
                <tr data-plate="<?= htmlspecialchars(strtolower($veiculo['placa'])) ?>">
                    <td><span class="plate"><?= htmlspecialchars($veiculo['placa']) ?></span></td>
                    <td class="primary-cell"><?= htmlspecialchars($veiculo['modelo']) ?></td>
                    <td><?= htmlspecialchars($veiculo['cor']) ?></td>
                    <td><?= htmlspecialchars($veiculo['entrada']) ?></td>
                    <td><?= htmlspecialchars($veiculo['vaga']) ?></td>
                    <td><?= htmlspecialchars($veiculo['tempo_estacionado']) ?></td>
                    <td><span class="status-badge parked"><i></i> Estacionado</span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="empty-state" id="parkedEmpty" hidden>Nenhum veículo encontrado para essa placa.</div>
    </div>
    <div class="panel-footer"><span>Dados ilustrativos para desenvolvimento do front-end.</span><span><?= $totalEstacionados ?> registros</span></div>
</section>
