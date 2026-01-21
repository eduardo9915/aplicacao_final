<?php
require_once __DIR__ . "/../model/Entrada.php";
require_once __DIR__ . "/../model/ProdutoEntrada.php";
require_once __DIR__ . "/../model/Produto.php";

class EntradaDAO extends Conexao {

    private ?PDO $conexao;

    public function __construct() {
        $this->conexao = $this->pegarConexao();
    }

    /**
     * Inserir entrada (Apenas o cabeçalho)
     */
    public function inserir(Entrada $entrada): bool {
        // Colunas conferidas com tabela 'entrada' [cite: 7, 9, 10, 11]
        $sql = "INSERT INTO entrada (
                    data_hora_entrada,
                    tipo_entrada,
                    observacao_entrada
                ) VALUES (
                    :data_hora,
                    :tipo,
                    :observacao
                )";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':data_hora'  => $entrada->getDataHora(),
            ':tipo'       => $entrada->getTipo(),
            ':observacao' => $entrada->getObservacao()
        ]);
    }

    /**
     * Inserir entrada com múltiplos produtos e atualizar estoque (Transactional)
     */
    public function inserirComProdutos(Entrada $entrada, array $produtos): bool {
        try {
            $this->conexao->beginTransaction();

            // 1. Inserir na tabela 'entrada' [cite: 7]
            $sql = "INSERT INTO entrada (
                        data_hora_entrada,
                        tipo_entrada,
                        observacao_entrada
                    ) VALUES (
                        :data_hora,
                        :tipo,
                        :observacao
                    )";

            $stmt = $this->conexao->prepare($sql);
            $stmt->execute([
                ':data_hora'  => $entrada->getDataHora(),
                ':tipo'       => $entrada->getTipo(),
                ':observacao' => $entrada->getObservacao()
            ]);

            $idEntrada = (int) $this->conexao->lastInsertId();

            if (!empty($produtos)) {
                // 2. Query para tabela 'produto_entrada'
                // Ajustado conforme diagrama [cite: 1, 2, 3, 4, 5, 6]
                $sqlPe = "INSERT INTO produto_entrada (
                            produto_produto_entrada,
                            entrada_produto_entrada,
                            quantidade_produto_entrada,
                            observacao_produto_entrada
                        ) VALUES (
                            :id_produto,
                            :id_entrada,
                            :quantidade,
                            :observacao
                        )";
                $stmtPe = $this->conexao->prepare($sqlPe);                
                
                // 3. Preparar queries de ESTOQUE
                // Ajustado: nome da coluna é 'produto_estoque' (FK) e 'quantidade_produto_estoque'
                // Fontes: [cite: 19, 20, 23]
                
                // Busca estoque pelo produto (FK: produto_estoque)
                $sqlSelectEst = "SELECT id_estoque, quantidade_produto_estoque 
                                 FROM estoque 
                                 WHERE produto_estoque = :id_produto LIMIT 1";
                $stmtSelectEst = $this->conexao->prepare($sqlSelectEst);

                // Atualiza quantidade (usando PK id_estoque)
                $sqlUpdateEst = "UPDATE estoque 
                                 SET quantidade_produto_estoque = quantidade_produto_estoque + :quantidade 
                                 WHERE id_estoque = :id_estoque";
                $stmtUpdateEst = $this->conexao->prepare($sqlUpdateEst);

                // Insere novo estoque (FK: produto_estoque)
                $sqlInsertEst = "INSERT INTO estoque (
                                    produto_estoque, 
                                    quantidade_produto_estoque, 
                                    data_entrada_estoque, 
                                    data_saida_estoque
                                 ) VALUES (
                                    :id_produto, 
                                    :quantidade, 
                                    :data_entrada, 
                                    NULL
                                 )";
                $stmtInsertEst = $this->conexao->prepare($sqlInsertEst);

                foreach ($produtos as $p) {
                    $idProd = (int) $p['idProduto'];
                    $quant = (int) $p['quantidade'];
                    $observacaoProd = $p['observacao'] ?? '';

                    // Executa insert na tabela associativa produto_entrada
                    $stmtPe->execute([
                        ':id_produto' => $idProd,
                        ':id_entrada' => $idEntrada,
                        ':quantidade' => $quant,
                        ':observacao' => $observacaoProd
                    ]);
                    
                    // Logica de Atualização de Estoque
                    $stmtSelectEst->execute([':id_produto' => $idProd]);
                    $estRow = $stmtSelectEst->fetch(PDO::FETCH_ASSOC);

                    if ($estRow) {
                        // Se já existe, atualiza somando a quantidade
                        $stmtUpdateEst->execute([
                            ':quantidade' => $quant,
                            ':id_estoque' => (int) $estRow['id_estoque'] // Usa o ID do estoque recuperado
                        ]);
                    } else {
                        // Se não existe, cria novo registro no estoque
                        $stmtInsertEst->execute([
                            ':id_produto'   => $idProd,
                            ':quantidade'   => $quant,
                            ':data_entrada' => $entrada->getDataHora()
                        ]);
                    }
                }
            }

            $this->conexao->commit();
            return true;

        } catch (PDOException $e) {
            $this->conexao->rollBack();
            error_log('EntradaDAO::inserirComProdutos PDOException: ' . $e->getMessage());
            // Mantive o throw original mas removi o return false inalcançável
            throw new Exception("Erro ao inserir entrada com produtos."); 
        }
    }

    /**
     * Atualizar entrada
     */
    public function atualizar(Entrada $entrada): bool {
        $sql = "UPDATE entrada SET
                    data_hora_entrada = :data_hora,
                    tipo_entrada = :tipo,
                    observacao_entrada = :observacao
                WHERE id_entrada = :id";

        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':data_hora'  => $entrada->getDataHora(),
            ':tipo'       => $entrada->getTipo(),
            ':observacao' => $entrada->getObservacao(),
            ':id'         => $entrada->getId()
        ]);
    }

    /**
     * Excluir entrada
     */
    public function excluir(int $idEntrada): bool {
        $sql = "DELETE FROM entrada WHERE id_entrada = :id";
        $stmt = $this->conexao->prepare($sql);

        return $stmt->execute([
            ':id' => $idEntrada
        ]);
    }

    /**
     * Buscar entrada por ID
     */
    public function buscarPorId(int $idEntrada): ?Entrada {
        $sql = "SELECT * FROM entrada WHERE id_entrada = :id";
        $stmt = $this->conexao->prepare($sql);
        $stmt->execute([':id' => $idEntrada]);

        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dados) {
            return null;
        }

        // Certifique-se que o construtor da Model Entrada segue esta ordem
        return new Entrada(
            $dados['data_hora_entrada'],
            $dados['tipo_entrada'],
            $dados['observacao_entrada'],
            $dados['id_entrada']
        );
    }

    /**
     * Listar todas as entradas
     */
    public function listarTodos(): array {
        $sql = "SELECT * FROM entrada ORDER BY data_hora_entrada DESC";
        $stmt = $this->conexao->query($sql);

        $entradas = [];

        while ($dados = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $entradas[] = new Entrada(
                $dados['data_hora_entrada'],
                $dados['tipo_entrada'],
                $dados['observacao_entrada'],
                $dados['id_entrada']
            );
        }

        return $entradas;
    }

    /**
     * Listar todas as entradas com seus produtos associados
     */
    public function listarTodosComProdutos(): array {
        // Recupera todas as entradas primeiro
        $entradas = $this->listarTodos(); 
        
        // Preparando a query de itens para reutilizar no loop
        // Join ajustado para usar 'produto_produto_entrada' e 'entrada_produto_entrada' [cite: 5, 6]
        $sqlPe = "SELECT pe.*, p.* FROM produto_entrada pe 
                  JOIN produto p ON p.id_produto = pe.produto_produto_entrada 
                  WHERE pe.entrada_produto_entrada = :id";
        
        $stmtPe = $this->conexao->prepare($sqlPe);

        foreach ($entradas as $entrada) {
            $stmtPe->execute([':id' => $entrada->getId()]);
            $itens = [];

            while ($row = $stmtPe->fetch(PDO::FETCH_ASSOC)) {
                // Mapeando colunas da tabela produto [cite: 24, 25, 26, 27, 28, 29, 30, 31, 32, 33]
                $produto = new Produto(
                    $row['nome_produto'],
                    $row['discriminacao_produto'], // Verifique se no banco está 'discriminacao_produtc' (typo do diagrama) ou correto. Assumi correto.
                    $row['tipo_produto'],          // Diagrama: tipo_produtc (typo visual provável)
                    $row['marca_produto'],
                    $row['data_registro_produto'],
                    $row['validade_produto'],
                    $row['ca_produto'],
                    $row['ca_data_validade_produto'],
                    $row['id_produto']
                );

                $pe = new ProdutoEntrada(
                    $produto,
                    $entrada,
                    (int)$row['quantidade_produto_entrada'],
                    $row['observacao_produto_entrada'] ?? '',
                    (int)($row['id_produto_entrada'] ?? 0)
                );

                $itens[] = $pe;
            }
            // Supondo que sua model Entrada tenha esse método
            $entrada->setItens($itens); 
        }

        return $entradas;
    }
}