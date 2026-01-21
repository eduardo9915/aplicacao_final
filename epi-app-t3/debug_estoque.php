<?php
require_once 'utils/Conexao.php';

// Capturar saída para arquivo
ob_start();

try {
    $con = Conexao::pegarConexao();
    
    echo "=== VERIFICAÇÃO DE ESTOQUE ===\n";
    echo "Data: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Verificar estrutura da tabela estoque
    echo "--- Estrutura da tabela ESTOQUE ---\n";
    $stmt = $con->query("DESCRIBE estoque");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    
    echo "\n=== DADOS EXISTENTES ===\n";
    
    // Verificar produtos
    echo "\n--- Produtos cadastrados ---\n";
    $stmt = $con->query("SELECT id_produto, nome_produto FROM produto ORDER BY nome_produto");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total de produtos: " . count($produtos) . "\n";
    foreach ($produtos as $p) {
        echo "ID: " . $p['id_produto'] . " - Nome: " . $p['nome_produto'] . "\n";
    }
    
    // Verificar estoque - mostrar todas as colunas
    echo "\n--- Registros de estoque (todas as colunas) ---\n";
    $stmt = $con->query("SELECT * FROM estoque");
    $estoques = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total de registros em estoque: " . count($estoques) . "\n";
    
    if (count($estoques) > 0) {
        // Mostrar nome das colunas
        echo "Colunas encontradas: " . implode(", ", array_keys($estoques[0])) . "\n\n";
        
        foreach ($estoques as $e) {
            echo "Registro completo: ";
            foreach ($e as $key => $value) {
                echo "$key: $value | ";
            }
            echo "\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERRO: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

$output = ob_get_clean();

// Salvar em arquivo
file_put_contents('debug_estoque_log.txt', $output);

// Exibir também na tela
echo "<pre>";
echo htmlspecialchars($output);
echo "</pre>";
?>
