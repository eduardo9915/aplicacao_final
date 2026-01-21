<?php
require_once __DIR__ . "/ProdutoEntrada.php";
class Entrada {
    private int $idEntrada;
    private string $dataHora;
    private string $tipo;
    private string $observacao;
    private array $itens = []; // array of ProdutoEntrada objects

    public function __construct(
        string $dataHora = "",
        string $tipo = "",
        string $observacao = "",
        int $idEntrada = 0
    ) {
        $this->idEntrada = $idEntrada;
        $this->dataHora = $dataHora;
        $this->tipo = $tipo;
        $this->observacao = $observacao;
    }

    public function getId(): int {
        return $this->idEntrada;
    }

    /**
     * Get the value of dataHora
     */ 
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * Get the value of tipo
     */ 
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Get the value of observacao
     */ 
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * Get itens (produtos relacionados a esta entrada)
     */
    public function getItens(): array
    {
        return $this->itens;
    }

    public function setItens(array $itens): void
    {
        $this->itens = $itens;
    }

    public function addItem(ProdutoEntrada $item): void
    {
        $this->itens[] = $item;
    }
}