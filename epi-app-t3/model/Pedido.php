<?php
require_once __DIR__ . "/Usuario.php";
class Pedido {
    private ?int $idPedido;
    private ?string $dataHora;
    private ?string $status;
    private ?string $tipo;
    private ?Usuario $usuario;
    private ?string $observacao;
    private ?string $dataRetirada;
    private ?string $dataDevolucao;

    public function __construct(
        string $dataHora = null,
        string $status = "PENDENTE",
        string $tipo = null,
        ?Usuario $usuario = null,
        ?string $observacao = null,
        ?string $dataRetirada = null,
        ?string $dataDevolucao = null,
        int $idPedido = 0
    ) {
        $this->idPedido = $idPedido;
        $this->dataHora = $dataHora;
        $this->status = $status;
        $this->tipo = $tipo;
        $this->usuario = $usuario;
        $this->observacao = $observacao;
        $this->dataRetirada = $dataRetirada;
        $this->dataDevolucao = $dataDevolucao;
    }

    public function getId(): int {
        return $this->idPedido;
    }

    /**
     * Get the value of dataHora
     */ 
    public function getDataHora()
    {
        return $this->dataHora;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get the value of tipo
     */ 
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Get the value of solicitanteId
     */ 
    public function getSolicitanteId()
    {
        return $this->usuario;
    }

    /**
     * Alias para compatibilidade com views que chamam getUsuario()
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Get the value of observacao
     */ 
    public function getObservacao()
    {
        return $this->observacao;
    }

    /**
     * Get the value of dataRetirada
     */ 
    public function getDataRetirada()
    {
        return $this->dataRetirada;
    }

    /**
     * Get the value of dataDevolucao
     */ 
    public function getDataDevolucao()
    {
        return $this->dataDevolucao;
    }

    // Itens (lista de ProdutoPedido)
    private array $itens = [];

    public function getItens(): array
    {
        return $this->itens;
    }

    public function setItens(array $itens): void
    {
        $this->itens = $itens;
    }

    public function addItem($item): void
    {
        $this->itens[] = $item;
    }
}
