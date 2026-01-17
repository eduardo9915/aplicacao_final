<?php

class FuncionarioAuth {
    
    /**
     * Verifica se o funcionário está logado
     */
    public static function estaLogado(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['funcionario_logado']) && $_SESSION['funcionario_logado'] === true;
    }
    
    /**
     * Obtém o ID do funcionário logado
     */
    public static function getId(): ?int {
        if (self::estaLogado()) {
            return $_SESSION['funcionario_id'] ?? null;
        }
        return null;
    }
    
    /**
     * Obtém o nome do funcionário logado
     */
    public static function getNome(): ?string {
        if (self::estaLogado()) {
            return $_SESSION['funcionario_nome'] ?? null;
        }
        return null;
    }
    
    /**
     * Obtém o CPF do funcionário logado
     */
    public static function getCpf(): ?string {
        if (self::estaLogado()) {
            return $_SESSION['funcionario_cpf'] ?? null;
        }
        return null;
    }
    
    /**
     * Obtém a matrícula do funcionário logado
     */
    public static function getMatricula(): ?string {
        if (self::estaLogado()) {
            return $_SESSION['funcionario_matricula'] ?? null;
        }
        return null;
    }
    
    /**
     * Exige que o funcionário esteja logado, senão redireciona para o login
     */
    public static function exigirLogin(): void {
        if (!self::estaLogado()) {
            header('Location: /code/epi-app-t3/view/loginFuncionario.php');
            exit;
        }
    }
    
    /**
     * Realiza logout do funcionário
     */
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Limpar dados da sessão do funcionário
        unset($_SESSION['funcionario_logado']);
        unset($_SESSION['funcionario_id']);
        unset($_SESSION['funcionario_nome']);
        unset($_SESSION['funcionario_cpf']);
        unset($_SESSION['funcionario_matricula']);
        
        header('Location: /code/epi-app-t3/view/loginFuncionario.php');
        exit;
    }
    
    /**
     * Verifica se o usuário atual é funcionário (não administrador)
     */
    public static function ehFuncionario(): bool {
        return self::estaLogado() && !isset($_SESSION['usuarioLogado']);
    }
    
    /**
     * Verifica se o usuário atual é administrador
     */
    public static function ehAdministrador(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuarioLogado']) && $_SESSION['usuarioLogado'] === true;
    }
}
