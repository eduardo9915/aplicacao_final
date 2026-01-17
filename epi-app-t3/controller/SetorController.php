<?php
    require_once __DIR__ . "/../repositories/SetorDAO.php";
    require_once __DIR__ . "/../model/Setor.php";
    require_once __DIR__ . "/../utils/Util.php";
    class SetorController {
        private ?SetorDAO $dao;
        
        public function __construct() {
            if (session_status() === PHP_SESSION_NONE){
                session_start();
            }
            $this->dao = new SetorDAO();
        }

        public function inserirSetor(string $requisicao) {
            if ($requisicao === "GET") {
                include "./view/setor/cadastrarSetor.php";
            }

            if ($requisicao === "POST") {
                $model = new Setor(
                    htmlspecialchars($_POST["nomeSetor"])                
                );

                $respostaCliente = $this->dao->inserir($model);                
               if ($respostaCliente) {
                    header("Location: /code/epi-app-t3/setor/lista");
                    exit;
                }
            }
        }

        public function atualizarSetor(string $requisicao) {
            if ($requisicao === "GET") {
                include "./view/setor/editarSetor.php";
            }

            if ($requisicao === "POST") {
                $model = new Setor(
                    htmlspecialchars($_POST["nomeSetor"]),
                    htmlspecialchars($_POST["idSetor"])
                );
                $resposta = $this->dao->atualizar($model);

                if ($resposta) {
                    header("Location: /code/epi-app-t3/setor/lista");
                    exit;
                } else {
                    include "./view/error.php";
                    exit;
                }
            }
        }

        public function listaTodosSetores(string $requisicao){
            if ($requisicao === "GET"){
                $respostaBD = $this->dao->listarTodos();                
                $respostaCliente = Util::converterListaSetor($respostaBD);
                $_SESSION["listaSetor"] = $respostaCliente;
                include "./view/setor/listaSetor.php";                                   
            }
        }
        
        

        public function excluirSetores(string $requisicao){
            if ($requisicao === "GET") {
                $resposta = $this->dao->excluir($_GET["idSetor"]);
                
                if ($resposta) {                    
                  header("Location: /code/epi-app-t3/setor/lista");
                  exit;
                }
            }
        }
    }
?>