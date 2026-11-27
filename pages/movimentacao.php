<?php
$page = $_GET['page'] ?? 'estacionados';
$allowedPages = ['estacionados', 'historico'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'estacionados';
}

$pageTitles = [
    'estacionados' => 'Veículos estacionados',
    'historico' => 'Histórico de movimentações'
];
$pageTitle = $pageTitles[$page];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | ParkControl</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/movimentacao.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="index.php" aria-label="ParkControl - início">
            <span class="brand-mark">P</span>
            <span class="brand-name">Park<span>Control</span></span>
        </a>

        <div class="sidebar-label">MENU PRINCIPAL</div>
        <nav class="main-nav" aria-label="Menu principal">
            <a class="nav-link <?= $page === 'estacionados' ? 'active' : '' ?>" href="index.php?page=estacionados">
                <span class="nav-icon">▦</span>
                <span>Veículos estacionados</span>
            </a>
            <a class="nav-link <?= $page === 'historico' ? 'active' : '' ?>" href="index.php?page=historico">
                <span class="nav-icon">◷</span>
                <span>Histórico</span>
            </a>
            <a class="nav-link <?= $page === 'historico' ? 'active' : '' ?>" href="../pages/saida.php">
                <span class="nav-icon">◷</span>
                <span>Saida</span>
            </a>
             <a class="nav-link <?= $page === 'historico' ? 'active' : '' ?>" href="../index.php">
                <span class="nav-icon">◷</span>
                <span>Cadastrar</span>
            </a>
        </nav>

        <div class="sidebar-bottom">
            <div class="sidebar-help">
                <div class="help-icon">?</div>
                <strong>Precisa de ajuda?</strong>
                <p>Consulte a equipe responsável pelo sistema.</p>
            </div>
            <div class="sidebar-version">ParkControl <span>•</span> Projeto Integrador</div>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="mobile-menu" id="mobileMenu" type="button" aria-label="Abrir menu">☰</button>
            <div class="breadcrumb">ParkControl <span>/</span> <?= htmlspecialchars($pageTitle) ?></div>
            <div class="topbar-right">
                <span class="system-indicator"><i></i> Sistema de controle</span>
                <div class="avatar" title="Módulo de consulta">PC</div>
            </div>
        </header>

        <section class="page-content">
        <?php if ($page === 'historico'): ?>
            <?php require '../dados/historico.php'; ?>
        <?php else: ?>
            <?php require '../dados/estacionados.php'; ?>
        <?php endif; ?>
        </section>

        <footer class="footer">
            <span>© <?= date('Y') ?> ParkControl</span>
            <span>Projeto Integrador — Programação Web</span>
        </footer>
    </main>
</div>
<script src="../assets/js/script.js"></script>
</body>
</html>
