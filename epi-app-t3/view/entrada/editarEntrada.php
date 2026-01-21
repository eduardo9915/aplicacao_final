<?php

$entradas = [];
$entradaAtual = null;
if (isset($_SESSION['listaEntrada'])) {
    $entradas = $_SESSION['listaEntrada'];

    if (isset($_GET['idEntrada'])) {
        foreach ($entradas as $e) {
            if ($e->getId() == $_GET['idEntrada']) {
                $entradaAtual = $e;
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
    <title>Editar Entrada</title>
</head>
<body>
    <h1>Editar Entrada</h1>

    <?php if ($entradaAtual): ?>
        <form action="/code/epi-app-t3/entrada/edita" method="POST">
            <input type="hidden" name="idEntrada" value="<?php echo $entradaAtual->getId(); ?>">

            <label for="dataHora">Data e Hora:</label>
            <?php
                $dh = $entradaAtual->getDataHora();
                // se estiver no formato 'YYYY-MM-DD HH:MM:SS' converte para 'YYYY-MM-DDTHH:MM' para o input
                if (strpos($dh, ' ') !== false) {
                    $dh = str_replace(' ', 'T', substr($dh, 0, 16));
                }
            ?>
            <input type="datetime-local" id="dataHora" name="dataHora" value="<?php echo htmlspecialchars($dh); ?>" required><br><br>

            <label for="tipo">Tipo:</label>
            <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($entradaAtual->getTipo()); ?>"><br><br>

            <label for="observacao">Observação:</label>
            <textarea id="observacao" name="observacao"><?php echo htmlspecialchars($entradaAtual->getObservacao()); ?></textarea><br><br>

            <button type="submit">Atualizar</button>
        </form>
    <?php else: ?>
        <p>Erro: entrada não encontrada.</p>
    <?php endif; ?>

    <a href="/code/epi-app-t3/entrada/lista">Voltar para a lista</a>
</body>
</html>