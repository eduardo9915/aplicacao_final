<?php
$pageTitle = 'Cadastro de Produto - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Cadastro de Produto</h1>
            <a href="/code/epi-app-t3/produto/lista" class="btn btn-secondary">← Voltar</a>
        </div>

        <form action="/code/epi-app-t3/produto/cadastro" method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                <div class="form-group">
                    <label for="nome" class="form-label">Nome *</label>
                    <input type="text" id="nome" name="nome" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="tipo" class="form-label">Tipo</label>
                    <select id="tipo" name="tipo" class="form-select">
                        <option value="">Selecione um tipo</option>
                        <option value="EPI">EPI</option>
                        <option value="EPC">EPC</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" id="marca" name="marca" class="form-input">
                </div>

                <div class="form-group">
                    <label for="dataRegistro" class="form-label">Data de Registro</label>
                    <input type="date" id="dataRegistro" name="dataRegistro" class="form-input" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div class="form-group">
                    <label for="validade" class="form-label">Validade</label>
                    <input type="date" id="validade" name="validade" class="form-input">
                </div>

                <div class="form-group">
                    <label for="ca" class="form-label">CA</label>
                    <input type="text" id="ca" name="ca" class="form-input">
                </div>

                <div class="form-group">
                    <label for="caValidade" class="form-label">Validade do CA</label>
                    <input type="date" id="caValidade" name="caValidade" class="form-input">
                </div>
            </div>

            <div class="form-group">
                <label for="discriminacao" class="form-label">Discriminação</label>
                <textarea id="discriminacao" name="discriminacao" class="form-textarea" rows="4"></textarea>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">💾 Salvar Produto</button>
                <a href="/code/epi-app-t3/produto/lista" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>