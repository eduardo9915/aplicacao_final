<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarios = [];
$usuarioAtual = null;
if (isset($_SESSION['usuarioEdit'])) {
    $usuarioAtual = $_SESSION['usuarioEdit'];
    unset($_SESSION['usuarioEdit']); // Limpar após usar
}

if (isset($_SESSION['listaUsuario'])) {
    $usuarios = $_SESSION['listaUsuario'];
}

$pageTitle = 'Editar Usuário - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Editar Usuário</h1>
            <a href="/code/epi-app-t3/usuario/lista" class="btn btn-secondary">← Voltar</a>
        </div>

        <?php if ($usuarioAtual): ?>
            <?php
                $setores = [];
                if (isset($_SESSION['listaSetor'])) {
                    $setores = $_SESSION['listaSetor'];
                }
                $usuarioSetorId = null;
                if ($usuarioAtual->getSetor() !== null) {
                    $usuarioSetorId = $usuarioAtual->getSetor()->getId();
                }
            ?>

            <?php if (isset($_SESSION['errorMessage'])): ?>
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <span><?php echo htmlspecialchars($_SESSION['errorMessage']); unset($_SESSION['errorMessage']); ?></span>
                </div>
            <?php endif; ?>

            <form action="/code/epi-app-t3/usuario/edita" method="POST">
                <input type="hidden" name="idUsuario" value="<?php echo $usuarioAtual->getId(); ?>">

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div class="form-group">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text" id="nome" name="nome" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getNome()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="sobrenome" class="form-label">Sobrenome *</label>
                        <input type="text" id="sobrenome" name="sobrenome" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getSobrenome()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="matricula" class="form-label">Matrícula *</label>
                        <input type="text" id="matricula" name="matricula" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getMatricula()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="cpf" class="form-label">CPF *</label>
                        <input type="text" id="cpf" name="cpf" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getCpf()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" id="email" name="email" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getEmail()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getTelefone()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="cargo" class="form-label">Cargo *</label>
                        <input type="text" id="cargo" name="cargo" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getCargo()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="setor" class="form-label">Setor *</label>
                        <?php if (!empty($setores)): ?>
                            <select id="setor" name="setor" class="form-select" required>
                                <option value="">Selecione um setor</option>
                                <?php foreach ($setores as $setor): ?>
                                    <option value="<?php echo $setor->getId(); ?>" <?php echo ($usuarioSetorId !== null && $usuarioSetorId == $setor->getId()) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($setor->getNome()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <p class="alert alert-warning">Não há setores cadastrados.</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="dataAdmissao" class="form-label">Data de Admissão *</label>
                        <input type="date" id="dataAdmissao" name="dataAdmissao" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getDataAdmissao()); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="dataDemissao" class="form-label">Data de Demissão</label>
                        <input type="date" id="dataDemissao" name="dataDemissao" class="form-input" value="<?php echo htmlspecialchars($usuarioAtual->getDataDemissao() ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" id="senha" name="senha" class="form-input" placeholder="Deixe em branco para manter a senha atual">
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" id="admUsuario" name="admUsuario" class="form-check-input" value="1" 
                                   <?php echo $usuarioAtual->getAdmUsuario() ? 'checked' : ''; ?>>
                            <label for="admUsuario" class="form-check-label">
                                👤 Administrador do Sistema
                            </label>
                            <small class="form-text text-muted">
                                Marque esta opção para conceder acesso administrativo completo ao sistema.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="btn-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        💾 Atualizar Usuário
                    </button>
                    <a href="/code/epi-app-t3/usuario/lista" class="btn btn-secondary">
                        Cancelar
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <span>Erro: usuário não encontrado.</span>
            </div>
            <a href="/code/epi-app-t3/usuario/lista" class="btn btn-secondary">Voltar para a lista</a>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
