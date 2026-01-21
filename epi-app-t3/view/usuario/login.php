<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = 'Login - Sistema EPI/EPC';
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
            <img src="" alt="">
            <h1 class="login-title">SafeStock EPI/EPC</h1>
            <p class="login-subtitle">Faça login para continuar</p>
        </div>

        <?php if (isset($_SESSION['loginError'])): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <span><?php echo htmlspecialchars($_SESSION['loginError']); unset($_SESSION['loginError']); ?></span>
            </div>
        <?php endif; ?>

        <form action="/code/epi-app-t3/login" method="POST">
            <div class="form-group">
                <label for="matricula" class="form-label">Matrícula</label>
                <input 
                    type="text" 
                    id="matricula" 
                    name="matricula" 
                    class="form-input" 
                    required 
                    placeholder="Ex: MAT12345"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="senha" class="form-label">Senha</label>
                <input 
                    type="password" 
                    id="senha" 
                    name="senha" 
                    class="form-input" 
                    required
                    placeholder="Digite sua senha"
                >
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Entrar
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="/code/epi-app-t3/usuario/cadastro" class="btn-link">Não tem uma conta? Cadastre-se</a>
        </div>

        <div class="text-center mt-2">
            <a href="/code/epi-app-t3/esqueci-senha" class="btn-link">Esqueci minha senha</a>
        </div>
    </div>
</body>
</html>
