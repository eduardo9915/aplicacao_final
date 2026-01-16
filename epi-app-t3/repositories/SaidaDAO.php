<?php
require_once __DIR__ . "/../model/Saida.php";
require_once __DIR__ . "/../model/Produto.php";
require_once __DIR__ . "/../model/Usuario.php";
require_once __DIR__ . "/../model/Pedido.php";
require_once __DIR__ . "/../utils/Conexao.php";

class SaidaDAO extends Conexao {

    private ?PDO $conexao;

    public function __construct() {
        $this->conexao = $this::pegarConexao();
    }

    /**
     * Inserir saida (Apenas o cabeçalho)
     */
    public function inserir(Saida $saida): bool {
        $sql = "INSERT INTO saida (
                    data_hora_saida,
                    tipo_saida,
                    observacao_saida,
                    pedido_saida
                ) VALUES (
                    :data_hora,
                    :tipo,
                    :observacao,
                    :pedido
                )";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':data_hora'  => $saida->getDataHora(),
            ':tipo'       => $saida->getTipo(),
            ':observacao' => $saida->getObservacao(),
            ':pedido'     => $saida->getPedidoId() ? $saida->getPedidoId()->getId() : null
        ]);
    }

    /**
     * Inserir saida com múltiplos produtos e atualizar estoque (Transactional)
     */
    public function inserirComProdutos(Saida $saida, array $produtos): bool {
        try {
            $this->conexao->beginTransaction();

            // 1. Inserir na tabela 'saida'
            $sql = "INSERT INTO saida (
                        data_hora_saida,
                        tipo_saida,
                        observacao_saida,
                        pedido_saida
                    ) VALUES (
                        :data_hora,
                        :tipo,
                        :observacao,
                        :pedido
                    )";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([
                ':data_hora'  => $saida->getDataHora(),
                ':tipo'       => $saida->getTipo(),
                ':observacao' => $saida->getObservacao(),
                ':pedido'     => $saida->getPedidoId() ? $saida->getPedidoId()->getId() : null
            ]);

            $idSaida = (int) $this->conexao->lastInsertId();

            if (!empty($produtos)) {
                // 2. Query para tabela 'produto_saida'
                $sqlPs = "INSERT INTO produto_saida (
                            produto_produto_saida,
                            saida_produto_saida,
                            quantidade_produto_saida,
                            observacao_produto_saida
                        ) VALUES (
                            :id_produto,
                            :id_saida,
                            :quantidade,
                            :observacao
                        )";
                $stmtPs = $this->conexao->prepare($sqlPs);

                // 3. Preparar queries de ESTOQUE
                // Busca estoque pelo produto
                $sqlSelectEst = "SELECT id_estoque, quantidade_produto_estoque
                                 FROM estoque
                                 WHERE produto_estoque = :id_produto LIMIT 1";
                $stmtSelectEst = $this->conexao->prepare($sqlSelectEst);

                // Atualiza quantidade (subtrai)
                $sqlUpdateEst = "UPDATE estoque
                                 SET quantidade_produto_estoque = quantidade_produto_estoque - :quantidade,
                                     data_saida_estoque = :data_saida
                                 WHERE id_estoque = :id_estoque";
                $stmtUpdateEst = $this->conexao->prepare($sqlUpdateEst);

                foreach ($produtos as $p) {
                    $idProd = (int) $p['idProduto'];
                    $quant = (int) $p['quantidade'];
                    $observacaoProd = $p['observacao'] ?? '';

                    // Executa insert na tabela associativa produto_saida
                    $stmtPs->execute([
                        ':id_produto' => $idProd,
                        ':id_saida' => $idSaida,
                        ':quantidade' => $quant,
                        ':observacao' => $observacaoProd
                    ]);

                    // Logica de Atualização de Estoque
                    $stmtSelectEst->execute([':id_produto' => $idProd]);
                    $estRow = $stmtSelectEst->fetch(PDO::FETCH_ASSOC);

                    if ($estRow) {
                        $available = (int) $estRow['quantidade_produto_estoque'];
                        if ($available < $quant) {
                            throw new Exception("Estoque insuficiente para produto ID $idProd");
                        }
                        // Atualiza subtraindo a quantidade e setando data_saida
                        $stmtUpdateEst->execute([
                            ':quantidade' => $quant,
                            ':data_saida' => $saida->getDataHora(),
                            ':id_estoque' => (int) $estRow['id_estoque']
                        ]);
                    } else {
                        // Se não existe estoque, lança erro
                        throw new Exception("Estoque insuficiente para produto ID $idProd");
                    }
                }
            }

            $this->conexao->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexao->rollBack();
            error_log('SaidaDAO::inserirComProdutos PDOException: ' . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            $this->conexao->rollBack();
            error_log('SaidaDAO::inserirComProdutos Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Atualizar saida
     */
    public function atualizar(Saida $saida): bool {
        $sql = "UPDATE saida SET
                    data_hora_saida = :data_hora,
                    tipo_saida = :tipo,
                    observacao_saida = :observacao
                WHERE id_saida = :id";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':data_hora'  => $saida->getDataHora(),
            ':tipo'       => $saida->getTipo(),
            ':observacao' => $saida->getObservacao(),
            ':id'         => $saida->getId()
        ]);
    }

    /**
     * Excluir saida
     */
    public function excluir(int $idSaida): bool {
        $sql = "DELETE FROM saida WHERE id_saida = :id";
        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':id' => $idSaida
        ]);
    }

    /**
     * Buscar saida por ID
     */
    public function buscarPorId(int $idSaida): ?Saida {
        $sql = "SELECT * FROM saida WHERE id_saida = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':id' => $idSaida]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        return new Saida(
            $dados['data_hora_saida'],
            $dados['tipo_saida'],
            $dados['observacao_saida'],
            $dados['id_saida']
        );
    }

    /**
     * Listar todas as saidas
     */
    public function listarTodos(): array {
        $sql = "SELECT s.*, 
                       p.id_pedido, p.data_hora_pedido as pedido_data_hora, p.status_pedido as pedido_status, 
                       p.tipo_pedido as pedido_tipo, p.observacao_pedido as pedido_observacao,
                       u.id_usuario, u.nome_usuario, u.sobrenome_usuario
                FROM saida s
                LEFT JOIN pedido p ON s.pedido_saida = p.id_pedido
                LEFT JOIN usuario u ON p.usuario_pedido = u.id_usuario
                ORDER BY s.data_hora_saida DESC";
        $stmt = $this->conexao->query($sql);

        $saidas = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Criar objeto Usuario se houver dados
            $usuario = null;
            if ($dados['id_usuario']) {
                $usuario = new Usuario(
                    $dados['nome_usuario'],
                    $dados['sobrenome_usuario'],
                    '', // matricula
                    '', // setor
                    '', // funcao
                    '', // email
                    '', // senha
                    '', // status
                    $dados['id_usuario']
                );
            }

            // Criar objeto Pedido se houver dados
            $pedido = null;
            if ($dados['id_pedido']) {
                $pedido = new Pedido(
                    $dados['pedido_data_hora'],
                    $dados['pedido_status'],
                    $dados['pedido_tipo'],
                    $usuario,
                    $dados['pedido_observacao'],
                    null, // dataRetirada
                    null, // dataDevolucao
                    $dados['id_pedido']
                );
            }

            $saidas[] = new Saida(
                $dados['data_hora_saida'],
                $dados['tipo_saida'],
                $pedido,
                $dados['observacao_saida'],
                $dados['id_saida']
            );
        }

        return $saidas;
    }

    /**
     * Listar todas as saidas com seus produtos associados
     */
    public function listarTodosComProdutos(): array {
        $saidas = $this->listarTodos();

        $sqlPs = "SELECT ps.*, p.* FROM produto_saida ps
                  JOIN produto p ON p.id_produto = ps.produto_produto_saida
                  WHERE ps.saida_produto_saida = :id";

        $stmtPs = $this->conexao->prepare($sqlPs);

        foreach ($saidas as $saida) {
            $stmtPs->execute([':id' => $saida->getId()]);
            $itens = [];

            while ($row = $stmtPs->fetch(PDO::FETCH_ASSOC)) {
                $produto = new Produto(
                    $row['nome_produto'],
                    $row['discriminacao_produto'],
                    $row['tipo_produto'],
                    $row['marca_produto'],
                    $row['data_registro_produto'],
                    $row['validade_produto'],
                    $row['ca_produto'],
                    $row['ca_data_validade_produto'],
                    $row['id_produto']
                );

                $ps = new ProdutoSaida(
                    $produto,
                    $saida,
                    (int)$row['quantidade_produto_saida'],
                    $row['observacao_produto_saida'] ?? '',
                    (int)($row['id_produto_saida'] ?? 0)
                );

                $itens[] = $ps;
            }
            $saida->setItens($itens);
        }

        return $saidas;
    }
}