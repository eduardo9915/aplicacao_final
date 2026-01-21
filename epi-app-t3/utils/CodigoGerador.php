<?php
/**
 * Gera códigos únicos para movimentações (Entrada e Saída)
 */
class CodigoGerador {
    
    /**
     * Gera código único para entrada
     * Formato: ENTRADA-YYYYMMDD-XXXX
     */
    public static function gerarCodigoEntrada(int $idEntrada): string {
        $data = date('Ymd');
        $sequencial = str_pad($idEntrada, 4, '0', STR_PAD_LEFT);
        return "ENTRADA-{$data}-{$sequencial}";
    }

    /**
     * Gera código único para saída
     * Formato: SAIDA-YYYYMMDD-XXXX
     */
    public static function gerarCodigoSaida(int $idSaida): string {
        $data = date('Ymd');
        $sequencial = str_pad($idSaida, 4, '0', STR_PAD_LEFT);
        return "SAIDA-{$data}-{$sequencial}";
    }
}
