<?php
    require_once __DIR__ . "/Produto.php";
    require_once __DIR__ . "/Entrada.php";

class ProdutoEntrada {
    private int $idProdutoEntrada;
    private int $quantidadeEntrada;
    private string $observacaoProdutoEntrada;
    private ?Produto $produtoProdutoEntrada;
    private ?Entrada $entradaProdutoEntrada;

    public function __construct(
        $produtoProdutoEntrada = null,
        $entradaProdutoEntrada = null,
        $quantidadeEntrada = 0,
        $observacaoProdutoEntrada = "",
        $idEntrada = 0
    ) {
        $this->idProdutoEntrada = $idEntrada;        
        $this->quantidadeEntrada = $quantidadeEntrada;
        $this->observacaoProdutoEntrada = $observacaoProdutoEntrada;
        $this->produtoProdutoEntrada = $produtoProdutoEntrada;
        $this->entradaProdutoEntrada = $entradaProdutoEntrada;
    }

    public function getId() { return $this->idProdutoEntrada; }    
    public function getQuantidade() { return $this->quantidadeEntrada; }
    public function getObservacao() { return $this->observacaoProdutoEntrada; }
    public function getProduto() { return $this->produtoProdutoEntrada; }
    public function getEntradaProduto() { return $this->entradaProdutoEntrada; }
}
?>