<?php
    require_once __DIR__ . "/Pedido.php";
    require_once __DIR__ . "/Produto.php";

class ProdutoPedido {
    private int $idProdutoPedido;
    private int $quantidadeProdutoPedido;
    private string $observacaoProdutoPedido;
    private ?Pedido $pedidoProdutoPedido;
    private ?Produto $produtoProdutoPedido;

    public function __construct(
        $produtoProdutoPedido = null,
        $pedidoProdutoPedido = null,
        $quantidadeProdutoPedido,
        $observacaoProdutoPedido = "",
        $idProdutoPedido = 0
    ) {
        $this->idProdutoPedido = $idProdutoPedido;
        $this->produtoProdutoPedido = $produtoProdutoPedido;
        $this->pedidoProdutoPedido = $pedidoProdutoPedido;
        $this->quantidadeProdutoPedido = $quantidadeProdutoPedido;
        $this->observacaoProdutoPedido = $observacaoProdutoPedido;
    }

    public function getId() { return $this->idProdutoPedido; }
    public function getProduto() { return $this->produtoProdutoPedido; }
    public function getPedido() { return $this->pedidoProdutoPedido; }
    public function getQuantidade() { return $this->quantidadeProdutoPedido; }
    public function getObservacao() { return $this->observacaoProdutoPedido; }
}
?>