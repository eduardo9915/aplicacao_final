<?php
$pageTitle = 'Cadastro de Setor - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="card-title">Cadastro de Setor</h1>
                <a href="/code/epi-app-t3/setor/lista" class="btn btn-secondary">← Voltar</a>
            </div>
            
            <form action="/code/epi-app-t3/setor/cadastro" method="POST">
                <div class="form-group">
                    <label for="nomeSetor" class="form-label">Nome do Setor *</label>
                    <input type="text" id="nomeSetor" name="nomeSetor" class="form-input" required>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">💾 Salvar Setor</button>
                    <a href="/code/epi-app-t3/setor/lista" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <?php include __DIR__ . '/../includes/footer.php'; ?>