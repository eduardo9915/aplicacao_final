<?php
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../model/Pedido.php";
require_once __DIR__ . "/../model/ProdutoPedido.php";
require_once __DIR__ . "/../model/Produto.php";
require_once __DIR__ . "/../repositories/UsuarioDAO.php";

class PedidoDAO extends Conexao {
    private ?PDO $conexao;

    public function __construct() {
        $this->conexao = $this::pegarConexao();
    }

    /**
     * Inserir pedido (apenas cabeçalho)
     */
    public function inserir(Pedido $pedido): bool {
        $sql = "INSERT INTO pedido (
                    data_hora_pedido,
                    status_pedido,
                    tipo_pedido,
                    usuario_pedido,
                    observacao_pedido,
                    data_retirada_pedido,
                    data_devolucao_pedido
                ) VALUES (
                    :data_hora,
                    :status,
                    :tipo,
                    :usuario,
                    :observacao,
                    :data_retirada,
                    :data_devolucao
                )";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':data_hora' => $pedido->getDataHora(),
            ':status' => $pedido->getStatus(),
            ':tipo' => $pedido->getTipo(),
            ':usuario' => $pedido->getSolicitanteId() ? $pedido->getSolicitanteId()->getId() : null,
            ':observacao' => $pedido->getObservacao(),
            ':data_retirada' => $pedido->getDataRetirada(),
            ':data_devolucao' => $pedido->getDataDevolucao()
        ]);
    }

    /**
     * Inserir pedido com múltiplos produtos (transactional)
     */
    public function inserirComProdutos(Pedido $pedido, array $produtos): bool {
        try {
            $this->conexao->beginTransaction();

            // 1) inserir pedido
            $sql = "INSERT INTO pedido (
                        data_hora_pedido,
                        status_pedido,
                        tipo_pedido,
                        usuario_pedido,
                        observacao_pedido,
                        data_retirada_pedido,
                        data_devolucao_pedido
                    ) VALUES (
                        :data_hora,
                        :status,
                        :tipo,
                        :usuario,
                        :observacao,
                        :data_retirada,
                        :data_devolucao
                    )";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([
                ':data_hora' => $pedido->getDataHora(),
                ':status' => $pedido->getStatus(),
                ':tipo' => $pedido->getTipo(),
                ':usuario' => $pedido->getSolicitanteId() ? $pedido->getSolicitanteId()->getId() : null,
                ':observacao' => $pedido->getObservacao(),
                ':data_retirada' => $pedido->getDataRetirada(),
                ':data_devolucao' => $pedido->getDataDevolucao()
            ]);

            $idPedido = (int)$this->conexao->lastInsertId();

            if (!empty($produtos)) {
                $sqlPp = "INSERT INTO produto_pedido (
                            produto_produto_pedido,
                            pedido_produto_pedido,
                            quantidade_produto_pedido,
                            observacao_produto_pedido
                        ) VALUES (
                            :id_produto,
                            :id_pedido,
                            :quantidade,
                            :observacao
                        )";

                $stmtPp = $this->conexao->prepare($sqlPp);

                foreach ($produtos as $p) {
                    $idProd = (int) $p['idProduto'];
                    $quant = (int) $p['quantidade'];
                    $observacaoProd = $p['observacao'] ?? '';

                    $stmtPp->execute([
                        ':id_produto' => $idProd,
                        ':id_pedido' => $idPedido,
                        ':quantidade' => $quant,
                        ':observacao' => $observacaoProd
                    ]);
                }
            }

            $this->conexao->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexao->rollBack();
            error_log('PedidoDAO::inserirComProdutos PDOException: ' . $e->getMessage());
            return false;
        }
    }

    public function listarTodos(): array {
        $sql = "SELECT * FROM pedido ORDER BY data_hora_pedido DESC";
        $stmt = $this->conexao->query($sql);
        $lista = [];
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = null;
            if ($dados['usuario_pedido']) {
                // buscar usuario
                $usuarioDao = new UsuarioDAO();
                $usuario = $usuarioDao->buscarPorId((int)$dados['usuario_pedido']);
            }

            $pedido = new Pedido(
                $dados['data_hora_pedido'],
                $dados['status_pedido'],
                $dados['tipo_pedido'],
                $usuario,
                $dados['observacao_pedido'] ?? null,
                $dados['data_retirada_pedido'] ?? null,
                $dados['data_devolucao_pedido'] ?? null,
                (int) $dados['id_pedido']
            );
            $lista[] = $pedido;
            
        }
        return $lista;
    }

    public function listarTodosComProdutos(): array {
        $pedidos = $this->listarTodos();

        $sql = "SELECT pp.*, p.* FROM produto_pedido pp JOIN produto p ON p.id_produto = pp.produto_produto_pedido WHERE pp.pedido_produto_pedido = :id";
        $stmt = $this->conexao->prepare($sql);

        foreach ($pedidos as $pedido) {
            $stmt->execute([':id' => $pedido->getId()]);
            $itens = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

                $pp = new ProdutoPedido(
                    $produto,
                    $pedido,
                    (int)$row['quantidade_produto_pedido'],
                    $row['observacao_produto_pedido'] ?? '',
                    (int)($row['id_produto_pedido'] ?? 0)
                );

                $itens[] = $pp;
            }
            // adicionar itens ao pedido — não havia método no model Pedido, mas podemos adicionar via setItens caso exista
            if (method_exists($pedido, 'setItens')) {
                $pedido->setItens($itens);
            }
        }

        return $pedidos;
    }

    /**
     * Listar pedidos pendentes
     */
    public function listarPendentes(): array {
        $sql = "SELECT * FROM pedido WHERE status_pedido = 'PENDENTE' ORDER BY data_hora_pedido DESC";
        $stmt = $this->conexao->query($sql);

        $pedidos = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = null;
            if ($dados['usuario_pedido']) {
                // buscar usuario
                $usuarioDao = new UsuarioDAO();
                $usuario = $usuarioDao->buscarPorId((int)$dados['usuario_pedido']);
            }

            $pedidos[] = new Pedido(
                $dados['data_hora_pedido'],
                $dados['status_pedido'],
                $dados['tipo_pedido'],
                $usuario,
                $dados['observacao_pedido'],
                $dados['data_retirada_pedido'],
                $dados['data_devolucao_pedido'],
                $dados['id_pedido']
            );
        }

        return $pedidos;
    }

    /**
     * Atualizar status do pedido
     */
    public function atualizarStatus(int $idPedido, string $status): bool {
        $sql = "UPDATE pedido SET status_pedido = :status WHERE id_pedido = :id";
        $stmt = $this->conexao->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $idPedido]);
    }

    /**
     * Buscar pedido por ID com produtos
     */
    public function buscarPorIdComProdutos(int $idPedido): ?Pedido {
        $sql = "SELECT * FROM pedido WHERE id_pedido = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':id' => $idPedido]);
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        $usuario = null;
        if ($dados['usuario_pedido']) {
            $usuarioDao = new UsuarioDAO();
            $usuario = $usuarioDao->buscarPorId((int)$dados['usuario_pedido']);
        }

        $pedido = new Pedido(
            $dados['data_hora_pedido'],
            $dados['status_pedido'],
            $dados['tipo_pedido'],
            $usuario,
            $dados['observacao_pedido'],
            $dados['data_retirada_pedido'],
            $dados['data_devolucao_pedido'],
            $dados['id_pedido']
        );

        // buscar itens
        $sqlPp = "SELECT pp.*, p.* FROM produto_pedido pp JOIN produto p ON p.id_produto = pp.produto_produto_pedido WHERE pp.pedido_produto_pedido = :id";
        $stmtPp = $this->conexao->prepare($sqlPp);
        $stmtPp->execute([':id' => $idPedido]);
        $itens = [];
        while ($row = $stmtPp->fetch(PDO::FETCH_ASSOC)) {
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

            $pp = new ProdutoPedido(
                $produto,
                $pedido,
                (int)$row['quantidade_produto_pedido'],
                $row['observacao_produto_pedido'] ?? '',
                (int)($row['id_produto_pedido'] ?? 0)
            );

            $itens[] = $pp;
        }
        if (method_exists($pedido, 'setItens')) {
            $pedido->setItens($itens);
        }

        return $pedido;
    }

    /**
     * Listar pedidos de um usuário específico
     */
    public function listarPorUsuario(int $idUsuario): array {
        $sql = "SELECT * FROM pedido WHERE usuario_pedido = :idUsuario ORDER BY data_hora_pedido DESC";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':idUsuario' => $idUsuario]);
        
        $pedidos = [];
        
        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = null;
            if ($dados['usuario_pedido']) {
                $usuarioDao = new UsuarioDAO();
                $usuario = $usuarioDao->buscarPorId((int)$dados['usuario_pedido']);
            }

            $pedido = new Pedido(
                $dados['data_hora_pedido'],
                $dados['status_pedido'],
                $dados['tipo_pedido'],
                $usuario,
                $dados['observacao_pedido'],
                $dados['data_retirada_pedido'],
                $dados['data_devolucao_pedido'],
                $dados['id_pedido']
            );
            $pedidos[] = $pedido;
        }

        // Buscar itens para cada pedido
        $sqlPp = "SELECT pp.*, p.* FROM produto_pedido pp 
            JOIN produto p ON p.id_produto = pp.produto_produto_pedido 
            WHERE pp.pedido_produto_pedido = :id";
        $stmtPp = $this->conexao->prepare($sqlPp);

        foreach ($pedidos as $pedido) {
            $stmtPp->execute([':id' => $pedido->getId()]);
            $itens = [];
            while ($row = $stmtPp->fetch(PDO::FETCH_ASSOC)) {
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

                $pp = new ProdutoPedido(
                    $produto,
                    $pedido,
                    (int)$row['quantidade_produto_pedido'],
                    $row['observacao_produto_pedido'] ?? '',
                    (int)($row['id_produto_pedido'] ?? 0)
                );

                $itens[] = $pp;
            }
            if (method_exists($pedido, 'setItens')) {
                $pedido->setItens($itens);
            }
        }

        return $pedidos;
    }

    /**
     * Listar pedidos pendentes com produtos
     */
    public function listarPendentesComProdutos(): array {
        $pedidos = $this->listarPendentes();

        $sqlPp = "SELECT pp.*, p.* FROM produto_pedido pp 
            JOIN produto p ON p.id_produto = pp.produto_produto_pedido 
            WHERE pp.pedido_produto_pedido = :id";
        $stmtPp = $this->conexao->prepare($sqlPp);

        foreach ($pedidos as $pedido) {
            $stmtPp->execute([':id' => $pedido->getId()]);
            $itens = [];
            while ($row = $stmtPp->fetch(PDO::FETCH_ASSOC)) {
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

                $pp = new ProdutoPedido(
                    $produto,
                    $pedido,
                    (int)$row['quantidade_produto_pedido'],
                    $row['observacao_produto_pedido'] ?? '',
                    (int)($row['id_produto_pedido'] ?? 0)
                );

                $itens[] = $pp;
            }
            if (method_exists($pedido, 'setItens')) {
                $pedido->setItens($itens);
            }
        }

        return $pedidos;
    }
}