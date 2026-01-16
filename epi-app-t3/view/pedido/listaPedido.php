<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pedidos = [];
if (isset($_SESSION['listaPedido'])) {
    $pedidos = $_SESSION['listaPedido'];
}

$pageTitle = 'Lista de Pedidos - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';

function getStatusBadge($status) {
    $statusLower = strtolower($status);
    switch ($statusLower) {
        case 'aprovado':
            return '<span class="badge badge-success">Aprovado</span>';
        case 'pendente':
            return '<span class="badge badge-warning">Pendente</span>';
        case 'rejeitado':
            return '<span class="badge badge-danger">Rejeitado</span>';
        default:
            return '<span class="badge badge-info">' . htmlspecialchars($status) . '</span>';
    }
}

function formatarObservacao($observacao, $status) {
    if (empty($observacao)) {
        return '-';
    }
    
    $observacaoFormatada = htmlspecialchars($observacao);
    
    // Se o pedido estiver rejeitado, destacar o motivo da reprovação
    if ($status === 'REJEITADO') {
        $observacaoFormatada = str_replace(
                            '[MOTIVO DA REPROVAÇÃO]', 
                            '<strong style="color: #dc2626;">[MOTIVO DA REPROVAÇÃO]</strong>', 
                            $observacaoFormatada
                        );
        // Converter quebras de linha para <br>
        $observacaoFormatada = nl2br($observacaoFormatada);
    }
    
    return $observacaoFormatada;
}
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lista de Pedidos</h1>
            <a href="/code/epi-app-t3/pedido/cadastro" class="btn btn-primary">
                ➕ Novo Pedido
            </a>
        </div>

        <?php if (empty($pedidos)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <div class="empty-state-text">Nenhum pedido cadastrado.</div>
                <a href="/code/epi-app-t3/pedido/cadastro" class="btn btn-primary mt-2">
                    Criar Primeiro Pedido
                </a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data/Hora</th>
                            <th>Usuário</th>
                            <th>Status</th>
                            <th>Tipo</th>
                            <th>Observação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pedido->getId()); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($pedido->getDataHora()))); ?></td>
                                <td><?php echo htmlspecialchars($pedido->getUsuario() ? ($pedido->getUsuario()->getNome() . ' ' . $pedido->getUsuario()->getSobrenome()) : '-'); ?></td>
                                <td><?php echo getStatusBadge($pedido->getStatus()); ?></td>
                                <td><?php echo htmlspecialchars($pedido->getTipo()); ?></td>
                                <td><?php echo formatarObservacao($pedido->getObservacao(), $pedido->getStatus()); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <?php if ($pedido->getStatus() === 'PENDENTE'): ?>
                                            <a href="/code/epi-app-t3/saida/autorizar?idPedido=<?php echo $pedido->getId(); ?>" class="btn btn-sm btn-success">
                                                ✅ Autorizar
                                            </a>
                                            <a 
                                                href="/code/epi-app-t3/pedido/rejeitar?idPedido=<?php echo $pedido->getId(); ?>" 
                                                class="btn btn-sm btn-warning"
                                            >
                                                ❌ Rejeitar
                                            </a>
                                        <?php elseif ($pedido->getStatus() === 'REJEITADO'): ?>
                                            <!-- Pedido já rejeitado - não mostrar botões -->
                                        <?php else: ?>
                                            <!-- Pedido já aprovado - não mostrar botão rejeitar -->
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php 
                            $itens = method_exists($pedido, 'getItens') ? $pedido->getItens() : []; 
                            if (!empty($itens)):
                            ?>
                                <tr>
                                    <td colspan="7" style="background-color: #f9fafb; padding: 1rem;">
                                        <strong>Produtos do Pedido:</strong>
                                        <div style="margin-top: 0.5rem;">
                                            <table style="width: 100%; background: white; border-radius: 0.25rem;">
                                                <thead>
                                                    <tr style="background-color: #f3f4f6;">
                                                        <th style="padding: 0.5rem;">Produto</th>
                                                        <th style="padding: 0.5rem;">Quantidade</th>
                                                        <th style="padding: 0.5rem;">Observação</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($itens as $item): ?>
                                                        <tr>
                                                            <td style="padding: 0.5rem;"><?php echo htmlspecialchars($item->getProduto()->getNome()); ?></td>
                                                            <td style="padding: 0.5rem;"><?php echo htmlspecialchars($item->getQuantidade()); ?></td>
                                                            <td style="padding: 0.5rem;"><?php echo htmlspecialchars($item->getObservacao() ?? '-'); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
