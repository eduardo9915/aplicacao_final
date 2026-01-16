<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Recuperar Senha - Sistema EPI/EPC';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="/code/epi-app-t3/view/assets/css/style.css">
</head>
<body id="login">
    <div class="login-container">
        <div class="login-header">
            <h1 class="login-title">SafeStock EPI/EPC</h1>
            <p class="login-subtitle">Recuperar senha</p>
        </div>

        <?php if (isset($_SESSION['successMessage'])): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <span><?php echo htmlspecialchars($_SESSION['successMessage']); unset($_SESSION['successMessage']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errorMessage'])): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <span><?php echo htmlspecialchars($_SESSION['errorMessage']); unset($_SESSION['errorMessage']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['successMessage'])): ?>
            <form action="/code/epi-app-t3/esqueci-senha" method="POST">
                <div class="form-group">
                    <label for="email" class="form-label">E-mail</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        required 
                        placeholder="Digite seu e-mail cadastrado"
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="matricula" class="form-label">Matrícula</label>
                    <input 
                        type="text" 
                        id="matricula" 
                        name="matricula" 
                        class="form-input" 
                        required 
                        placeholder="Digite sua matrícula"
                    >
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Recuperar Senha
                </button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="/code/epi-app-t3/login" class="btn-link">Voltar para o login</a>
        </div>
    </div>
</body>
</html>
