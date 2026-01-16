<?php
$estoques = [];
$estoqueAtual = null;
if (isset($_SESSION['listaEstoque'])) {
    $estoques = $_SESSION['listaEstoque'];
    if (isset($_GET['idEstoque'])) {
        foreach ($estoques as $e) {
            if ($e->getId() == $_GET['idEstoque']) {
                $estoqueAtual = $e;
                break;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Estoque</title>
</head>
<body>
    <h1>Editar Estoque</h1>

    <?php if ($estoqueAtual): ?>
        <form action="/code/epi-app-t3/estoque/edita" method="POST">
            <input type="hidden" name="idEstoque" value="<?php echo $estoqueAtual->getId(); ?>">

            <label for="idProduto">Produto:</label>
            <select id="idProduto" name="idProduto" required>
                <?php if (isset($_SESSION['listaProduto'])): ?>
                    <?php foreach ($_SESSION['listaProduto'] as $p): ?>
                        <option value="<?php echo $p->getId(); ?>" <?php echo $p->getId() == $estoqueAtual->getProdutoId()->getId() ? 'selected' : ''?>><?php echo htmlspecialchars($p->getNome()); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select><br><br>

            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" value="<?php echo $estoqueAtual->getQuantidade(); ?>" min="0" required><br><br>

            <label for="dataEntrada">Data de Entrada:</label>
            <input type="date" id="dataEntrada" name="dataEntrada" value="<?php echo htmlspecialchars($estoqueAtual->getDataEntrada()); ?>" required><br><br>

            <label for="dataSaida">Data de Saída:</label>
            <input type="date" id="dataSaida" name="dataSaida" value="<?php echo htmlspecialchars($estoqueAtual->getDataSaida() ?? ''); ?>"><br><br>

            <button type="submit">Atualizar</button>
        </form>
    <?php else: ?>
        <p>Erro: estoque não encontrado.</p>
    <?php endif; ?>

    <a href="/code/epi-app-t3/estoque/lista">Voltar</a>
</body>
</html>