<?php
class Produto {
    private ?int $idProduto;
    private ?string $nome;
    private ?string $discriminacao;
    private ?string $tipo;
    private ?string $marca;
    private ?string $validade;
    private ?string $ca;
    private ?string $caValidade;
    private ?string $dataRegistro;

    public function __construct(
        string $nome = "",
        string $discriminacao = "",
        string $tipo = "",
        string $marca = "",
        string $dataRegistro = "",
        string $validade = "",
        string $ca = "",
        string $caValidade = "",
        int $idProduto = 0
    ) {
        $this->idProduto = $idProduto;
        $this->nome = $nome;
        $this->discriminacao = $discriminacao;
        $this->tipo = $tipo;
        $this->marca = $marca;
        $this->validade = $validade;
        $this->ca = $ca;
        $this->caValidade = $caValidade;
        $this->dataRegistro = $dataRegistro;
    }

    public function getId(): int {
        return $this->idProduto;
    }

    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Get the value of discriminacao
     */ 
    public function getDiscriminacao()
    {
        return $this->discriminacao;
    }

    /**
     * Get the value of tipo
     */ 
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Get the value of marca
     */ 
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Get the value of validade
     */ 
    public function getValidade()
    {
        return $this->validade;
    }

    /**
     * Get the value of ca
     */ 
    public function getCa()
    {
        return $this->ca;
    }

    /**
     * Get the value of caValidade
     */ 
    public function getCaValidade()
    {
        return $this->caValidade;
    }

    /**
     * Get the value of dataRegistro
     */ 
    public function getDataRegistro()
    {
        return $this->dataRegistro;
    }
}