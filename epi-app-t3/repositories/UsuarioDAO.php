<?php
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../model/Usuario.php";
require_once __DIR__ . "/../model/Setor.php";

class UsuarioDAO extends Conexao {

    private ?PDO $conexao;

    public function __construct() {
        $this->conexao = $this::pegarConexao();
    }

    /**
     * Verifica se CPF já existe
     */
    public function cpfExiste(string $cpf, int $excluirId = 0): bool {
        $sql = "SELECT COUNT(*) FROM usuario WHERE cpf_usuario = :cpf";
        if ($excluirId > 0) {
            $sql .= " AND id_usuario != :id";
        }
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":cpf", $cpf, PDO::PARAM_STR);
        if ($excluirId > 0) {
            $stmt->bindValue(":id", $excluirId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se email já existe
     */
    public function emailExiste(string $email, int $excluirId = 0): bool {
        $sql = "SELECT COUNT(*) FROM usuario WHERE email_usuario = :email";
        if ($excluirId > 0) {
            $sql .= " AND id_usuario != :id";
        }
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        if ($excluirId > 0) {
            $stmt->bindValue(":id", $excluirId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Verifica se matrícula já existe
     */
    public function matriculaExiste(string $matricula, int $excluirId = 0): bool {
        $sql = "SELECT COUNT(*) FROM usuario WHERE matricula_usuario = :matricula";
        if ($excluirId > 0) {
            $sql .= " AND id_usuario != :id";
        }
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":matricula", $matricula, PDO::PARAM_STR);
        if ($excluirId > 0) {
            $stmt->bindValue(":id", $excluirId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn() > 0;
    }

    public function inserir(Usuario $usuario, int $idSetor): bool {
        try {
            // Validar unicidade antes de inserir
            if ($this->cpfExiste($usuario->getCpf())) {
                throw new Exception("CPF já cadastrado no sistema.");
            }
            if ($this->emailExiste($usuario->getEmail())) {
                throw new Exception("Email já cadastrado no sistema.");
            }
            if ($this->matriculaExiste($usuario->getMatricula())) {
                throw new Exception("Matrícula já cadastrada no sistema.");
            }

            $sql = "INSERT INTO usuario (
                    nome_usuario,
                    sobrenome_usuario,
                    matricula_usuario,
                    telefone_usuario,
                    cargo_usuario,
                    data_admissao_usuario,
                    data_demissao_usuario,
                    cpf_usuario,
                    senha_usuario,
                    email_usuario,
                    setor_usuario
                ) 
                VALUES (
                    :nome,
                    :sobrenome,
                    :matricula,
                    :telefone,
                    :cargo,
                    :data_admissao,
                    :data_demissao,
                    :cpf,
                    :senha,
                    :email,
                    :setor
                )";
            $stmt = $this->conexao->prepare($sql);                 
            $stmt->bindValue(":nome", $usuario->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(":sobrenome", $usuario->getSobrenome(), PDO::PARAM_STR);
            $stmt->bindValue(":matricula", $usuario->getMatricula(), PDO::PARAM_STR);
            $stmt->bindValue(":telefone", $usuario->getTelefone(), PDO::PARAM_STR);
            $stmt->bindValue(":cargo", $usuario->getCargo(), PDO::PARAM_STR);
            $stmt->bindValue(":data_admissao", $usuario->getDataAdmissao(), PDO::PARAM_STR);
            //$dataDemissao = $usuario->getDataDemissao();
            $stmt->bindValue(":data_demissao", null, PDO::PARAM_NULL);
            $stmt->bindValue(":cpf", $usuario->getCpf(), PDO::PARAM_STR);
            $stmt->bindValue(":senha", $usuario->getSenha(), PDO::PARAM_STR);
            $stmt->bindValue(":email", $usuario->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(":setor", $idSetor, PDO::PARAM_INT);            
            $resposta = $stmt->execute();  
            return $resposta;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao cadastrar usuário: " . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function atualizar(Usuario $usuario, int $idSetor): bool {
        try {
            // Validar unicidade antes de atualizar (excluindo o próprio usuário)
            if ($this->cpfExiste($usuario->getCpf(), $usuario->getId())) {
                throw new Exception("CPF já cadastrado no sistema.");
            }
            if ($this->emailExiste($usuario->getEmail(), $usuario->getId())) {
                throw new Exception("Email já cadastrado no sistema.");
            }
            if ($this->matriculaExiste($usuario->getMatricula(), $usuario->getId())) {
                throw new Exception("Matrícula já cadastrada no sistema.");
            }

            $sql = "UPDATE usuario SET nome_usuario = :nome,
            sobrenome_usuario = :sobrenome,
            matricula_usuario = :matricula,
            telefone_usuario = :telefone,
            cargo_usuario = :cargo,
            data_admissao_usuario = :data_admissao,
            data_demissao_usuario = :data_demissao,
            cpf_usuario = :cpf,
            senha_usuario = :senha,
            email_usuario = :email,
            setor_usuario = :setor_id
            WHERE id_usuario = :id";
            $stmt = $this->conexao->prepare($sql);

            $stmt->bindValue(":nome", $usuario->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(":sobrenome", $usuario->getSobrenome(), PDO::PARAM_STR);
            $stmt->bindValue(":matricula", $usuario->getMatricula(), PDO::PARAM_STR);
            $stmt->bindValue(":telefone", $usuario->getTelefone(), PDO::PARAM_STR);
            $stmt->bindValue(":cargo", $usuario->getCargo(), PDO::PARAM_STR);
            $stmt->bindValue(":data_admissao", $usuario->getDataAdmissao(), PDO::PARAM_STR);
            $dataDemissao = $usuario->getDataDemissao();
            $stmt->bindValue(":data_demissao", $dataDemissao !== null ? $dataDemissao : null, $dataDemissao !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(":cpf", $usuario->getCpf(), PDO::PARAM_STR);
            $stmt->bindValue(":senha", $usuario->getSenha(), PDO::PARAM_STR);
            $stmt->bindValue(":email", $usuario->getEmail(), PDO::PARAM_STR);
            $stmt->bindValue(":setor_id", $idSetor, PDO::PARAM_INT);
            $stmt->bindValue(":id", $usuario->getId(), PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new Exception("Erro ao atualizar usuário: " . $e->getMessage());
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function excluir(int $idUsuario): bool {
        try {
            $sql = "DELETE FROM usuario WHERE id_usuario = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $idUsuario, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function buscarPorId(int $idUsuario): ?Usuario {
        $sql = "SELECT u.*, s.* FROM usuario u LEFT JOIN setor s ON s.id_setor = u.setor_usuario WHERE u.id_usuario = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }
        $setor = isset($dados['id_setor']) ? new Setor($dados['nome_setor'], $dados['id_setor']) : null;
        $usuario = new Usuario(
            $dados['nome_usuario'],
            $dados['sobrenome_usuario'],
            $dados['matricula_usuario'],
            $dados['telefone_usuario'],
            $dados['cargo_usuario'],
            $dados['data_admissao_usuario'],
            $dados['cpf_usuario'],
            $dados['senha_usuario'],
            $dados['email_usuario'] ?? null,
            $setor,
            $dados['data_demissao_usuario'] ?? null,
            (int) $dados['id_usuario']
        );

        return $usuario;
    }

    public function buscarPorMatricula(string $matricula): ?Usuario {
        try {
            $sql = "SELECT u.*, s.* FROM usuario u LEFT JOIN setor s ON s.id_setor = u.setor_usuario WHERE u.matricula_usuario = :matricula LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":matricula", $matricula, PDO::PARAM_STR);
            $stmt->execute();

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$dados) return null;

            $setor = isset($dados['id_setor']) ? new Setor($dados['nome_setor'], $dados['id_setor']) : null;

            return new Usuario(
                $dados['nome_usuario'],
                $dados['sobrenome_usuario'],
                $dados['matricula_usuario'],
                $dados['telefone_usuario'],
                $dados['cargo_usuario'],
                $dados['data_admissao_usuario'],
                $dados['cpf_usuario'],
                $dados['senha_usuario'],
                $dados['email_usuario'] ?? $dados['email'],
                $setor,
                $dados['data_demissao_usuario'] ?? null,
                (int) $dados['id_usuario']
            );
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function buscarPorCpfMatricula(string $cpf, string $matricula): ?Usuario {
        try {
            $sql = "SELECT u.*, s.* FROM usuario u LEFT JOIN setor s ON s.id_setor = u.setor_usuario 
                     WHERE u.cpf_usuario = :cpf AND u.matricula_usuario = :matricula LIMIT 1";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":cpf", $cpf, PDO::PARAM_STR);
            $stmt->bindValue(":matricula", $matricula, PDO::PARAM_STR);
            $stmt->execute();

            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $setor = new Setor(
                    $row['nome_setor'],
                    $row['id_setor']
                );

                return new Usuario(
                    $row['nome_usuario'],
                    $row['sobrenome_usuario'],
                    $row['matricula_usuario'],
                    $row['telefone_usuario'],
                    $row['cargo_usuario'],
                    $row['data_admissao_usuario'],
                    $row['cpf_usuario'],
                    $row['senha_usuario'],
                    $row['email_usuario'] ?? $row['email'],
                    $setor,
                    $row['data_demissao_usuario'] ?? null,
                    (int) $row['id_usuario']
                );
            }

            return null;
        } catch (PDOException $e) {
            error_log("UsuarioDAO::buscarPorCpfMatricula - " . $e->getMessage());
            return null;
        }
    }

    public function listarTodos(): array {
        try {
            $sql = "SELECT * FROM usuario INNER JOIN setor ON id_setor = setor_usuario;";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            $resposta = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $resposta;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

}
