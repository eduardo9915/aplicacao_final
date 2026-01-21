<?php
    abstract class Conexao {
        private static ?PDO $pdo = null;
        public static function  pegarConexao() {
            try {
                $dsn = "mysql:host=127.0.0.1;dbname=epi_paulo";
                $user = "root";
                $password = "";

                self::$pdo = new PDO($dsn, $user, $password);
                return self::$pdo;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                return null;
            }
        }
    }
?>