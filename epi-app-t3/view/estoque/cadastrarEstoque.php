<?php
$pageTitle = 'Cadastro de Estoque - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Cadastro de Estoque</h1>
            <a href="/code/epi-app-t3/estoque/lista" class="btn btn-secondary">← Voltar</a>
        </div>
        
        <form action="/code/epi-app-t3/estoque/cadastro" method="POST">
            <div class="form-group">
                <label for="idProduto" class="form-label">Produto *</label>
                <select id="idProduto" name="idProduto" class="form-select" required>
                    <?php if (isset($_SESSION['listaProduto'])): ?>
                        <?php foreach ($_SESSION['listaProduto'] as $p): ?>
                            <option value="<?php echo $p->getId(); ?>"><?php echo htmlspecialchars($p->getNome()); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantidade" class="form-label">Quantidade *</label>
                <input type="number" id="quantidade" name="quantidade" value="1" min="0" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="dataEntrada" class="form-label">Data de Entrada *</label>
                <input type="date" id="dataEntrada" name="dataEntrada" value="<?php echo date('Y-m-d'); ?>" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="dataSaida" class="form-label">Data de Saída</label>
                <input type="date" id="dataSaida" name="dataSaida" class="form-input">
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">💾 Salvar Estoque</button>
                <a href="/code/epi-app-t3/estoque/lista" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>