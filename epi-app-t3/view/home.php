<?php
$pageTitle = 'Início - Sistema EPI/EPC';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Bem-vindo ao Sistema de Gerenciamento e Solicitações de EPI/EPC(s)</h1>
        </div>
        
        <div class="dashboard-grid">
            <a href="/code/epi-app-t3/pedido/cadastro" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #dbeafe; color: #1e40af;">
                    📝
                </div>
                <div class="dashboard-card-title">Novo Pedido</div>
                <div class="dashboard-card-value">Criar</div>
            </a>

            <a href="/code/epi-app-t3/pedido/lista" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #fef3c7; color: #92400e;">
                    📋
                </div>
                <div class="dashboard-card-title">Pedidos</div>
                <div class="dashboard-card-value">Listar</div>
            </a>

            <a href="/code/epi-app-t3/produto/lista" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #d1fae5; color: #065f46;">
                    🎁
                </div>
                <div class="dashboard-card-title">Produtos</div>
                <div class="dashboard-card-value">Gerenciar</div>
            </a>

            <a href="/code/epi-app-t3/estoque/lista" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #e0e7ff; color: #3730a3;">
                    📦
                </div>
                <div class="dashboard-card-title">Estoque</div>
                <div class="dashboard-card-value">Consultar</div>
            </a>

            <a href="/code/epi-app-t3/entrada/lista" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #d1fae5; color: #065f46;">
                    ⬇️
                </div>
                <div class="dashboard-card-title">Entradas</div>
                <div class="dashboard-card-value">Histórico</div>
            </a>

            <a href="/code/epi-app-t3/saida/lista" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #fee2e2; color: #991b1b;">
                    ⬆️
                </div>
                <div class="dashboard-card-title">Saídas</div>
                <div class="dashboard-card-value">Histórico</div>
            </a>
        </div>

        <div class="quick-actions">
            <a href="/code/epi-app-t3/usuario/lista" class="quick-action">
                <span style="font-size: 1.5rem;">👥</span>
                <span>Usuários</span>
            </a>
            <a href="/code/epi-app-t3/setor/lista" class="quick-action">
                <span style="font-size: 1.5rem;">🏢</span>
                <span>Setores</span>
            </a>
            <a href="/code/epi-app-t3/produto/cadastro" class="quick-action">
                <span style="font-size: 1.5rem;">➕</span>
                <span>Cadastrar Produto</span>
            </a>
            <a href="/code/epi-app-t3/entrada/cadastro" class="quick-action">
                <span style="font-size: 1.5rem;">📥</span>
                <span>Nova Entrada</span>
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
