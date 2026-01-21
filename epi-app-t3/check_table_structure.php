<?php
require_once __DIR__ . "/utils/Conexao.php";

try {
    $conexao = new Conexao();
    $pdo = $conexao::pegarConexao();
    
    echo "<h2>Estrutura da Tabela: estoque</h2>";
    $stmt = $pdo->query('DESCRIBE estoque');
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
    
    echo "<h2>Estrutura da Tabela: produto</h2>";
    $stmt = $pdo->query('DESCRIBE produto');
    echo "<pre>";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<h2>Erro</h2>";
    echo "<pre>";
    echo "Erro: " . $e->getMessage() . "\n";
    echo "</pre>";
}
?>
