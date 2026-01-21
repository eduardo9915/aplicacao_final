<?php
$pageTitle = 'Cadastro de Entrada - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Cadastro de Entrada</h1>
            <a href="/code/epi-app-t3/entrada/lista" class="btn btn-secondary">← Voltar</a>
        </div>
        
        <form action="/code/epi-app-t3/entrada/cadastro" method="POST">
            <div class="form-group">
                <label for="dataHora" class="form-label">Data e Hora:</label>
                <input type="datetime-local" id="dataHora" name="dataHora" value="<?php echo date('Y-m-d\TH:i'); ?>" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="tipo" class="form-label">Tipo:</label>
                <select name="tipo" id="tipo" class="form-select">
                    <option value="">Selecione um tipo</option>
                    <option value="EPI">EPI(s)</option>
                    <option value="EPC">EPC(s)</option>
                    <option value="EPI_EPC">EPI(s) e EPC(s)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="observacao" class="form-label">Observação:</label>
                <textarea id="observacao" name="observacao" class="form-textarea"></textarea>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Produtos</h3>
                </div>
                
                <div id="produtosContainer">
                    <!-- linhas adicionadas dinamicamente -->
                </div>

                <button type="button" id="addProdutoBtn" class="btn btn-secondary mb-3">Adicionar produto</button>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">💾 Salvar Entrada</button>
                <a href="/code/epi-app-t3/entrada/lista" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

    <template id="produtoTemplate">
        <div class="produto-row d-flex gap-2 mb-3">
            <select name="productId[]" class="form-select" style="flex: 1;">
                <?php if (isset($_SESSION['listaProduto']) && !empty($_SESSION['listaProduto'])): ?>
                    <?php foreach ($_SESSION['listaProduto'] as $p): ?>
                        <option value="<?php echo $p->getId(); ?>"><?php echo htmlspecialchars($p->getNome()); ?></option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="0">Nenhum produto</option>
                <?php endif; ?>
            </select>
            <input type="number" name="quantidade[]" value="1" min="1" class="form-input" style="width: 120px;" placeholder="Qtd" />
            <input type="text" name="observacaoProd[]" class="form-input" style="flex: 1;" placeholder="Observação" />
            <button type="button" class="btn btn-danger btn-sm removeProdutoBtn">Remover</button>
        </div>
    </template>

    <style>
        .produto-row {
            align-items: center;
        }
    </style>

    <script>
        const addBtn = document.getElementById('addProdutoBtn');
        const container = document.getElementById('produtosContainer');
        const template = document.getElementById('produtoTemplate');

        addBtn.addEventListener('click', () => {
            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
        });

        container.addEventListener('click', (e) => {
            if (e.target && e.target.classList.contains('removeProdutoBtn')) {
                e.target.closest('.produto-row').remove();
            }
        });

        // adicionar uma linha inicial
        (function(){
            addBtn.click();
        })();
    </script>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>