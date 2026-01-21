<?php
require_once __DIR__ . "/controller/SetorController.php";
$setorController = new SetorController();
require_once __DIR__ . "/controller/UsuarioController.php";
$usuarioController = new UsuarioController();
require_once __DIR__ . "/controller/ProdutoController.php";
$produtoController = new ProdutoController();
require_once __DIR__ . "/controller/PedidoController.php";
$pedidoController = new PedidoController();
require_once __DIR__ . "/controller/EntradaController.php";
$entradaController = new EntradaController();
require_once __DIR__ . "/controller/SaidaController.php";
$saidaController = new SaidaController();
require_once __DIR__ . "/controller/EstoqueController.php";
$estoqueController = new EstoqueController();
require_once __DIR__ . "/repositories/EstoqueDAO.php";
$requisicao = $_SERVER["REQUEST_METHOD"];
//arquivo responsavel pelo gerenciamento de rotas
    $url = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $request = $_SERVER["REQUEST_METHOD"];

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // rotas públicas que não exigem login
    $publicPaths = [
        '/code/epi-app-t3/login',
        '/code/epi-app-t3/logout',
        '/code/epi-app-t3/usuario/cadastro',
        '/code/epi-app-t3/esqueci-senha',
        '/code/epi-app-t3/pedido/cadastro',
        '/code/epi-app-t3/pedido/confirmacao',
        '/code/epi-app-t3/pedido/meusPedidos',
        '/code/epi-app-t3/view/loginFuncionario.php',
        '/code/epi-app-t3/funcionario/logout'
    ];

    if (!isset($_SESSION['usuarioLogado']) && !in_array($url, $publicPaths)) {
        header('Location: /code/epi-app-t3/login');
        exit;
    }

    // Controle de acesso baseado no tipo de usuário
    if (isset($_SESSION['usuarioLogado'])) {
        $usuario = $_SESSION['usuarioLogado'];
        
        // Rotas restritas para funcionários
        $rotasRestritas = [
            '/code/epi-app-t3/produto',
            '/code/epi-app-t3/estoque',
            '/code/epi-app-t3/entrada',
            '/code/epi-app-t3/saida',
            '/code/epi-app-t3/usuario',
            '/code/epi-app-t3/setor'
        ];
        
        if (!$usuario->getAdmUsuario()) {
            foreach ($rotasRestritas as $rota) {
                if (str_starts_with($url, $rota)) {
                    header('Location: /code/epi-app-t3/funcionario/home');
                    exit;
                }
            }
        }
    }

    switch ($url) {
        case "/code/epi-app-t3/":
            // Verificar tipo de usuário e redirecionar se necessário
            if (isset($_SESSION['usuarioLogado'])) {
                $usuario = $_SESSION['usuarioLogado'];
                if (!$usuario->getAdmUsuario()) {
                    header('Location: /code/epi-app-t3/funcionario/home');
                    exit;
                }
                
                // Carregar produtos com estoque baixo para administradores
                $estoqueDAO = new EstoqueDAO();
                $produtosEstoqueBaixo = $estoqueDAO->buscarProdutosEstoqueBaixo();
                $_SESSION['produtos_estoque_baixo'] = $produtosEstoqueBaixo;
            }
            include "./view/home.php";
            break;

        /* Rotas Funcionário */
        case "/code/epi-app-t3/funcionario/home":
            if (isset($_SESSION['usuarioLogado'])) {
                $usuario = $_SESSION['usuarioLogado'];
                if ($usuario->getAdmUsuario()) {
                    header('Location: /code/epi-app-t3/');
                    exit;
                }
                include "./view/home_funcionario.php";
            } else {
                header('Location: /code/epi-app-t3/login');
                exit;
            }
            break;
        case "/code/epi-app-t3/funcionario/perfil":
            if (isset($_SESSION['usuarioLogado'])) {
                $usuario = $_SESSION['usuarioLogado'];
                if ($usuario->getAdmUsuario()) {
                    header('Location: /code/epi-app-t3/');
                    exit;
                }
                include "./view/funcionario/perfil.php";
            } else {
                header('Location: /code/epi-app-t3/login');
                exit;
            }
            break;

        /*Rotas Setor*/
        case "/code/epi-app-t3/setor/cadastro":
            $setorController->inserirSetor($requisicao);
            break;

        case "/code/epi-app-t3/setor/lista":
            $setorController->listaTodosSetores($requisicao);
            break;
        case "/code/epi-app-t3/setor/exclui":
            $setorController->excluirSetores($requisicao);
            break;
        case "/code/epi-app-t3/setor/edita":
            $setorController->atualizarSetor($requisicao);            
            break;
        
        /*Rotas Usuario*/
        case "/code/epi-app-t3/usuario/cadastro":
            $usuarioController->inserirUsuario($requisicao);
            break;
        case "/code/epi-app-t3/usuario/lista":
            $usuarioController->listaTodosUsuarios($requisicao);
            break;
        case "/code/epi-app-t3/usuario/exclui":
            $usuarioController->excluirUsuario($requisicao);
            break;
        case "/code/epi-app-t3/usuario/edita":
            $usuarioController->atualizarUsuario($requisicao);
            break;
        case "/code/epi-app-t3/login":
            $usuarioController->login($requisicao);
            break;
        case "/code/epi-app-t3/logout":
            $usuarioController->logout($requisicao);
            break;
        case "/code/epi-app-t3/esqueci-senha":
            $usuarioController->recuperarSenha($requisicao);
            break;

        /* Rotas Produto */
        case "/code/epi-app-t3/produto/cadastro":
            $produtoController->inserirProduto($requisicao);
            break;
        case "/code/epi-app-t3/produto/lista":
            $produtoController->listaTodosProdutos($requisicao);
            break;
        case "/code/epi-app-t3/produto/exclui":
            $produtoController->excluirProdutos($requisicao);
            break;
        case "/code/epi-app-t3/produto/edita":
            $produtoController->atualizarProduto($requisicao);
            break;

        /* Rotas Entrada */
        case "/code/epi-app-t3/entrada/cadastro":
            $entradaController->inserirEntrada($requisicao);
            break;
        case "/code/epi-app-t3/entrada/lista":
            $entradaController->listaTodasEntradas($requisicao);
            break;
        case "/code/epi-app-t3/entrada/exclui":
            $entradaController->excluirEntradas($requisicao);
            break;
        case "/code/epi-app-t3/entrada/edita":
            $entradaController->atualizarEntrada($requisicao);
            break;



        /* Rotas Saida */
        case "/code/epi-app-t3/saida/cadastro":
            $saidaController->inserirSaida($requisicao);
            break;
        case "/code/epi-app-t3/saida/lista":
            $saidaController->listaTodasSaidas($requisicao);
            break;
        case "/code/epi-app-t3/saida/exclui":
            $saidaController->excluirSaidas($requisicao);
            break;
        case "/code/epi-app-t3/saida/edita":
            $saidaController->atualizarSaida($requisicao);
            break;
        case "/code/epi-app-t3/saida/autorizar":
            $saidaController->autorizarSaida($requisicao);
            break;

        /* Rotas Pedido */
        case "/code/epi-app-t3/pedido/cadastro":
            $pedidoController->inserirPedido($requisicao);
            break;
        case "/code/epi-app-t3/pedido/lista":
            $pedidoController->listaTodosPedidos($requisicao);
            break;
        case "/code/epi-app-t3/pedido/exclui":
            $pedidoController->excluirPedidos($requisicao);
            break;
        case "/code/epi-app-t3/pedido/rejeitar":
            $pedidoController->rejeitarPedido($requisicao);
            break;
        case "/code/epi-app-t3/pedido/meusPedidos":
            $pedidoController->meusPedidos($requisicao);
            break;
        case "/code/epi-app-t3/pedido/confirmacao":
            $pedidoController->confirmarPedido($requisicao);
            break;

        /* Rotas Estoque */
        case "/code/epi-app-t3/estoque/cadastro":
            $estoqueController->inserirEstoque($requisicao);
            break;
        case "/code/epi-app-t3/estoque/lista":
            $estoqueController->listaTodosEstoques($requisicao);
            break;
        case "/code/epi-app-t3/estoque/exclui":
            $estoqueController->excluirEstoques($requisicao);
            break;
        case "/code/epi-app-t3/estoque/edita":
            $estoqueController->atualizarEstoque($requisicao);
            break;
        default:
            header("Location: ./view/error.php");
            exit;
            break;
    }