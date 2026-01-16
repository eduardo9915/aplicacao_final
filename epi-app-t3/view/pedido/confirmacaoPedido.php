<?php
$pageTitle = 'Pedido Confirmado - Sistema EPI/EPC';
$pedido = $_SESSION['pedidoConfirmado'] ?? null;
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">✅ Pedido Confirmado</h1>
        </div>
        
        <div class="alert alert-success">
            <span>📋</span>
            <span>Seu pedido foi registrado com sucesso!</span>
        </div>

        <?php if ($pedido): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dados do Pedido</h3>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Número do Pedido:</label>
                    <p class="form-value"><strong>#<?php echo htmlspecialchars($pedido->getId()); ?></strong></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Data e Hora:</label>
                    <p class="form-value"><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($pedido->getDataHora()))); ?></p>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo:</label>
                    <p class="form-value">
                        <?php 
                        if ($pedido->getTipo() === 'EPI') {
                            echo '<span class="badge badge-info">EPI</span>';
                        } elseif ($pedido->getTipo() === 'EPC') {
                            echo '<span class="badge badge-warning">EPC</span>';
                        } else {
                            echo '<span class="badge badge-secondary">' . htmlspecialchars($pedido->getTipo()) . '</span>';
                        }
                        ?>
                    </p>
                </div>

                <div class="form-group">
                    <label class="form-label">Status:</label>
                    <p class="form-value">
                        <?php 
                        if ($pedido->getStatus() === 'PENDENTE') {
                            echo '<span class="badge badge-warning">PENDENTE</span>';
                        } elseif ($pedido->getStatus() === 'APROVADO') {
                            echo '<span class="badge badge-success">APROVADO</span>';
                        } elseif ($pedido->getStatus() === 'REJEITADO') {
                            echo '<span class="badge badge-danger">REJEITADO</span>';
                        } else {
                            echo '<span class="badge badge-secondary">' . htmlspecialchars($pedido->getStatus()) . '</span>';
                        }
                        ?>
                    </p>
                </div>

                <?php if ($pedido->getObservacao()): ?>
                <div class="form-group">
                    <label class="form-label">Observação:</label>
                    <p class="form-value"><?php echo htmlspecialchars($pedido->getObservacao()); ?></p>
                </div>
                <?php endif; ?>

                <?php if ($pedido->getItens() && !empty($pedido->getItens())): ?>
                <div class="form-group">
                    <label class="form-label">Produtos Solicitados:</label>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Quantidade</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedido->getItens() as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item->getProduto()->getNome()); ?></td>
                                        <td><?php echo htmlspecialchars($item->getQuantidade()); ?></td>
                                        <td><?php echo htmlspecialchars($item->getObservacao() ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="alert alert-info">
                <span>ℹ️</span>
                <span>Seu pedido será analisado pela equipe responsável. Você receberá uma atualização sobre o status do pedido em breve.</span>
            </div>

            <div class="btn-group">
                <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
                <a href="/code/epi-app-t3/pedido/cadastro" class="btn btn-primary">📝 Novo Pedido</a>
            </div>
        <?php else: ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <span>Não foi possível exibir os detalhes do pedido.</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.form-value {
    padding: 0.5rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    margin: 0;
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
