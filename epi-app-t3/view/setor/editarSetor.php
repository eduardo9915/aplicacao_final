<?php
$pageTitle = 'Editar Setor - Sistema EPI/EPC';
$setores;
$setorAtual;

if (isset($_SESSION["listaSetor"])) {
    $setores = $_SESSION["listaSetor"];

    if (isset($_GET["idSetor"])) {            
        foreach ($setores as $setor) {                                
            if ($setor->getId() == $_GET["idSetor"]) {
                $setorAtual = $setor;
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
            <h1 class="card-title">Editar Setor</h1>
            <a href="/code/epi-app-t3/setor/lista" class="btn btn-secondary">← Voltar</a>
        </div>

        <?php if ($setorAtual): ?>
            <form action="/code/epi-app-t3/setor/edita" method="POST">
                <input type="hidden" name="idSetor" value="<?php echo $setorAtual->getId(); ?>">

                <div class="form-group">
                    <label for="nomeSetor" class="form-label">Nome do Setor *</label>
                    <input type="text" id="nomeSetor" name="nomeSetor" value="<?php echo $setorAtual->getNome(); ?>" class="form-input" required>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">💾 Atualizar Setor</button>
                    <a href="/code/epi-app-t3/setor/lista" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>    
        <?php else: ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <span>Erro: setor não encontrado.</span>
            </div>
        <?php endif;?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
