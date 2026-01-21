<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pedido = $_SESSION['pedidoParaReprovar'] ?? null;
unset($_SESSION['pedidoParaReprovar']);

if (!$pedido) {
    header('Location: /code/epi-app-t3/pedido/lista');
    exit;
}

$pageTitle = 'Reprovar Pedido - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Reprovar Pedido</h1>
        </div>

        <div class="alert alert-warning">
            <strong>Atenção:</strong> Você está reprovando o pedido #<?php echo htmlspecialchars($pedido->getId()); ?>.
        </div>

        <form method="POST" action="/code/epi-app-t3/pedido/rejeitar">
            <input type="hidden" name="idPedido" value="<?php echo htmlspecialchars($pedido->getId()); ?>">
            
            <div class="form-group">
                <label class="form-label">Informações do Pedido</label>
                <div style="background-color: #f9fafb; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($pedido->getId()); ?></p>
                    <p><strong>Data/Hora:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($pedido->getDataHora()))); ?></p>
                    <p><strong>Usuário:</strong> <?php echo htmlspecialchars($pedido->getUsuario() ? ($pedido->getUsuario()->getNome() . ' ' . $pedido->getUsuario()->getSobrenome()) : '-'); ?></p>
                    <p><strong>Tipo:</strong> <?php echo htmlspecialchars($pedido->getTipo()); ?></p>
                    <p><strong>Observação:</strong> <?php echo htmlspecialchars($pedido->getObservacao() ?? '-'); ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="motivoReprovacao">
                    <strong>Motivo da Reprovação *</strong>
                </label>
                <textarea 
                    name="motivoReprovacao" 
                    id="motivoReprovacao" 
                    class="form-textarea" 
                    rows="4" 
                    placeholder="Descreva o motivo pelo qual este pedido está sendo reprovado..."
                    required
                ></textarea>
                <small style="color: var(--text-secondary);">Este campo é obrigatório e será visível para o solicitante.</small>
            </div>

            <div class="form-group">
                <div class="btn-group">
                    <button type="submit" class="btn btn-danger">
                        ❌ Confirmar Reprovação
                    </button>
                    <a href="/code/epi-app-t3/pedido/lista" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
