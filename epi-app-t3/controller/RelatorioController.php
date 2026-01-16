<?php
require_once __DIR__ . "/../repositories/PedidoDAO.php";
require_once __DIR__ . "/../repositories/EntradaDAO.php";
require_once __DIR__ . "/../repositories/SaidaDAO.php";
require_once __DIR__ . "/../repositories/UsuarioDAO.php";
require_once __DIR__ . "/../utils/Conexao.php";
require_once __DIR__ . "/../utils/Authorization.php";

class RelatorioController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Relatório de pedidos
     */
    public function relatorioPedidos(string $requisicao) {
        $usuario = Authorization::getUsuarioLogado();
        Authorization::requirePermission($usuario, 'relatorio.visualizar');

        if ($requisicao === 'GET') {
            $dataInicio = $_GET['dataInicio'] ?? date('Y-m-01'); // Primeiro dia do mês
            $dataFim = $_GET['dataFim'] ?? date('Y-m-d');
            $usuarioId = isset($_GET['usuarioId']) ? (int)$_GET['usuarioId'] : null;

            $pedidoDAO = new PedidoDAO();
            $pedidos = $pedidoDAO->listarTodosComProdutos();

            // Filtrar por data
            $pedidosFiltrados = array_filter($pedidos, function($pedido) use ($dataInicio, $dataFim, $usuarioId) {
                $dataPedido = date('Y-m-d', strtotime($pedido->getDataHora()));
                $passaData = ($dataPedido >= $dataInicio && $dataPedido <= $dataFim);
                
                $passaUsuario = true;
                if ($usuarioId !== null && $pedido->getUsuario()) {
                    $passaUsuario = ($pedido->getUsuario()->getId() === $usuarioId);
                }

                return $passaData && $passaUsuario;
            });

            $_SESSION['relatorioPedidos'] = $pedidosFiltrados;
            $_SESSION['filtrosPedidos'] = [
                'dataInicio' => $dataInicio,
                'dataFim' => $dataFim,
                'usuarioId' => $usuarioId
            ];

            // Buscar usuários para o filtro
            $usuarioDAO = new UsuarioDAO();
            $usuarios = $usuarioDAO->listarTodos();
            $_SESSION['listaUsuarioFiltro'] = $usuarios;

            include "./view/relatorio/relatorioPedidos.php";
        }
    }

    /**
     * Relatório de movimentações (entradas e saídas)
     */
    public function relatorioMovimentacoes(string $requisicao) {
        $usuario = Authorization::getUsuarioLogado();
        Authorization::requirePermission($usuario, 'relatorio.visualizar');

        if ($requisicao === 'GET') {
            $dataInicio = $_GET['dataInicio'] ?? date('Y-m-01');
            $dataFim = $_GET['dataFim'] ?? date('Y-m-d');
            $tipo = $_GET['tipo'] ?? 'todos'; // 'entrada', 'saida', 'todos'
            $codigo = $_GET['codigo'] ?? '';

            $entradas = [];
            $saidas = [];

            if ($tipo === 'entrada' || $tipo === 'todos') {
                $entradaDAO = new EntradaDAO();
                $todasEntradas = $entradaDAO->listarTodosComProdutos();
                $entradas = array_filter($todasEntradas, function($entrada) use ($dataInicio, $dataFim, $codigo) {
                    $dataEntrada = date('Y-m-d', strtotime($entrada->getDataHora()));
                    $passaData = ($dataEntrada >= $dataInicio && $dataEntrada <= $dataFim);
                    
                    $passaCodigo = true;
                    if (!empty($codigo)) {
                        require_once __DIR__ . "/../utils/CodigoGerador.php";
                        $codigoEntrada = CodigoGerador::gerarCodigoEntrada($entrada->getId());
                        $passaCodigo = (stripos($codigoEntrada, $codigo) !== false);
                    }

                    return $passaData && $passaCodigo;
                });
            }

            if ($tipo === 'saida' || $tipo === 'todos') {
                $saidaDAO = new SaidaDAO();
                $todasSaidas = $saidaDAO->listarTodosComProdutos();
                $saidas = array_filter($todasSaidas, function($saida) use ($dataInicio, $dataFim, $codigo) {
                    $dataSaida = date('Y-m-d', strtotime($saida->getDataHora()));
                    $passaData = ($dataSaida >= $dataInicio && $dataSaida <= $dataFim);
                    
                    $passaCodigo = true;
                    if (!empty($codigo)) {
                        require_once __DIR__ . "/../utils/CodigoGerador.php";
                        $codigoSaida = CodigoGerador::gerarCodigoSaida($saida->getId());
                        $passaCodigo = (stripos($codigoSaida, $codigo) !== false);
                    }

                    return $passaData && $passaCodigo;
                });
            }

            $_SESSION['relatorioMovimentacoes'] = [
                'entradas' => $entradas,
                'saidas' => $saidas
            ];
            $_SESSION['filtrosMovimentacoes'] = [
                'dataInicio' => $dataInicio,
                'dataFim' => $dataFim,
                'tipo' => $tipo,
                'codigo' => $codigo
            ];

            include "./view/relatorio/relatorioMovimentacoes.php";
        }
    }
}
