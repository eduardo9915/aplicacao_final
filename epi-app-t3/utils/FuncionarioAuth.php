<?php
require_once __DIR__ . "/../model/Usuario.php";

class FuncionarioAuth {
    
    /**
     * Verifica se o funcionário está logado
     */
    public static function estaLogado(): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['usuarioLogado']) && $_SESSION['usuarioLogado'] instanceof Usuario;
    }
    
    /**
     * Obtém o ID do funcionário logado
     */
    public static function getId(): ?int {
        if (self::estaLogado()) {
            return $_SESSION['usuarioLogado']->getId();
        }
        return null;
    }
    
    /**
     * Obtém o nome do funcionário logado
     */
    public static function getNome(): ?string {
        if (self::estaLogado()) {
            return $_SESSION['usuarioLogado']->getNome();
        }
        return null;
    }
    
    /**
     * Obtém o CPF do funcionário logado
     */
    public static function getCpf(): ?string {
        if (self::estaLogado()) {
            return $_SESSION['usuarioLogado']->getCpf();
        }
        return null;
    }
    
    /**
     * Obtém a matrícula do funcionário logado
     */
    public static function getMatricula(): ?string {
        if (self::estaLogado()) {
            return $_SESSION['usuarioLogado']->getMatricula();
        }
        return null;
    }
    
    /**
     * Obtém o setor do funcionário logado
     */
    public static function getSetor(): ?string {
        if (self::estaLogado()) {
            $setor = $_SESSION['usuarioLogado']->getSetor();
            return $setor ? $setor->getNome() : null;
        }
        return null;
    }
    
    /**
     * Obtém o ID do setor do funcionário logado
     */
    public static function getSetorId(): ?int {
        if (self::estaLogado()) {
            $setor = $_SESSION['usuarioLogado']->getSetor();
            return $setor ? $setor->getId() : null;
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
        unset($_SESSION['usuarioLogado']);
        
        header('Location: /code/epi-app-t3/view/loginFuncionario.php');
        exit;
    }
    
    /**
     * Verifica se o usuário atual é funcionário (não administrador)
     */
    public static function ehFuncionario(): bool {
        if (self::estaLogado()) {
            return !$_SESSION['usuarioLogado']->getAdmUsuario();
        }
        return false;
    }
    
    /**
     * Verifica se o usuário atual é administrador
     */
    public static function ehAdministrador(): bool {
        if (self::estaLogado()) {
            return $_SESSION['usuarioLogado']->getAdmUsuario();
        }
        return false;
    }
}
