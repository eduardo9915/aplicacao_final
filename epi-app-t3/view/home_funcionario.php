<?php
$pageTitle = 'Meu Painel - Sistema EPI/EPC';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">👷 Bem-vindo, <?php echo htmlspecialchars($u->getNome()); ?>!</h1>
            <p class="card-subtitle">Painel do Funcionário - Sistema de Solicitações de EPI/EPC</p>
        </div>
        
        <div class="dashboard-grid">
            <a href="/code/epi-app-t3/pedido/cadastro" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #dbeafe; color: #1e40af;">
                    📝
                </div>
                <div class="dashboard-card-title">Novo Pedido</div>
                <div class="dashboard-card-value">Solicitar EPI/EPC</div>
            </a>

            <a href="/code/epi-app-t3/pedido/meusPedidos" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #fef3c7; color: #92400e;">
                    📋
                </div>
                <div class="dashboard-card-title">Meus Pedidos</div>
                <div class="dashboard-card-value">Histórico Completo</div>
            </a>

            <a href="/code/epi-app-t3/funcionario/perfil" class="dashboard-card">
                <div class="dashboard-card-icon" style="background-color: #e0e7ff; color: #3730a3;">
                    👤
                </div>
                <div class="dashboard-card-title">Meu Perfil</div>
                <div class="dashboard-card-value">Dados Pessoais</div>
            </a>
        </div>

        <div class="info-section">
            <div class="info-card">
                <h3>📖 Como solicitar EPI/EPC:</h3>
                <ol>
                    <li>Clique em "Novo Pedido" para abrir uma solicitação</li>
                    <li>Selecione os itens que você precisa</li>
                    <li>Aguarde a aprovação do administrador</li>
                    <li>Acompanhe o status em "Meus Pedidos"</li>
                </ol>
            </div>
            
            <div class="info-card">
                <h3>📞 Precisa de ajuda?</h3>
                <p>Entre em contato com o setor administrativo para dúvidas sobre suas solicitações.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
