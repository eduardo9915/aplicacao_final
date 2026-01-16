<?php
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../model/Estoque.php";
require_once __DIR__ . "/../model/Produto.php";

class EstoqueDAO extends Conexao {
    private ?PDO $conexao;

    public function __construct() {
        $this->conexao = $this::pegarConexao();
    }

    public function inserir(Estoque $estoque): bool {
        try {
            $sql = "INSERT INTO estoque (
                        id_produto,
                        quantidade_estoque,
                        data_entrada_estoque,
                        data_saida_estoque
                    ) VALUES (
                        :id_produto,
                        :quantidade,
                        :data_entrada,
                        :data_saida
                    )";

            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                ':id_produto' => $estoque->getProdutoId()->getId(),
                ':quantidade' => $estoque->getQuantidade(),
                ':data_entrada' => $estoque->getDataEntrada(),
                ':data_saida' => $estoque->getDataSaida()
            ]);
        } catch (PDOException $e) {
            error_log('EstoqueDAO::inserir PDOException: ' . $e->getMessage());
            return false;
        }
    }

    public function atualizar(Estoque $estoque): bool {
        try {
            $sql = "UPDATE estoque SET
                        id_produto = :id_produto,
                        quantidade_estoque = :quantidade,
                        data_entrada_estoque = :data_entrada,
                        data_saida_estoque = :data_saida
                    WHERE id_estoque = :id";

            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([
                ':id_produto' => $estoque->getProdutoId()->getId(),
                ':quantidade' => $estoque->getQuantidade(),
                ':data_entrada' => $estoque->getDataEntrada(),
                ':data_saida' => $estoque->getDataSaida(),
                ':id' => $estoque->getId()
            ]);
        } catch (PDOException $e) {
            error_log('EstoqueDAO::atualizar PDOException: ' . $e->getMessage());
            return false;
        }
    }

    public function excluir(int $idEstoque): bool {
        try {
            $sql = "DELETE FROM estoque WHERE id_estoque = :id";
            $stmt = $this->conexao->prepare($sql);
            return $stmt->execute([':id' => $idEstoque]);
        } catch (PDOException $e) {
            error_log('EstoqueDAO::excluir PDOException: ' . $e->getMessage());
            return false;
        }
    }

    public function buscarPorId(int $idEstoque): ?Estoque {
        try {
            $sql = "SELECT e.*, p.* FROM estoque e JOIN produto p ON p.id_produto = e.id_produto WHERE e.id_estoque = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([':id' => $idEstoque]);

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$dados) return null;

            $produto = new Produto(
                $dados['nome_produto'],
                $dados['discriminacao_produto'],
                $dados['tipo_produto'],
                $dados['marca_produto'],
                $dados['data_registro_produto'],
                $dados['validade_produto'],
                $dados['ca_produto'],
                $dados['ca_data_validade_produto'],
                $dados['id_produto']
            );

            return new Estoque(
                $produto,
                (int)$dados['quantidade_estoque'],
                $dados['data_entrada_estoque'],
                $dados['data_saida_estoque'] ?? null,
                (int)$dados['id_estoque']
            );
        } catch (PDOException $e) {
            error_log('EstoqueDAO::buscarPorId PDOException: ' . $e->getMessage());
            return null;
        }
    }

    public function listarTodos(): array {
        try {
            $sql = "SELECT e.*, p.* FROM estoque e JOIN produto p ON p.id_produto = e.produto_estoque ORDER BY e.data_entrada_estoque DESC";
            $stmt = $this->conexao->query($sql);

            $lista = [];
            while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $produto = new Produto(
                    $dados['nome_produto'],
                    $dados['discriminacao_produto'],
                    $dados['tipo_produto'],
                    $dados['marca_produto'],
                    $dados['data_registro_produto'],
                    $dados['validade_produto'],
                    $dados['ca_produto'],
                    $dados['ca_data_validade_produto'],
                    $dados['id_produto']
                );

                $lista[] = new Estoque(
                    $produto,
                    (int)$dados['quantidade_produto_estoque'],
                    $dados['data_entrada_estoque'],
                    $dados['data_saida_estoque'],
                    (int)$dados['id_estoque']
                );
            }            
            return $lista;
        } catch (PDOException $e) {
            error_log('EstoqueDAO::listarTodos PDOException: ' . $e->getMessage());
            throw new Exception("Debug");
            return [];
        }
    }
}
