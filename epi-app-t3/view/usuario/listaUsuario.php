<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarios = [];
if (isset($_SESSION['listaUsuario'])) {
    $usuarios = $_SESSION['listaUsuario'];
}

$pageTitle = 'Lista de Usuários - Sistema EPI/EPC';
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lista de Usuários</h1>
            <a href="/code/epi-app-t3/usuario/cadastro" class="btn btn-primary">
                ➕ Novo Usuário
            </a>
        </div>

        <?php if (empty($usuarios)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">👥</div>
                <div class="empty-state-text">Nenhum usuário cadastrado.</div>
                <a href="/code/epi-app-t3/usuario/cadastro" class="btn btn-primary mt-2">
                    Cadastrar Primeiro Usuário
                </a>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome Completo</th>
                            <th>Email</th>
                            <th>Setor</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u->getId()); ?></td>
                                <td><?php echo htmlspecialchars($u->getNome() . ' ' . $u->getSobrenome()); ?></td>
                                <td><?php echo htmlspecialchars($u->getEmail()); ?></td>
                                <td><?php echo htmlspecialchars($u->getSetor() ? $u->getSetor()->getNome() : '-'); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a href="/code/epi-app-t3/usuario/edita?idUsuario=<?php echo $u->getId(); ?>" class="btn btn-sm btn-secondary">
                                            ✏️ Editar
                                        </a>
                                        <!--<a 
                                            href="/code/epi-app-t3/usuario/exclui?idUsuario=<?php echo $u->getId(); ?>" 
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Tem certeza que deseja excluir este usuário?');"
                                        >
                                            🗑️ Excluir
                                        </a>-->
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
