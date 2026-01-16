<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistema EPI/EPC'; ?></title>
    <link rel="stylesheet" href="/code/epi-app-t3/view/assets/css/style.css">
</head>
<body>
    <?php if (isset($_SESSION['usuarioLogado'])): ?>
    <header class="header">
        <div class="header-content">
            <a href="/code/epi-app-t3/" class="logo">
                <div class="logo-placeholder">
                    <img src="" alt="">
                    <div class="logo-text">SafeStock EPI/EPC</div>
                </div>
            </a>
            <nav class="nav">
                <a href="/code/epi-app-t3/" class="nav-link">Início</a>
                <a href="/code/epi-app-t3/pedido/cadastro" class="nav-link">Novo Pedido</a>
                <a href="/code/epi-app-t3/pedido/lista" class="nav-link">Pedidos</a>
                <a href="/code/epi-app-t3/produto/lista" class="nav-link">Produtos</a>
                <a href="/code/epi-app-t3/estoque/lista" class="nav-link">Estoque</a>
                <a href="/code/epi-app-t3/entrada/lista" class="nav-link">Entradas</a>
                <a href="/code/epi-app-t3/saida/lista" class="nav-link">Saídas</a>
            </nav>
            <div class="user-info">
                <?php $u = $_SESSION['usuarioLogado']; ?>
                <span class="user-name">👤 <?php echo htmlspecialchars($u->getNome() . ' ' . $u->getSobrenome()); ?></span>
                <a href="/code/epi-app-t3/logout" class="btn-logout">Sair</a>
            </div>
        </div>
    </header>
    <?php else: ?>
    <!-- Header simplificado para usuários não logados, mas oculto na página de novo pedido -->
    <?php 
    $currentUrl = $_SERVER['REQUEST_URI'] ?? '';
    if (!str_contains($currentUrl, '/pedido/cadastro')): 
    ?>
    <header class="header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <div class="header-content" style="justify-content: center;">
            <div style="text-align: center;">
                <h1 style="color: white; margin: 0; font-size: 1.8rem;">SafeStock EPI/EPC</h1>
                <?php if (isset($pageTitle)): ?>
                    <p style="color: rgba(255,255,255,0.9); margin: 0.5rem 0 0 0; font-size: 1rem;"><?php echo htmlspecialchars($pageTitle); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <?php endif; ?>
    <?php endif; ?>
