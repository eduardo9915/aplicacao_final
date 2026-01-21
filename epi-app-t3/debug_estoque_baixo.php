<?php
require_once __DIR__ . "/repositories/EstoqueDAO.php";
require_once __DIR__ . "/utils/Conexao.php";

$estoqueDAO = new EstoqueDAO();
$produtosEstoqueBaixo = $estoqueDAO->buscarProdutosEstoqueBaixo();

echo "<h2>Debug: Produtos com Estoque Baixo</h2>";
echo "<pre>";
echo "Número de produtos encontrados: " . count($produtosEstoqueBaixo) . "\n\n";
print_r($produtosEstoqueBaixo);
echo "</pre>";

// Teste da query diretamente
try {
    $conexao = new Conexao();
    $pdo = $conexao::pegarConexao();
    
    $sql = "SELECT 
                p.nome_produto,
                p.id_produto,
                COALESCE(SUM(e.quantidade_estoque), 0) as quantidade_total
            FROM produto p 
            LEFT JOIN estoque e ON p.id_produto = e.produto_estoque
            GROUP BY p.id_produto, p.nome_produto
            HAVING quantidade_total <= 10
            ORDER BY quantidade_total ASC";
    
    $stmt = $pdo->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Query Direta no Banco</h2>";
    echo "<pre>";
    echo "SQL: " . $sql . "\n\n";
    echo "Resultados: " . count($result) . "\n\n";
    print_r($result);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Erro na Query</h2>";
    echo "<pre>";
    echo "Erro: " . $e->getMessage() . "\n";
    echo "</pre>";
}
?>
