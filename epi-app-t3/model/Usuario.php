<?php
require_once __DIR__ . "/Setor.php";
class Usuario {
    private int $idUsuario;
    private string $nome;
    private string $sobrenome;
    private string $matricula;
    private string $telefone;
    private string $cargo;
    private string $dataAdmissao;
    private ?string $dataDemissao;
    private string $cpf;
    private string $senha;
    private string $email;
    private ?Setor $setor;
    private bool $admUsuario;

    public function __construct(
        string $nome,
        string $sobrenome,
        string $matricula,
        string $telefone,
        string $cargo,
        string $dataAdmissao,
        string $cpf,
        string $senha,
        string $email,
        ?Setor $setor = null,
        ?string $dataDemissao = null,
        int $idUsuario = 0,
        bool $admUsuario = false
    ) {
        $this->idUsuario = $idUsuario;
        $this->nome = $nome;
        $this->sobrenome = $sobrenome;
        $this->matricula = $matricula;
        $this->telefone = $telefone;
        $this->cargo = $cargo;
        $this->dataAdmissao = $dataAdmissao;
        $this->dataDemissao = $dataDemissao;
        $this->cpf = $cpf;
        $this->senha = $senha;
        $this->email = $email;
        $this->setor = $setor;
        $this->admUsuario = $admUsuario;
    }

    public function getId(): int {
        return $this->idUsuario;
    }

    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Get the value of sobrenome
     */ 
    public function getSobrenome()
    {
        return $this->sobrenome;
    }

    /**
     * Get the value of matricula
     */ 
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Get the value of telefone
     */ 
    public function getTelefone()
    {
        return $this->telefone;
    }

    /**
     * Get the value of cargo
     */ 
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Get the value of dataAdmissao
     */ 
    public function getDataAdmissao()
    {
        return $this->dataAdmissao;
    }

    /**
     * Get the value of dataDemissao
     */ 
    public function getDataDemissao()
    {
        return $this->dataDemissao;
    }

    /**
     * Get the value of cpf
     */ 
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * Get the value of senha
     */ 
    public function getSenha()
    {
        return $this->senha;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get the value of setorId
     */ 
    public function getSetor()
    {
        return $this->setor;
    }

    /**
     * Set the value of senha
     */ 
    public function setSenha(string $senha)
    {
        $this->senha = $senha;
    }

    /**
     * Get the value of admUsuario
     */ 
    public function getAdmUsuario(): bool
    {
        return $this->admUsuario;
    }

    /**
     * Set the value of admUsuario
     */ 
    public function setAdmUsuario(bool $admUsuario)
    {
        $this->admUsuario = $admUsuario;
    }
}
