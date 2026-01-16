<?php
$pageTitle = 'Novo Pedido - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Novo Pedido</h1>
        </div>
        
        <form action="/code/epi-app-t3/pedido/cadastro" method="POST">
            <div class="form-group">
                <label for="dataHora" class="form-label">Data e Hora:</label>
                <input type="datetime-local" id="dataHora" name="dataHora" value="<?php echo date('Y-m-d\TH:i'); ?>" class="form-input" required>
            </div>

            <?php if (!FuncionarioAuth::ehFuncionario()): ?>
            <div class="form-group">
                <label for="userSelect" class="form-label">Selecionar usuário do sistema:</label>
                <select id="userSelect" class="form-select" name="matricula">
                    <option value="">Identificação do usuário</option>
                    <?php if (isset($_SESSION['listaUsuario']) && !empty($_SESSION['listaUsuario'])): ?>
                        <?php foreach ($_SESSION['listaUsuario'] as $u): ?>
                            <?php $display = htmlspecialchars(($u['nome_usuario'] ?? '') . ' ' . ($u['sobrenome_usuario'] ?? '') . ' (' . ($u['matricula_usuario'] ?? '') . ')'); ?>
                            <option value="<?php echo htmlspecialchars($u['matricula_usuario'] ?? ''); ?>"><?php echo $display; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <?php else: ?>
            <!-- Campo oculto para funcionário logado - será preenchido automaticamente -->
            <input type="hidden" name="matricula" value="<?php echo htmlspecialchars(FuncionarioAuth::getMatricula()); ?>">
            <?php endif; ?>

            <!-- <div class="form-group">
                <label for="matricula" class="form-label">Matrícula do solicitante (preenchida automaticamente ao selecionar):</label>
                <input type="text" id="matricula" name="matricula" class="form-input" required placeholder="Ex: 12345">
            </div> -->


            <div class="form-group">
                <label for="tipo" class="form-label">Tipo:</label>
                <select id="tipo" name="tipo" class="form-select">
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
                <button type="submit" class="btn btn-primary">Enviar Pedido</button>
            </div>
        </form>
    </div>
</div>

    <template id="produtoTemplate">
        <div class="produto-row d-flex gap-2 mb-3">
            <select name="productId[]" class="form-select produto-select" style="flex: 1;">
                <?php if (isset($_SESSION['listaProduto']) && !empty($_SESSION['listaProduto'])): ?>
                    <?php foreach ($_SESSION['listaProduto'] as $p): ?>
                        <?php if (is_array($p)): ?>
                            <!-- Formato array (novo método com estoque) -->
                            <?php 
                            $quantidade = isset($p['quantidade_estoque']) ? $p['quantidade_estoque'] : 0;
                            $nomeProduto = $p['nome_produto'] ?? 'Produto sem nome';
                            $idProduto = $p['id_produto'] ?? 0;
                            $textoEstoque = $quantidade > 0 ? " (Estoque: $quantidade)" : " (Sem estoque)";
                            ?>
                            <option value="<?php echo $idProduto; ?>" data-quantidade="<?php echo $quantidade; ?>">
                                <?php echo htmlspecialchars($nomeProduto . $textoEstoque); ?>
                            </option>
                        <?php else: ?>
                            <!-- Formato objeto (método antigo) -->
                            <option value="<?php echo $p->getId(); ?>" data-quantidade="0">
                                <?php echo htmlspecialchars($p->getNome() . ' (Estoque: não informado)'); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="0">Nenhum produto encontrado</option>
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
        .estoque-baixo {
            background-color: #fff3cd !important;
            border-color: #ffeaa7 !important;
        }
        .estoque-insuficiente {
            background-color: #f8d7da !important;
            border-color: #f5c6cb !important;
        }
        .alerta-estoque {
            font-size: 0.8em;
            margin-top: 0.25rem;
            color: #856404;
        }
        .alerta-estoque.danger {
            color: #721c24;
        }
    </style>

    <script>
        const userSelect = document.getElementById('userSelect');
        const matriculaInput = document.getElementById('matricula');
        if (userSelect) {
            userSelect.addEventListener('change', () => {
                matriculaInput.value = userSelect.value || '';
            });
        }
        const addBtn = document.getElementById('addProdutoBtn');
        const container = document.getElementById('produtosContainer');
        const template = document.getElementById('produtoTemplate');

        function validarEstoque(select, quantidadeInput) {
            const option = select.options[select.selectedIndex];
            const quantidadeEstoque = parseInt(option.dataset.quantidade) || 0;
            const quantidadeSolicitada = parseInt(quantidadeInput.value) || 0;
            
            // Remover classes anteriores
            select.classList.remove('estoque-baixo', 'estoque-insuficiente');
            
            // Remover alerta anterior se existir
            const alertaExistente = select.parentNode.querySelector('.alerta-estoque');
            if (alertaExistente) {
                alertaExistente.remove();
            }
            
            if (quantidadeEstoque === 0) {
                select.classList.add('estoque-insuficiente');
                const alerta = document.createElement('div');
                alerta.className = 'alerta-estoque danger';
                alerta.textContent = 'Produto sem estoque disponível';
                select.parentNode.appendChild(alerta);
                return false;
            } else if (quantidadeSolicitada > quantidadeEstoque) {
                select.classList.add('estoque-insuficiente');
                const alerta = document.createElement('div');
                alerta.className = 'alerta-estoque danger';
                alerta.textContent = `Estoque insuficiente! Disponível: ${quantidadeEstoque}`;
                select.parentNode.appendChild(alerta);
                return false;
            } else if (quantidadeEstoque <= 5) {
                select.classList.add('estoque-baixo');
                const alerta = document.createElement('div');
                alerta.className = 'alerta-estoque';
                alerta.textContent = `Estoque baixo: ${quantidadeEstoque} unidades`;
                select.parentNode.appendChild(alerta);
            }
            
            return true;
        }

        function adicionarEventosLinha(produtoRow) {
            const select = produtoRow.querySelector('.produto-select');
            const quantidadeInput = produtoRow.querySelector('input[name="quantidade[]"]');
            
            if (select && quantidadeInput) {
                // Validar quando selecionar produto
                select.addEventListener('change', () => {
                    validarEstoque(select, quantidadeInput);
                });
                
                // Validar quando alterar quantidade
                quantidadeInput.addEventListener('input', () => {
                    validarEstoque(select, quantidadeInput);
                });
                
                // Validar no carregamento se já houver produto selecionado
                if (select.value && select.value !== '0') {
                    validarEstoque(select, quantidadeInput);
                }
            }
        }

        addBtn.addEventListener('click', () => {
            const clone = template.content.cloneNode(true);
            container.appendChild(clone);
            
            // Adicionar eventos na nova linha
            const novaLinha = container.lastElementChild;
            adicionarEventosLinha(novaLinha);
        });

        container.addEventListener('click', (e) => {
            if (e.target && e.target.classList.contains('removeProdutoBtn')) {
                e.target.closest('.produto-row').remove();
            }
        });

        // Validar formulário antes de enviar
        document.querySelector('form').addEventListener('submit', (e) => {
            const selects = container.querySelectorAll('.produto-select');
            let estoqueValido = true;
            
            selects.forEach(select => {
                const quantidadeInput = select.parentNode.querySelector('input[name="quantidade[]"]');
                if (!validarEstoque(select, quantidadeInput)) {
                    estoqueValido = false;
                }
            });
            
            if (!estoqueValido) {
                e.preventDefault();
                alert('Existem produtos com estoque insuficiente. Por favor, ajuste as quantidades.');
            }
        });

        // adicionar uma linha inicial
        (function(){
            addBtn.click();
        })();
    </script>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>