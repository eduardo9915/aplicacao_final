<?php
require_once __DIR__ . "/../repositories/ProdutoDAO.php";
require_once __DIR__ . "/../model/Produto.php";
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../utils/Util.php";

class ProdutoController {
    private ?ProdutoDAO $dao;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $con = Conexao::pegarConexao();
        $this->dao = new ProdutoDAO($con);
    }

    public function inserirProduto(string $requisicao) {
        if ($requisicao === "GET") {
            include "./view/produto/cadastrarProduto.php";
        }

        if ($requisicao === "POST") {
            $tipo = htmlspecialchars($_POST['tipo']) === "EPI" ?  "1" : "2";
            $model = new Produto(
                htmlspecialchars($_POST['nome']),
                htmlspecialchars($_POST['discriminacao']),
                $tipo,
                htmlspecialchars($_POST['marca']),
                htmlspecialchars($_POST['dataRegistro'] ?? date('Y-m-d')),
                !empty($_POST['validade']) ? $_POST['validade'] : null,
                !empty($_POST['ca']) ? htmlspecialchars($_POST['ca']) : null,
                !empty($_POST['caValidade']) ? $_POST['caValidade'] : null
            );            
            $resposta = $this->dao->inserir($model);
            
            if ($resposta) {
                header("Location: /code/epi-app-t3/produto/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function atualizarProduto(string $requisicao) {
        if ($requisicao === "GET") {
            include "./view/produto/editarProduto.php";
        }

        if ($requisicao === "POST") {
            $id = (int) $_POST['idProduto'];
            $model = new Produto(
                htmlspecialchars($_POST['nome']),
                htmlspecialchars($_POST['discriminacao']),
                htmlspecialchars($_POST['tipo']),
                htmlspecialchars($_POST['marca']),
                $_POST['dataRegistro'],
                $_POST['validade'],
                $_POST['ca'],
                $_POST['caValidade'],
                $id
            );

            $res = $this->dao->atualizar($model);
            if ($res) {
                header("Location: /code/epi-app-t3/produto/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function listaTodosProdutos(string $requisicao) {
        if ($requisicao === "GET") {
            $respostaBD = $this->dao->listarTodos();
            
            $_SESSION['listaProduto'] = Util::converterListaProduto($respostaBD);
            
            include "./view/produto/listaProduto.php";
        }
    }

    public function excluirProdutos(string $requisicao) {
        if ($requisicao === "GET") {
            $id = isset($_GET['idProduto']) ? (int) $_GET['idProduto'] : 0;
            $res = $this->dao->excluir($id);
            if ($res) {
                header("Location: /code/epi-app-t3/produto/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }
}
