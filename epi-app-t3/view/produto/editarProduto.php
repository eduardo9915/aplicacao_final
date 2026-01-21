<?php
$pageTitle = 'Editar Produto - Sistema EPI/EPC';
$produtos;
$produtoAtual;
if (isset($_SESSION["listaProduto"])) {
    $produtos = $_SESSION["listaProduto"];
    
    if (isset($_GET['idProduto'])) {
        foreach ($produtos as $p) {
            if ($p->getId() == $_GET['idProduto']) {
                $produtoAtual = $p;
                break;
            }
        }
    }
}
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Editar Produto</h1>
            <a href="/code/epi-app-t3/produto/lista" class="btn btn-secondary">← Voltar</a>
        </div>

        <?php if ($produtoAtual): ?>
            <form action="/code/epi-app-t3/produto/edita" method="POST">
                <input type="hidden" name="idProduto" value="<?php echo $produtoAtual->getId(); ?>">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" id="nome" name="nome" class="form-input" value="<?php echo htmlspecialchars($produtoAtual->getNome()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select id="tipo" name="tipo" class="form-select">
                            <option value="">Selecione um tipo</option>
                            <option value="EPI" <?php echo ($produtoAtual->getTipo() === 'EPI') ? 'selected' : ''; ?>>EPI</option>
                            <option value="EPC" <?php echo ($produtoAtual->getTipo() === 'EPC') ? 'selected' : ''; ?>>EPC</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" id="marca" name="marca" class="form-input" value="<?php echo htmlspecialchars($produtoAtual->getMarca()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="dataRegistro" class="form-label">Data de Registro</label>
                        <input type="date" id="dataRegistro" name="dataRegistro" class="form-input" value="<?php echo htmlspecialchars($produtoAtual->getDataRegistro()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="validade" class="form-label">Validade</label>
                        <input type="date" id="validade" name="validade" class="form-input" value="<?php echo htmlspecialchars($produtoAtual->getValidade() ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="ca" class="form-label">CA</label>
                        <input type="text" id="ca" name="ca" class="form-input" value="<?php echo htmlspecialchars($produtoAtual->getCa() ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="caValidade" class="form-label">Validade do CA</label>
                        <input type="date" id="caValidade" name="caValidade" class="form-input" value="<?php echo htmlspecialchars($produtoAtual->getCaValidade() ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="discriminacao" class="form-label">Discriminação</label>
                    <textarea id="discriminacao" name="discriminacao" class="form-textarea" rows="4"><?php echo htmlspecialchars($produtoAtual->getDiscriminacao()); ?></textarea>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">💾 Atualizar Produto</button>
                    <a href="/code/epi-app-t3/produto/lista" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <span>Erro: produto não encontrado.</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>