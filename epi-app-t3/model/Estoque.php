<?php
require_once __DIR__ . "/Produto.php";
class Estoque {
    private int $idEstoque;
    private ?Produto $produto;
    private int $quantidade;
    private string $dataEntrada;
    private ?string $dataSaida;

    public function __construct(
        ?Produto $produto = null,
        int $quantidade,
        string $dataEntrada,
        ?string $dataSaida = null,
        int $idEstoque = 0
    ) {
        $this->idEstoque = $idEstoque;
        $this->produto = $produto;
        $this->quantidade = $quantidade;
        $this->dataEntrada = $dataEntrada;
        $this->dataSaida = $dataSaida;
    }

    public function getId(): int {
        return $this->idEstoque;
    }

    public function getProdutoId(): Produto {
        return $this->produto;
    }

    public function getQuantidade(): int {
        return $this->quantidade;
    }

    public function getDataEntrada(): string {
        return $this->dataEntrada;
    }

    public function getDataSaida(): ?string {
        return $this->dataSaida;
    }
}