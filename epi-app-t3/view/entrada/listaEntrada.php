<?php
$pageTitle = 'Lista de Entradas - Sistema EPI/EPC';
$entradas = [];
if (isset($_SESSION['listaEntrada'])) {
    $entradas = $_SESSION['listaEntrada'];
}
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lista de Entradas</h1>
            <a href="/code/epi-app-t3/entrada/cadastro" class="btn btn-primary">Nova Entrada</a>
        </div>

        <?php if (empty($entradas)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">⬇️</div>
                <h2 class="empty-state-text">Nenhuma entrada cadastrada</h2>
                <p>Clique no botão acima para registrar sua primeira entrada.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data Hora</th>
                            <th>Tipo</th>
                            <th>Observação</th>                    
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entradas as $entrada): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($entrada->getId()); ?></td>
                                <td><?php echo htmlspecialchars($entrada->getDataHora()); ?></td>
                                <td><?php echo htmlspecialchars($entrada->getTipo()); ?></td>
                                <td><?php echo htmlspecialchars($entrada->getObservacao()); ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" style="background-color: #f9fafb; padding: 1rem;">
                                    <?php $itens = $entrada->getItens(); ?>
                                    <?php if (!empty($itens)): ?>
                                        <strong>Produtos da Entrada:</strong>
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
                                                            <td style="padding: 0.5rem;"><?php echo htmlspecialchars($item->getObservacao()); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <em style="color: var(--text-secondary);">Sem produtos associados</em>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>