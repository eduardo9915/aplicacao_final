<?php
require_once __DIR__ . "/../repositories/ProdutoDAO.php";
require_once __DIR__ . "/../model/Produto.php";

/**
 * Serviço para verificar alertas de validade de produtos e CAs
 */
class AlertaService {
    private ProdutoDAO $produtoDAO;

    public function __construct() {
        $this->produtoDAO = new ProdutoDAO();
    }

    /**
     * Retorna produtos com validade próxima ao vencimento (30 dias)
     */
    public function getProdutosVencendo(): array {
        $produtos = $this->produtoDAO->listarTodos();
        $vencendo = [];
        $hoje = new DateTime();
        $limite = clone $hoje;
        $limite->modify('+30 days');

        foreach ($produtos as $produto) {
            $validade = $produto->getValidade();
            if (!empty($validade)) {
                try {
                    $dataValidade = new DateTime($validade);
                    if ($dataValidade <= $limite && $dataValidade >= $hoje) {
                        $vencendo[] = [
                            'produto' => $produto,
                            'tipo' => 'validade',
                            'data' => $validade,
                            'dias' => $hoje->diff($dataValidade)->days
                        ];
                    }
                } catch (Exception $e) {
                    // Data inválida, ignorar
                }
            }
        }

        return $vencendo;
    }

    /**
     * Retorna produtos com validade vencida
     */
    public function getProdutosVencidos(): array {
        $produtos = $this->produtoDAO->listarTodos();
        $vencidos = [];
        $hoje = new DateTime();

        foreach ($produtos as $produto) {
            $validade = $produto->getValidade();
            if (!empty($validade)) {
                try {
                    $dataValidade = new DateTime($validade);
                    if ($dataValidade < $hoje) {
                        $vencidos[] = [
                            'produto' => $produto,
                            'tipo' => 'validade',
                            'data' => $validade,
                            'dias' => $hoje->diff($dataValidade)->days
                        ];
                    }
                } catch (Exception $e) {
                    // Data inválida, ignorar
                }
            }
        }

        return $vencidos;
    }

    /**
     * Retorna CAs com validade próxima ao vencimento (30 dias)
     */
    public function getCAsVencendo(): array {
        $produtos = $this->produtoDAO->listarTodos();
        $vencendo = [];
        $hoje = new DateTime();
        $limite = clone $hoje;
        $limite->modify('+30 days');

        foreach ($produtos as $produto) {
            $caValidade = $produto->getCaValidade();
            if (!empty($caValidade)) {
                try {
                    $dataCA = new DateTime($caValidade);
                    if ($dataCA <= $limite && $dataCA >= $hoje) {
                        $vencendo[] = [
                            'produto' => $produto,
                            'tipo' => 'ca',
                            'data' => $caValidade,
                            'dias' => $hoje->diff($dataCA)->days
                        ];
                    }
                } catch (Exception $e) {
                    // Data inválida, ignorar
                }
            }
        }

        return $vencendo;
    }

    /**
     * Retorna CAs vencidos
     */
    public function getCAsVencidos(): array {
        $produtos = $this->produtoDAO->listarTodos();
        $vencidos = [];
        $hoje = new DateTime();

        foreach ($produtos as $produto) {
            $caValidade = $produto->getCaValidade();
            if (!empty($caValidade)) {
                try {
                    $dataCA = new DateTime($caValidade);
                    if ($dataCA < $hoje) {
                        $vencidos[] = [
                            'produto' => $produto,
                            'tipo' => 'ca',
                            'data' => $caValidade,
                            'dias' => abs($hoje->diff($dataCA)->days)
                        ];
                    }
                } catch (Exception $e) {
                    // Data inválida, ignorar
                }
            }
        }

        return $vencidos;
    }

    /**
     * Retorna todos os alertas consolidados
     */
    public function getAllAlertas(): array {
        return [
            'vencendo' => $this->getProdutosVencendo(),
            'vencidos' => $this->getProdutosVencidos(),
            'cas_vencendo' => $this->getCAsVencendo(),
            'cas_vencidos' => $this->getCAsVencidos()
        ];
    }
}
