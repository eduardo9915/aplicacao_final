<?php
$pageTitle = 'Lista de Setores - Sistema EPI/EPC';
$setores = [];
if (isset($_SESSION['listaSetor'])) {
    $setores = $_SESSION['listaSetor'];
}
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lista de Setores</h1>
            <a href="/code/epi-app-t3/setor/cadastro" class="btn btn-primary">Novo Setor</a>
        </div>

        <?php if (empty($setores)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🏢</div>
                <h2 class="empty-state-text">Nenhum setor cadastrado</h2>
                <p>Clique no botão acima para cadastrar seu primeiro setor.</p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Setor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($setores as $setor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($setor->getId()); ?></td>
                                <td><?php echo htmlspecialchars($setor->getNome()); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/code/epi-app-t3/setor/edita?idSetor=<?php echo $setor->getId(); ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <a href="/code/epi-app-t3/setor/exclui?idSetor=<?php echo $setor->getId(); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este setor?')">Excluir</a>
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