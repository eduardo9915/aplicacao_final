<?php
require_once __DIR__ . "/Pedido.php";
class Saida {
    private int $idSaida;
    private string $dataHora;
    private string $tipo;
    private ?string $observacao;
    private ?Pedido $pedidoId;
    private array $itens;

    public function __construct(
        string $dataHora,
        string $tipo,
        ?Pedido $pedidoId = null,
        ?string $observacao = null,
        int $idSaida = 0
    ) {
        $this->idSaida = $idSaida;
        $this->dataHora = $dataHora;
        $this->tipo = $tipo;
        $this->pedidoId = $pedidoId;
        $this->observacao = $observacao;
        $this->itens = [];
    }

    public function getId(): int {
        return $this->idSaida;
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
     * Get the value of pedidoId
     */ 
    public function getPedidoId()
    {
        return $this->pedidoId;
    }

    /**
     * Get the value of itens
     */ 
    public function getItens()
    {
        return $this->itens;
    }

    /**
     * Set the value of itens
     */ 
    public function setItens(array $itens)
    {
        $this->itens = $itens;
    }
}