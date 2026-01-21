<?php
    require_once __DIR__ . "/../model/Setor.php";
    require_once __DIR__ . "/../model/Usuario.php";

    final class Util {
        public static function converterListaSetor(array $lista): array {
            $novaLista = [];
            foreach ($lista as $setor){
                $model = new Setor(
                    $setor["nome_setor"],
                    $setor["id_setor"]
                );
                $novaLista[] = $model;
            }

            return $novaLista;
        }

        public static function converterListaProduto(array $lista): array {
            $produtos = [];
            foreach ($lista as $produto) {
                $produtos[] = new Produto(
                    $produto['nome_produto'],
                    $produto['discriminacao_produto'],
                    $produto['tipo_produto'],
                    $produto['marca_produto'],
                    $produto['data_registro_produto'],
                    $produto['validade_produto'],
                    $produto['ca_produto'],
                    $produto['ca_data_validade_produto'],
                    $produto['id_produto']
                );
            }
            return $produtos;
        }
    }