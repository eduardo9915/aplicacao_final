<?php
require_once __DIR__ . "/../repositories/PedidoDAO.php";
require_once __DIR__ . "/../repositories/ProdutoDAO.php";
require_once __DIR__ . "/../repositories/UsuarioDAO.php";
require_once __DIR__ . "/../model/Pedido.php";
require_once __DIR__ . "/../model/ProdutoPedido.php";
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../utils/Util.php";
require_once __DIR__ . "/../utils/FuncionarioAuth.php";

class PedidoController {
    private ?PedidoDAO $dao;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $con = Conexao::pegarConexao();
        $this->dao = new PedidoDAO($con);
    }

    public function inserirPedido(string $requisicao) {
        if ($requisicao === 'GET') {
            // carregar produtos com quantidade em estoque
            $produtoDao = new ProdutoDAO();
            $resProdutos = $produtoDao->listarTodosComEstoque();
            $_SESSION['listaProduto'] = $resProdutos;

            // carregar usuarios para seleção na view (apenas para administradores)
            if (!FuncionarioAuth::ehFuncionario()) {
                $usuarioDao = new UsuarioDAO();
                $resUsuarios = $usuarioDao->listarTodos();
                $_SESSION['listaUsuario'] = $resUsuarios;
            }

            include "./view/pedido/cadastrarPedido.php";
            return;
        }

        if ($requisicao === 'POST') {
            $dataHora = $_POST['dataHora'] ?? date('Y-m-d H:i:s');
            if (strpos($dataHora, 'T') !== false) {
                $dataHora = str_replace('T', ' ', substr($dataHora, 0, 16));
            }
            $tipo = $_POST['tipo'] ?? '';
            $observacao = $_POST['observacao'] ?? '';
            $status = $_POST['status'] ?? 'PENDENTE';
            
            // Verificar se é funcionário logado ou administrador
            $usuario = null;
            if (FuncionarioAuth::ehFuncionario()) {
                // Usar funcionário logado automaticamente
                $usuarioDao = new UsuarioDAO();
                $usuario = $usuarioDao->buscarPorId(FuncionarioAuth::getId());
            } else {
                // Administrador selecionando usuário manualmente
                $matricula = trim($_POST['matricula'] ?? '');
                if (!empty($matricula)) {
                    $usuarioDao = new UsuarioDAO();
                    $usuario = $usuarioDao->buscarPorMatricula($matricula);
                    if (!$usuario) {
                        include "./view/error.php";
                        exit;
                    }
                }
            }
            
            if (!$usuario) {
                include "./view/error.php";
                exit;
            }

            $model = new Pedido(
                $dataHora,
                $status,
                $tipo,
                $usuario,
                $observacao,
                null,
                null
            );

            // coletar produtos do form
            $produtos = [];
            $ids = $_POST['productId'] ?? [];
            $quantidades = $_POST['quantidade'] ?? [];
            $obs = $_POST['observacaoProd'] ?? [];

            for ($i = 0; $i < count($ids); $i++) {
                $id = (int) $ids[$i];
                if ($id <= 0) continue;
                $produtos[] = [
                    'idProduto' => $id,
                    'quantidade' => (int) ($quantidades[$i] ?? 1),
                    'observacao' => $obs[$i] ?? ''
                ];
            }

            $res = $this->dao->inserirComProdutos($model, $produtos);

            if ($res) {
                // Buscar o pedido recém-criado para exibir na confirmação
                $pedidoCriado = $this->dao->buscarPorIdComProdutos($res);
                $_SESSION['pedidoConfirmado'] = $pedidoCriado;
                
                header('Location: /code/epi-app-t3/pedido/confirmacao');
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function listaTodosPedidos(string $requisicao) {
        if ($requisicao === 'GET') {
            $res = $this->dao->listarTodosComProdutos();
            $_SESSION['listaPedido'] = $res;
            include "./view/pedido/listaPedido.php";
        }
    }

    public function excluirPedidos(string $requisicao) {
        if ($requisicao === 'GET') {
            $id = isset($_GET['idPedido']) ? (int) $_GET['idPedido'] : 0;
            // simples delete por id
            $con = Conexao::pegarConexao();
            $sql = "DELETE FROM pedido WHERE id_pedido = :id";
            $stmt = $con->prepare($sql);
            $res = $stmt->execute([':id' => $id]);
            if ($res) {
                header('Location: /code/epi-app-t3/pedido/lista');
                exit;
            }
        }
    }

    public function rejeitarPedido(string $requisicao) {
        if ($requisicao === 'GET') {
            $id = isset($_GET['idPedido']) ? (int) $_GET['idPedido'] : 0;
            
            if ($id <= 0) {
                include "./view/error.php";
                exit;
            }
            
            // Buscar o pedido para exibir no formulário
            $pedido = $this->dao->buscarPorIdComProdutos($id);
            if (!$pedido) {
                include "./view/error.php";
                exit;
            }
            
            // Armazenar pedido na sessão para a view de reprovação
            $_SESSION['pedidoParaReprovar'] = $pedido;
            include "./view/pedido/reprovarPedido.php";
            return;
        }

        if ($requisicao === 'POST') {
            $id = (int) ($_POST['idPedido'] ?? 0);
            $motivoReprovacao = $_POST['motivoReprovacao'] ?? '';
            
            if ($id <= 0 || empty($motivoReprovacao)) {
                $_SESSION['errorMessage'] = 'ID do pedido inválido ou motivo da reprovação não informado.';
                include "./view/error.php";
                exit;
            }
            
            // Atualizar status para REJEITADO e incluir motivo na observação
            $con = Conexao::pegarConexao();
            
            // Primeiro, buscar o pedido atual para preservar observação existente
            $sqlSelect = "SELECT observacao_pedido FROM pedido WHERE id_pedido = :id";
            $stmtSelect = $con->prepare($sqlSelect);
            $stmtSelect->execute([':id' => $id]);
            $pedidoAtual = $stmtSelect->fetch(PDO::FETCH_ASSOC);
            
            $observacaoAtual = $pedidoAtual['observacao_pedido'] ?? '';
            $novaObservacao = $observacaoAtual;
            
            // Adicionar motivo da reprovação à observação
            if (!empty($observacaoAtual)) {
                $novaObservacao .= "\n\n[MOTIVO DA REPROVAÇÃO]\n" . $motivoReprovacao;
            } else {
                $novaObservacao = "[MOTIVO DA REPROVAÇÃO]\n" . $motivoReprovacao;
            }
            
            $sql = "UPDATE pedido SET status_pedido = 'REJEITADO', observacao_pedido = :observacao WHERE id_pedido = :id";
            $stmt = $con->prepare($sql);
            $res = $stmt->execute([
                ':id' => $id,
                ':observacao' => $novaObservacao
            ]);
            
            if ($res) {
                header('Location: /code/epi-app-t3/pedido/lista');
                exit;
            } else {
                $_SESSION['errorMessage'] = 'Erro ao reprovar o pedido.';
                include "./view/error.php";
                exit;
            }
        }
    }

    public function confirmarPedido(string $requisicao) {
        if ($requisicao === 'GET') {
            $pedido = $_SESSION['pedidoConfirmado'] ?? null;
            unset($_SESSION['pedidoConfirmado']); // Limpar da sessão após exibir
            
            // Passar o pedido para a view
            include "./view/pedido/confirmacaoPedido.php";
        }
    }

    public function meusPedidos(string $requisicao) {
        if ($requisicao === 'GET') {
            // Verificar se usuário está logado
            if (!isset($_SESSION['usuario_id'])) {
                $_SESSION['errorMessage'] = 'Você precisa estar logado para ver seus pedidos.';
                include "./view/error.php";
                exit;
            }
            
            $idUsuario = $_SESSION['usuario_id'];
            $res = $this->dao->listarPorUsuario($idUsuario);
            $_SESSION['meusPedidos'] = $res;
            include "./view/pedido/meusPedidos.php";
        }
    }
}
