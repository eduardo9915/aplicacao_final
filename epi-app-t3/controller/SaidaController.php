<?php
require_once __DIR__ . "/../repositories/SaidaDAO.php";
require_once __DIR__ . "/../repositories/ProdutoDAO.php";
require_once __DIR__ . "/../model/Saida.php";
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../utils/Util.php";

class SaidaController {
    private ?SaidaDAO $dao;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $con = Conexao::pegarConexao();
        $this->dao = new SaidaDAO($con);
    }

    public function inserirSaida(string $requisicao) {
        if ($requisicao === "GET") {
            // carregar lista de pedidos pendentes com produtos
            $pedidoDao = new PedidoDAO();
            $resPedidos = $pedidoDao->listarPendentesComProdutos();
            $_SESSION['listaPedidoPendente'] = $resPedidos;

            include "./view/saida/cadastrarSaida.php";
            return;
        }

        if ($requisicao === "POST") {
            $idPedido = (int) ($_POST['idPedido'] ?? 0);
            if ($idPedido <= 0) {
                include "./view/error.php";
                exit;
            }

            // buscar o pedido
            $pedidoDao = new PedidoDAO();
            $pedido = $pedidoDao->buscarPorIdComProdutos($idPedido);
            if (!$pedido) {
                include "./view/error.php";
                exit;
            }

            // criar a saída baseada no pedido
            $dataHora = $_POST['dataHora'] ?? date('Y-m-d H:i:s');
            if (strpos($dataHora, 'T') !== false) {
                $dataHora = str_replace('T', ' ', substr($dataHora, 0, 16));
            }
            $observacao = $_POST['observacao'] ?? '';

            $model = new Saida(
                $dataHora,
                $pedido->getTipo(),
                $pedido,
                $observacao
            );

            // produtos do pedido
            $produtos = [];
            foreach ($pedido->getItens() as $item) {
                $produtos[] = [
                    'idProduto' => $item->getProduto()->getId(),
                    'quantidade' => $item->getQuantidade(),
                    'observacao' => $item->getObservacao()
                ];
            }

            try {
                $resposta = $this->dao->inserirComProdutos($model, $produtos);
                if ($resposta) {
                    // atualizar status do pedido para APROVADO
                    $pedidoDao->atualizarStatus($idPedido, 'APROVADO');
                    header("Location: /code/epi-app-t3/saida/lista");
                    exit;
                } else {
                    $_SESSION['errorMessage'] = 'Erro ao inserir saída.';
                    include "./view/error.php";
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['errorMessage'] = $e->getMessage();
                include "./view/error.php";
                exit;
            }
        }
    }

    public function atualizarSaida(string $requisicao) {
        if ($requisicao === "GET") {
            include "./view/saida/editarSaida.php";
            return;
        }

        if ($requisicao === "POST") {
            $id = (int) ($_POST['idSaida'] ?? 0);
            $dataHora = $_POST['dataHora'] ?? '';
            if (strpos($dataHora, 'T') !== false) {
                $dataHora = str_replace('T', ' ', $dataHora);
            }

            // tentar recuperar o objeto Saida atual na sessão para obter o Pedido associado
            $pedidoObj = null;
            if (isset($_SESSION['listaSaida']) && is_array($_SESSION['listaSaida'])) {
                foreach ($_SESSION['listaSaida'] as $s) {
                    if (method_exists($s, 'getId') && $s->getId() === $id) {
                        if (method_exists($s, 'getPedidoId')) {
                            $pedidoObj = $s->getPedidoId();
                        }
                        break;
                    }
                }
            }

            $model = new Saida(
                htmlspecialchars($dataHora),
                htmlspecialchars($_POST['tipo'] ?? ''),
                $pedidoObj,
                htmlspecialchars($_POST['observacao'] ?? ''),
                $id
            );

            $res = $this->dao->atualizar($model);
            if ($res) {
                header("Location: /code/epi-app-t3/saida/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function listaTodasSaidas(string $requisicao) {
        if ($requisicao === "GET") {
            $respostaBD = $this->dao->listarTodosComProdutos();

            $_SESSION['listaSaida'] = $respostaBD;

            include "./view/saida/listaSaida.php";
        }
    }

    public function excluirSaidas(string $requisicao) {
        if ($requisicao === "GET") {
            $id = isset($_GET['idSaida']) ? (int) $_GET['idSaida'] : 0;
            $res = $this->dao->excluir($id);
            if ($res) {
                header("Location: /code/epi-app-t3/saida/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function autorizarSaida(string $requisicao) {
        if ($requisicao === "GET") {
            $idPedido = (int) ($_GET['idPedido'] ?? 0);
            if ($idPedido <= 0) {
                include "./view/error.php";
                exit;
            }

            // buscar o pedido
            $pedidoDao = new PedidoDAO();
            $pedido = $pedidoDao->buscarPorIdComProdutos($idPedido);
            if (!$pedido || $pedido->getStatus() !== 'PENDENTE') {
                include "./view/error.php";
                exit;
            }

            // criar a saída baseada no pedido
            $model = new Saida(
                date('Y-m-d H:i:s'),
                $pedido->getTipo(),
                $pedido,
                'Saída autorizada automaticamente'
            );

            // produtos do pedido
            $produtos = [];
            foreach ($pedido->getItens() as $item) {
                $produtos[] = [
                    'idProduto' => $item->getProduto()->getId(),
                    'quantidade' => $item->getQuantidade(),
                    'observacao' => $item->getObservacao()
                ];
            }

            try {
                $resposta = $this->dao->inserirComProdutos($model, $produtos);
                if ($resposta) {
                    // atualizar status do pedido para APROVADO
                    $pedidoDao->atualizarStatus($idPedido, 'APROVADO');
                    header("Location: /code/epi-app-t3/pedido/lista");
                    exit;
                } else {
                    $_SESSION['errorMessage'] = 'Erro ao inserir saída.';
                    include "./view/error.php";
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['errorMessage'] = $e->getMessage();
                include "./view/error.php";
                exit;
            }
        }
    }
}