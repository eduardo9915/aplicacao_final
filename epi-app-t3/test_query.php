<?php
require_once 'utils/Conexao.php';

try {
    $con = Conexao::pegarConexao();
    
    echo "=== Verificando estrutura das tabelas ===\n";
    
    // Verificar estrutura da tabela produto
    echo "\n--- Estrutura da tabela PRODUTO ---\n";
    $stmt = $con->query("DESCRIBE produto");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
    }
    
    // Verificar estrutura da tabela estoque
    echo "\n--- Estrutura da tabela ESTOQUE ---\n";
    $stmt = $con->query("DESCRIBE estoque");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
    }
    
    echo "\n=== Verificando dados existentes ===\n";
    
    // Verificar produtos
    echo "\n--- Produtos cadastrados ---\n";
    $stmt = $con->query("SELECT id_produto, nome_produto FROM produto ORDER BY nome_produto");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total de produtos: " . count($produtos) . "\n";
    foreach ($produtos as $p) {
        echo "ID: " . $p['id_produto'] . " - Nome: " . $p['nome_produto'] . "\n";
    }
    
    // Verificar estoque
    echo "\n--- Registros de estoque ---\n";
    $stmt = $con->query("SELECT * FROM estoque");
    $estoques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total de registros em estoque: " . count($estoques) . "\n";
    foreach ($estoques as $e) {
        echo "ID: " . $e['id_estoque'] . " - Produto: " . $e['produto_estoque'] . " - Quantidade: " . $e['quantidade_estoque'] . "\n";
    }
    
    echo "\n=== Testando diferentes combinações de JOIN ===\n";
    
    // Teste 1: produto_estoque
    echo "\n--- Teste 1: JOIN com produto_estoque ---\n";
    $sql1 = "SELECT p.id_produto, p.nome_produto, COALESCE(SUM(e.quantidade_estoque), 0) as qtd
              FROM produto p 
              LEFT JOIN estoque e ON p.id_produto = e.produto_estoque 
              GROUP BY p.id_produto 
              ORDER BY p.nome_produto";
    $stmt1 = $con->prepare($sql1);
    $stmt1->execute();
    $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    echo "Resultados: " . count($result1) . "\n";
    foreach ($result1 as $r) {
        echo "Produto: " . $r['nome_produto'] . " - Estoque: " . $r['qtd'] . "\n";
    }
    
    // Teste 2: id_produto
    echo "\n--- Teste 2: JOIN com id_produto ---\n";
    $sql2 = "SELECT p.id_produto, p.nome_produto, COALESCE(SUM(e.quantidade_estoque), 0) as qtd
              FROM produto p 
              LEFT JOIN estoque e ON p.id_produto = e.id_produto 
              GROUP BY p.id_produto 
              ORDER BY p.nome_produto";
    $stmt2 = $con->prepare($sql2);
    $stmt2->execute();
    $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "Resultados: " . count($result2) . "\n";
    foreach ($result2 as $r) {
        echo "Produto: " . $r['nome_produto'] . " - Estoque: " . $r['qtd'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
}
?>
