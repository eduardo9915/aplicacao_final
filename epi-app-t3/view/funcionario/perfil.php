<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuarioLogado'])) {
    header('Location: /code/epi-app-t3/login');
    exit;
}

$usuario = $_SESSION['usuarioLogado'];

$pageTitle = 'Meu Perfil - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">👤 Meu Perfil</h1>
            <a href="/code/epi-app-t3/funcionario/home" class="btn btn-secondary">← Voltar</a>
        </div>

        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <div class="avatar-placeholder">
                        <?php echo strtoupper(substr($usuario->getNome(), 0, 1) . substr($usuario->getSobrenome(), 0, 1)); ?>
                    </div>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($usuario->getNome() . ' ' . $usuario->getSobrenome()); ?></h2>
                    <span class="badge badge-secondary">👷 Funcionário</span>
                </div>
            </div>

            <div class="profile-details">
                <div class="detail-group">
                    <h3>📋 Dados Pessoais</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Matrícula:</label>
                            <span><?php echo htmlspecialchars($usuario->getMatricula()); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>CPF:</label>
                            <span><?php echo htmlspecialchars($usuario->getCpf()); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Telefone:</label>
                            <span><?php echo htmlspecialchars($usuario->getTelefone() ?: 'Não informado'); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Email:</label>
                            <span><?php echo htmlspecialchars($usuario->getEmail()); ?></span>
                        </div>
                    </div>
                </div>

                <div class="detail-group">
                    <h3>🏢 Dados Profissionais</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <label>Cargo:</label>
                            <span><?php echo htmlspecialchars($usuario->getCargo()); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Setor:</label>
                            <span><?php echo htmlspecialchars($usuario->getSetor() ? $usuario->getSetor()->getNome() : 'Não definido'); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Data de Admissão:</label>
                            <span><?php echo htmlspecialchars($usuario->getDataAdmissao()); ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Data de Demissão:</label>
                            <span><?php echo htmlspecialchars($usuario->getDataDemissao() ?: 'Ativo'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <div class="alert alert-info">
                    <span>ℹ️</span>
                    <span>Para alterar seus dados pessoais, entre em contato com o setor administrativo.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 0 auto;
}

.profile-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.profile-avatar {
    flex-shrink: 0;
}

.avatar-placeholder {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
}

.profile-info h2 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
}

.profile-details {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.detail-group h3 {
    margin: 0 0 1rem 0;
    color: #374151;
    font-size: 1.1rem;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem;
    background: #f9fafb;
    border-radius: 0.5rem;
}

.detail-item label {
    font-weight: 600;
    color: #6b7280;
}

.detail-item span {
    color: #1f2937;
}

.profile-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e5e7eb;
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
