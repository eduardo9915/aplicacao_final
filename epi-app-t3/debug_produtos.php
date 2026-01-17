<?php
require_once 'utils/Conexao.php';
require_once 'repositories/ProdutoDAO.php';

try {
    $con = Conexao::pegarConexao();
    
    // Testar consulta básica de produtos
    $sql1 = 'SELECT COUNT(*) as total FROM produto';
    $stmt1 = $con->prepare($sql1);
    $stmt1->execute();
    $totalProdutos = $stmt1->fetch(PDO::FETCH_ASSOC);
    echo 'Total de produtos: ' . $totalProdutos['total'] . PHP_EOL;
    
    // Testar consulta básica de estoque
    $sql2 = 'SELECT COUNT(*) as total FROM estoque';
    $stmt2 = $con->prepare($sql2);
    $stmt2->execute();
    $totalEstoque = $stmt2->fetch(PDO::FETCH_ASSOC);
    echo 'Total de registros em estoque: ' . $totalEstoque['total'] . PHP_EOL;
    
    // Verificar estrutura das tabelas
    $sql3 = 'DESCRIBE produto';
    $stmt3 = $con->prepare($sql3);
    $stmt3->execute();
    echo 'Estrutura da tabela produto:' . PHP_EOL;
    while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
    }
    
    echo PHP_EOL . 'Estrutura da tabela estoque:' . PHP_EOL;
    $sql4 = 'DESCRIBE estoque';
    $stmt4 = $con->prepare($sql4);
    $stmt4->execute();
    while ($row = $stmt4->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . ' - ' . $row['Type'] . PHP_EOL;
    }
    
    // Testar a consulta do método listarTodosComEstoque
    echo PHP_EOL . 'Testando consulta listarTodosComEstoque:' . PHP_EOL;
    $sql = "SELECT p.*, COALESCE(SUM(e.quantidade_estoque), 0) as quantidade_estoque 
            FROM produto p 
            LEFT JOIN estoque e ON p.id_produto = e.id_produto 
            GROUP BY p.id_produto 
            ORDER BY p.nome_produto";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo 'Resultados encontrados: ' . count($produtos) . PHP_EOL;
    foreach ($produtos as $produto) {
        echo 'ID: ' . $produto['id_produto'] . ' - Nome: ' . $produto['nome_produto'] . ' - Estoque: ' . $produto['quantidade_estoque'] . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage() . PHP_EOL;
}
?>
