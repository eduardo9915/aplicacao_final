<?php
$pageTitle = 'Lista de Saídas - Sistema EPI/EPC';
$saidas = [];
if (isset($_SESSION['listaSaida'])) {
    $saidas = $_SESSION['listaSaida'];
}
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lista de Saídas</h1>
            
        </div>

        <?php if (empty($saidas)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">⬆️</div>
                <h2 class="empty-state-text">Nenhuma saída cadastrada</h2>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data e Hora da Saída</th>
                            <th>Tipo da Saída</th>
                            <th>Observação da Saída</th>
                            <th>Usuário do Pedido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($saidas as $saida): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($saida->getId()); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($saida->getDataHora())); ?></td>
                                <td><?php echo htmlspecialchars($saida->getTipo()); ?></td>
                                <td><?php echo htmlspecialchars($saida->getObservacao() ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $pedido = $saida->getPedidoId();
                                    if ($pedido && $pedido->getUsuario()) {
                                        $usuario = $pedido->getUsuario();
                                        echo htmlspecialchars($usuario->getNome() . ' ' . $usuario->getSobrenome());
                                    } else {
                                        echo '-';
                                    }
                                    ?>
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