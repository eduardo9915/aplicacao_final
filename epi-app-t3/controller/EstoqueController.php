<?php
require_once __DIR__ . "/../repositories/EstoqueDAO.php";
require_once __DIR__ . "/../repositories/ProdutoDAO.php";
require_once __DIR__ . "/../model/Estoque.php";
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../utils/Util.php";

class EstoqueController {
    private ?EstoqueDAO $dao;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $con = Conexao::pegarConexao();
        $this->dao = new EstoqueDAO($con);
    }

    public function inserirEstoque(string $requisicao) {
        if ($requisicao === "GET") {
            // carregar lista de produtos
            $produtoDao = new ProdutoDAO();
            $resProdutos = $produtoDao->listarTodos();
            $_SESSION['listaProduto'] = Util::converterListaProduto($resProdutos);

            include "./view/estoque/cadastrarEstoque.php";
            return;
        }

        if ($requisicao === "POST") {
            $idProduto = (int) ($_POST['idProduto'] ?? 0);
            $quantidade = (int) ($_POST['quantidade'] ?? 0);
            $dataEntrada = $_POST['dataEntrada'] ?? date('Y-m-d');
            $dataSaida = !empty($_POST['dataSaida']) ? $_POST['dataSaida'] : null;

            // pegar produto para criar model
            $produtoDao = new ProdutoDAO();
            $produto = $produtoDao->buscarPorId($idProduto);

            $model = new Estoque(
                $produto,
                $quantidade,
                $dataEntrada,
                $dataSaida
            );

            $resposta = $this->dao->inserir($model);

            if ($resposta) {
                header("Location: /code/epi-app-t3/estoque/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function atualizarEstoque(string $requisicao) {
        if ($requisicao === "GET") {
            include "./view/estoque/editarEstoque.php";
            return;
        }

        if ($requisicao === "POST") {
            $id = (int) ($_POST['idEstoque'] ?? 0);
            $idProduto = (int) ($_POST['idProduto'] ?? 0);
            $quantidade = (int) ($_POST['quantidade'] ?? 0);
            $dataEntrada = $_POST['dataEntrada'] ?? '';
            $dataSaida = !empty($_POST['dataSaida']) ? $_POST['dataSaida'] : null;

            $produtoDao = new ProdutoDAO();
            $produto = $produtoDao->buscarPorId($idProduto);

            $model = new Estoque(
                $produto,
                $quantidade,
                $dataEntrada,
                $dataSaida,
                $id
            );

            $res = $this->dao->atualizar($model);
            if ($res) {
                header("Location: /code/epi-app-t3/estoque/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function listaTodosEstoques(string $requisicao) {
        if ($requisicao === "GET") {
            $respostaBD = $this->dao->listarTodos();
            $_SESSION['listaEstoque'] = $respostaBD;
            include "./view/estoque/listaEstoque.php";
        }
    }

    public function excluirEstoques(string $requisicao) {
        if ($requisicao === "GET") {
            $id = isset($_GET['idEstoque']) ? (int) $_GET['idEstoque'] : 0;
            $res = $this->dao->excluir($id);
            if ($res) {
                header("Location: /code/epi-app-t3/estoque/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }
}
