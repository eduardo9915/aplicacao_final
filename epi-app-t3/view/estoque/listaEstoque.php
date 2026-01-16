<?php
$pageTitle = 'Lista de Estoque - Sistema EPI/EPC';
$estoques = [];
if (isset($_SESSION['listaEstoque'])) {
    $estoques = $_SESSION['listaEstoque'];
}
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lista de Estoque</h1>
            <a href="/code/epi-app-t3/estoque/cadastro" class="btn btn-primary">Novo Registro</a>
        </div>

        <?php if (empty($estoques)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h2 class="empty-state-text">Nenhum registro em estoque</h2>
                <p>Clique no botão acima para adicionar um novo registro.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Quantidade</th>
                            <th>Data Entrada</th>
                            <th>Data Saída</th>
                            <th>Excluir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estoques as $estoque): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($estoque->getId()); ?></td>
                                <td><?php echo htmlspecialchars($estoque->getProdutoId()->getNome()); ?></td>
                                <td>
                                    <?php 
                                    $qty = $estoque->getQuantidade();
                                    if ($qty <= 5) {
                                        echo '<span class="badge badge-danger">' . htmlspecialchars($qty) . '</span>';
                                    } elseif ($qty <= 10) {
                                        echo '<span class="badge badge-warning">' . htmlspecialchars($qty) . '</span>';
                                    } else {
                                        echo '<span class="badge badge-success">' . htmlspecialchars($qty) . '</span>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($estoque->getDataEntrada()); ?></td>
                                <td><?php echo htmlspecialchars($estoque->getDataSaida() ?? '-'); ?></td>
                                <td>
                                    <div class="btn-group">
                                        
                                        <a href="/code/epi-app-t3/estoque/exclui?idEstoque=<?php echo $estoque->getId(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este registro?')">Excluir</a>
                                    </div>
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