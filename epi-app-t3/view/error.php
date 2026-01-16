<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Erro - Sistema EPI/EPC';
include __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">⚠️</div>
            <h1 class="empty-state-text">Ops! Algo deu errado</h1>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                <?php 
                if (isset($_SESSION['errorMessage'])) {
                    echo htmlspecialchars($_SESSION['errorMessage']);
                    unset($_SESSION['errorMessage']);
                } else {
                    echo 'Ocorreu um erro inesperado. Por favor, tente novamente.';
                }
                ?>
            </p>
            <div class="btn-group">
                <a href="/code/epi-app-t3/" class="btn btn-primary">
                    ← Voltar para o Início
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    ← Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
