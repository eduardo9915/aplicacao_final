<?php
$pageTitle = 'Lista de Produtos - Sistema EPI/EPC';
$produtos = [];
if (isset($_SESSION['listaProduto'])) {    
    $produtos = $_SESSION['listaProduto'];
}

function getProdutoTipoBadge($tipo) {
    if ($tipo === 'EPI') {
        return '<span class="badge badge-info">EPI</span>';
    } elseif ($tipo === 'EPC') {
        return '<span class="badge badge-warning">EPC</span>';
    } else {
        return '<span class="badge badge-secondary">' . htmlspecialchars($tipo) . '</span>';
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lista de Produtos</h1>
            <a href="/code/epi-app-t3/produto/cadastro" class="btn btn-primary">Novo Produto</a>
        </div>

        <?php if (empty($produtos)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <h2 class="empty-state-text">Nenhum produto cadastrado</h2>
                <p>Clique no botão acima para cadastrar seu primeiro produto.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Marca</th>
                            <th>Validade</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($produto->getId()); ?></td>
                                <td><?php echo htmlspecialchars($produto->getNome()); ?></td>
                                <td><?php echo getProdutoTipoBadge($produto->getTipo()); ?></td>
                                <td><?php echo htmlspecialchars($produto->getMarca()); ?></td>
                                <td><?php echo htmlspecialchars($produto->getValidade() ?? ''); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/code/epi-app-t3/produto/edita?idProduto=<?php echo $produto->getId(); ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <a href="/code/epi-app-t3/produto/exclui?idProduto=<?php echo $produto->getId(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
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