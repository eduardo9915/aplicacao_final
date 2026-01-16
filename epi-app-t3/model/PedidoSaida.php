<?php
    require_once __DIR__ . "/Saida.php";
    require_once __DIR__ . "/Produto.php";

class ProdutoSaida {
    private int $idProdutoSaida;
    private ?Saida $idSaida;
    private int $quantidadeProdutoSaida;
    private string $observacaoProdutoSaida;
    private ?Produto $produtoProdutoSaida;
    private ?Saida $saidaProdutoSaida;

    public function __construct(
        $produtoProdutoSaida,
        $saidaProdutoSaida,
        $quantidadeProdutoSaida,
        $observacaoProdutoSaida = "",
        $idProdutoSaida = 0
    ) {
        $this->idProdutoSaida = $idProdutoSaida;
        $this->idSaida = $saidaProdutoSaida;
        $this->quantidadeProdutoSaida = $quantidadeProdutoSaida;
        $this->observacaoProdutoSaida = $observacaoProdutoSaida;
        $this->produtoProdutoSaida = $produtoProdutoSaida;
        $this->saidaProdutoSaida = $saidaProdutoSaida;
    }

    public function getId() { return $this->idProdutoSaida; }
    public function getIdSaida() { return $this->idSaida; }
    public function getQuantidade() { return $this->quantidadeProdutoSaida; }
    public function getObservacao() { return $this->observacaoProdutoSaida; }
    public function getProduto() { return $this->produtoProdutoSaida; }
    public function getSaidaProduto() { return $this->saidaProdutoSaida; }
}
?>