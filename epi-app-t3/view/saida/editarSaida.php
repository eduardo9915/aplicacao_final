<?php

$saidas = [];
$saidaAtual = null;
if (isset($_SESSION['listaSaida'])) {
    $saidas = $_SESSION['listaSaida'];

    if (isset($_GET['idSaida'])) {
        foreach ($saidas as $s) {
            if ($s->getId() == $_GET['idSaida']) {
                $saidaAtual = $s;
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
    <title>Editar Saida</title>
</head>
<body>
    <h1>Editar Saida</h1>

    <?php if ($saidaAtual): ?>
        <form action="/code/epi-app-t3/saida/edita" method="POST">
            <input type="hidden" name="idSaida" value="<?php echo $saidaAtual->getId(); ?>">

            <label for="dataHora">Data e Hora:</label>
            <?php
                $dh = $saidaAtual->getDataHora();
                // se estiver no formato 'YYYY-MM-DD HH:MM:SS' converte para 'YYYY-MM-DDTHH:MM' para o input
                if (strpos($dh, ' ') !== false) {
                    $dh = str_replace(' ', 'T', substr($dh, 0, 16));
                }
            ?>
            <input type="datetime-local" id="dataHora" name="dataHora" value="<?php echo htmlspecialchars($dh); ?>" required><br><br>

            <label for="tipo">Tipo:</label>
            <input type="text" id="tipo" name="tipo" value="<?php echo htmlspecialchars($saidaAtual->getTipo()); ?>"><br><br>

            <label for="observacao">Observação:</label>
            <textarea id="observacao" name="observacao"><?php echo htmlspecialchars($saidaAtual->getObservacao()); ?></textarea><br><br>

            <button type="submit">Atualizar</button>
        </form>
    <?php else: ?>
        <p>Erro: saida não encontrada.</p>
    <?php endif; ?>

    <a href="/code/epi-app-t3/saida/lista">Voltar para a lista</a>
</body>
</html>