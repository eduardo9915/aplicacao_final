<?php
/**
 * Sistema de Autorização por Cargo
 * Controla acesso baseado no cargo do usuário
 */
class Authorization {
    
    // Definição de permissões por cargo
    private static array $permissions = [
        'almoxarifado' => [
            'produto.cadastro',
            'produto.editar',
            'produto.excluir',
            'produto.listar',
            'entrada.cadastro',
            'entrada.editar',
            'entrada.excluir',
            'entrada.listar',
            'saida.cadastro',
            'saida.editar',
            'saida.excluir',
            'saida.listar',
            'saida.autorizar',
            'estoque.cadastro',
            'estoque.editar',
            'estoque.excluir',
            'estoque.listar',
            'pedido.listar',
            'pedido.aprovar',
            'relatorio.visualizar'
        ],
        'rh' => [
            'usuario.cadastro',
            'usuario.editar',
            'usuario.excluir',
            'usuario.listar',
            'setor.cadastro',
            'setor.editar',
            'setor.excluir',
            'setor.listar',
            'pedido.listar',
            'pedido.aprovar',
            'relatorio.visualizar'
        ],
        'técnico de segurança' => [
            'pedido.listar',
            'pedido.aprovar',
            'relatorio.visualizar',
            'produto.listar'
        ],
        'técnico segurança' => [ // variação do nome
            'pedido.listar',
            'pedido.aprovar',
            'relatorio.visualizar',
            'produto.listar'
        ]
    ];

    /**
     * Verifica se o usuário tem permissão para realizar uma ação
     */
    public static function hasPermission(?object $usuario, string $permission): bool {
        if (!$usuario || !isset($_SESSION['usuarioLogado'])) {
            return false;
        }

        $cargo = strtolower(trim($usuario->getCargo()));
        
        // Todos os usuários logados podem fazer pedidos
        if ($permission === 'pedido.cadastro') {
            return true;
        }

        if (!isset(self::$permissions[$cargo])) {
            return false;
        }

        return in_array($permission, self::$permissions[$cargo], true);
    }

    /**
     * Verifica permissão e redireciona se não autorizado
     */
    public static function requirePermission(?object $usuario, string $permission): void {
        if (!self::hasPermission($usuario, $permission)) {
            $_SESSION['errorMessage'] = 'Você não tem permissão para realizar esta ação.';
            header('Location: /code/epi-app-t3/');
            exit;
        }
    }

    /**
     * Retorna o usuário logado da sessão
     */
    public static function getUsuarioLogado(): ?object {
        return $_SESSION['usuarioLogado'] ?? null;
    }
}
