<?php
require_once __DIR__ . "/../repositories/EntradaDAO.php";
require_once __DIR__ . "/../repositories/ProdutoDAO.php";
require_once __DIR__ . "/../model/Entrada.php";
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../utils/Util.php";

class EntradaController {
    private ?EntradaDAO $dao;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $con = Conexao::pegarConexao();
        $this->dao = new EntradaDAO($con);
    }

    public function inserirEntrada(string $requisicao) {
        if ($requisicao === "GET") {
            // carregar lista de produtos para o select
            $produtoDao = new ProdutoDAO();
            $resProdutos = $produtoDao->listarTodos();
            $_SESSION['listaProduto'] = Util::converterListaProduto($resProdutos);

            include "./view/entrada/cadastrarEntrada.php";
            return;
        }

        if ($requisicao === "POST") {                        
            

            $model = new Entrada(
                htmlspecialchars($_POST["dataHora"]),
                htmlspecialchars($_POST['tipo']),
                htmlspecialchars($_POST['observacao'])
            );

            // montar array de produtos vindos do form
            $produtos = [];
            $ids = $_POST['productId'] ?? [];
            $quantidades = $_POST['quantidade'] ?? [];
            $obs = $_POST['observacaoProd'] ?? [];

            for ($i = 0; $i < count($ids); $i++) {
                $idProduto = (int) $ids[$i];
                if ($idProduto <= 0) continue;
                $quant = (int)$quantidades[$i];
                $observacaoProd = !empty($obs[$i]) ? htmlspecialchars($obs[$i]) : '';
                $produtos[] = [
                    'idProduto' => $idProduto,
                    'quantidade' => $quant,
                    'observacao' => $observacaoProd
                ];
            }

            $resposta = $this->dao->inserirComProdutos($model, $produtos);

            if ($resposta) {
                header("Location: /code/epi-app-t3/entrada/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function atualizarEntrada(string $requisicao) {
        if ($requisicao === "GET") {
            include "./view/entrada/editarEntrada.php";
            return;
        }

        if ($requisicao === "POST") {
            $id = (int) ($_POST['idEntrada'] ?? 0);
            $dataHora = $_POST['dataHora'] ?? '';
            if (strpos($dataHora, 'T') !== false) {
                $dataHora = str_replace('T', ' ', $dataHora);
            }

            $model = new Entrada(
                htmlspecialchars($dataHora),
                htmlspecialchars($_POST['tipo'] ?? ''),
                htmlspecialchars($_POST['observacao'] ?? ''),
                $id
            );

            $res = $this->dao->atualizar($model);
            if ($res) {
                header("Location: /code/epi-app-t3/entrada/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function listaTodasEntradas(string $requisicao) {
        if ($requisicao === "GET") {
            $respostaBD = $this->dao->listarTodosComProdutos();

            // A DAO já retorna objetos Entrada com seus itens, então apenas salva na sessão
            $_SESSION['listaEntrada'] = $respostaBD;

            include "./view/entrada/listaEntrada.php";
        }
    }

    public function excluirEntradas(string $requisicao) {
        if ($requisicao === "GET") {
            $id = isset($_GET['idEntrada']) ? (int) $_GET['idEntrada'] : 0;
            $res = $this->dao->excluir($id);
            if ($res) {
                header("Location: /code/epi-app-t3/entrada/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }
}
