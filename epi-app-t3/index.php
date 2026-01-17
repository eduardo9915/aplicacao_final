<?php
    require_once __DIR__ . "/model/Setor.php";
    require_once __DIR__ . "/model/Usuario.php";
    require_once __DIR__ . "/model/Produto.php";
    require_once __DIR__ . "/model/Entrada.php";
    require_once __DIR__ . "/model/Estoque.php";
    require_once __DIR__ . "/model/Pedido.php";
    require_once __DIR__ . "/model/Saida.php";
    require_once __DIR__ . "/model/ProdutoEntrada.php";
    require_once __DIR__ . "/model/ProdutoPedido.php";
    require_once __DIR__ . "/model/PedidoSaida.php";
    session_start();    
//arquivo responsavel pelo gerenciamento e iniciação da aplicação:
    include "./routes.php"; 
?>