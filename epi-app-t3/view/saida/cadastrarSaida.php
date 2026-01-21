<?php
$pageTitle = 'Autorizar Saída - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Autorizar Saída</h1>
            <a href="/code/epi-app-t3/" class="btn btn-secondary">Início</a>
        </div>
        
        <form action="/code/epi-app-t3/saida/cadastro" method="POST">
            <div class="form-group">
                <label for="idPedido" class="form-label">Selecionar Pedido:</label>
                <select id="idPedido" name="idPedido" class="form-select" required>
                    <option value="">Selecione um pedido</option>
                    <?php if (isset($_SESSION['listaPedidoPendente']) && !empty($_SESSION['listaPedidoPendente'])): ?>
                        <?php foreach ($_SESSION['listaPedidoPendente'] as $pedido): ?>
                            <?php
                                $produtosTexto = '';
                                if ($pedido->getItens()) {
                                    $produtosLista = [];
                                    foreach ($pedido->getItens() as $item) {
                                        $produtosLista[] = htmlspecialchars($item->getProduto()->getNome()) . ' (' . $item->getQuantidade() . ')';
                                    }
                                    $produtosTexto = ' - Produtos: ' . implode(', ', $produtosLista);
                                }
                            ?>
                            <option value="<?php echo $pedido->getId(); ?>">
                                Solicitante: <?php echo $pedido->getSolicitanteId();?> | Pedido #<?php echo $pedido->getId(); ?> 
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">Nenhum pedido pendente</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="dataHora" class="form-label">Data e Hora da Saída:</label>
                <input type="datetime-local" id="dataHora" name="dataHora" value="<?php echo date('Y-m-d\TH:i'); ?>" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="observacao" class="form-label">Observação da Saída:</label>
                <textarea id="observacao" name="observacao" class="form-textarea"></textarea>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">✅ Autorizar Saída</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>