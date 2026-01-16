<?php
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../model/Produto.php";

class ProdutoDAO extends Conexao {

    private ?PDO $conexao;

    public function __construct() {
        $this->conexao = $this::pegarConexao();
    }

    /**
     * Inserir produto
     */
    public function inserir(Produto $produto): bool {
        try {
            $sql = "INSERT INTO produto (
                        nome_produto,
                        discriminacao_produto,
                        tipo_produto,
                        marca_produto,
                        validade_produto,
                        ca_produto,
                        ca_data_validade_produto,
                        data_registro_produto
                    ) VALUES (
                        :nome,
                        :discriminacao,
                        :tipo,
                        :marca,
                        :validade,
                        :ca,
                        :ca_validade,
                        :data_registro
                    )";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":nome", $produto->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(":discriminacao", $produto->getDiscriminacao(), PDO::PARAM_STR);
            $stmt->bindValue(":tipo", (int) $produto->getTipo(), PDO::PARAM_INT);
            $stmt->bindValue(":marca", $produto->getMarca(), PDO::PARAM_STR);            
            $stmt->bindValue(":validade", $produto->getValidade(), PDO::PARAM_STR);            
            $stmt->bindValue(":ca", $produto->getCa(), PDO::PARAM_STR);            
            $stmt->bindValue(":ca_validade", $produto->getCaValidade(), PDO::PARAM_STR);
            $stmt->bindValue(":data_registro", $produto->getDataRegistro(), PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('ProdutoDAO::inserir PDOException: ' . $e->getMessage());
            if (isset($stmt)) {
                error_log('ProdutoDAO::inserir statement errorInfo: ' . json_encode($stmt->errorInfo()));
            }
            return false;
        }
    }

    /**
     * Atualizar produto
     */
    public function atualizar(Produto $produto): bool {
        try {
            $sql = "UPDATE produto SET
                        nome_produto = :nome,
                        discriminacao_produto = :discriminacao,
                        tipo_produto = :tipo,
                        marca_produto = :marca,
                        validade_produto = :validade,
                        ca_produto = :ca,
                        ca_data_validade_produto = :ca_validade,
                        data_registro_produto = :data_registro
                    WHERE id_produto = :id";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":nome", $produto->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(":discriminacao", $produto->getDiscriminacao(), PDO::PARAM_STR);
            $stmt->bindValue(":tipo", $produto->getTipo(), PDO::PARAM_STR);
            $stmt->bindValue(":marca", $produto->getMarca(), PDO::PARAM_STR);            
            $stmt->bindValue(":validade", $produto->getValidade(), PDO::PARAM_STR);            
            $stmt->bindValue(":ca", $produto->getCa(), PDO::PARAM_STR);            
            $stmt->bindValue(":ca_validade", $produto->getCaValidade(), PDO::PARAM_STR);
            $stmt->bindValue(":data_registro", $produto->getDataRegistro(), PDO::PARAM_STR);
            $stmt->bindValue(":id", $produto->getId(), PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('ProdutoDAO::atualizar PDOException: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Excluir produto
     */
    public function excluir(int $idProduto): bool {
        try {
            $sql = "DELETE FROM produto WHERE id_produto = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $idProduto, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('ProdutoDAO::excluir PDOException: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Buscar produto por ID
     */
    public function buscarPorId(int $idProduto): ?Produto {
        try {
            $sql = "SELECT * FROM produto WHERE id_produto = :id";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id", $idProduto, PDO::PARAM_INT);
            $stmt->execute();

            $dados = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$dados) {
                return null;
            }

            return new Produto(
                $dados['nome_produto'],
                $dados['discriminacao_produto'],
                $dados['tipo_produto'],
                $dados['marca_produto'],
                $dados['data_registro_produto'],
                $dados['validade_produto'],
                $dados['ca_produto'],
                $dados['ca_validade_produto'],
                $dados['id_produto']
            );
        } catch (PDOException $e) {
            error_log('ProdutoDAO::buscarPorId PDOException: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Listar todos os produtos
     */
    public function listarTodos(): array {
        try {
            $sql = "SELECT * FROM produto ORDER BY nome_produto";
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $produtos;
        } catch (PDOException $e) {
            error_log('ProdutoDAO::listarTodos PDOException: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Buscar quantidade em estoque de um produto
     */
    public function buscarQuantidadeEstoque(int $idProduto): int {
        try {
            $sql = "SELECT COALESCE(SUM(quantidade_produto_estoque), 0) as quantidade 
                    FROM estoque 
                    WHERE produto_estoque = :id_produto";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindValue(":id_produto", $idProduto, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $resultado['quantidade'];
        } catch (PDOException $e) {
            error_log('ProdutoDAO::buscarQuantidadeEstoque PDOException: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Listar todos os produtos com quantidade em estoque
     */
    public function listarTodosComEstoque(): array {
        try {
            // Tentar primeiro com a estrutura correta
            $sql = "SELECT p.*, COALESCE(SUM(e.quantidade_produto_estoque), 0) as quantidade_estoque 
                    FROM produto p 
                    LEFT JOIN estoque e ON p.id_produto = e.produto_estoque 
                    GROUP BY p.id_produto 
                    ORDER BY p.nome_produto";
            
            error_log('ProdutoDAO::listarTodosComEstoque - Tentando consulta com quantidade_produto_estoque');
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->execute();
            $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($produtos) > 0) {
                error_log('ProdutoDAO::listarTodosComEstoque - Sucesso com quantidade_produto_estoque: ' . count($produtos) . ' produtos');
                return $produtos;
            }
            
            // Se não funcionar, tentar com id_produto
            $sql2 = "SELECT p.*, COALESCE(SUM(e.quantidade_produto_estoque), 0) as quantidade_estoque 
                     FROM produto p 
                     LEFT JOIN estoque e ON p.id_produto = e.id_produto 
                     GROUP BY p.id_produto 
                     ORDER BY p.nome_produto";
            
            error_log('ProdutoDAO::listarTodosComEstoque - Tentando consulta com id_produto');
            
            $stmt2 = $this->conexao->prepare($sql2);
            $stmt2->execute();
            $produtos2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($produtos2) > 0) {
                error_log('ProdutoDAO::listarTodosComEstoque - Sucesso com id_produto: ' . count($produtos2) . ' produtos');
                return $produtos2;
            }
            
            // Se nenhuma funcionar, listar produtos sem estoque
            error_log('ProdutoDAO::listarTodosComEstoque - Nenhuma consulta funcionou, usando listarTodos()');
            return $this->listarTodos();
            
        } catch (PDOException $e) {
            error_log('ProdutoDAO::listarTodosComEstoque PDOException: ' . $e->getMessage());
            return $this->listarTodos();
        }
    }
}