<?php
require_once __DIR__ . "/../repositories/SetorDAO.php";
require_once __DIR__ . "/../repositories/UsuarioDAO.php";
require_once __DIR__ . "/../model/Usuario.php";
require_once __DIR__ . "/../model/Setor.php";
require_once __DIR__ . "/../utils/Util.php";

class UsuarioController {
    private ?UsuarioDAO $dao;
    private ?SetorDAO $setorDAO;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->dao = new UsuarioDAO();
        $this->setorDAO = new SetorDAO();
    }

    public function inserirUsuario(string $requisicao) {
        if ($requisicao === "GET") {
            $setores = $this->setorDAO->listarTodos();
            $_SESSION["listaSetor"] = Util::converterListaSetor($setores);
            include "./view/usuario/cadastrarUsuario.php";
        }

        if ($requisicao === "POST") {
            $model = new Usuario(
                $_POST["nome"],
                $_POST["sobrenome"],
                $_POST["matricula"],
                $_POST["telefone"],
                $_POST["cargo"],
                $_POST["dataAdmissao"],
                $_POST["cpf"],
                $_POST["senha"],
                $_POST["email"],
                null,
                $_POST["dataDemissao"],
                0,
                isset($_POST["admUsuario"]) ? (bool)$_POST["admUsuario"] : false
            );            

            $id = (int) $_POST["setor"];            
            try {
                $resposta = $this->dao->inserir($model, $id);
                if ($resposta) {
                    header("Location: /code/epi-app-t3/usuario/lista");
                    exit;
                } else {
                    $_SESSION['errorMessage'] = 'Erro ao cadastrar usuário.';
                    include "./view/error.php";
                    exit;
                }
            } catch (Exception $e) {
                $_SESSION['errorMessage'] = $e->getMessage();
                $setores = $this->setorDAO->listarTodos();
                $_SESSION["listaSetor"] = Util::converterListaSetor($setores);
                include "./view/usuario/cadastrarUsuario.php";
                exit;
            }
            
        }
    }

    public function atualizarUsuario(string $requisicao) {
        if ($requisicao === "GET") {
            // Carregar dados do usuário para edição
            $id = (int) ($_GET['idUsuario'] ?? 0);
            if ($id > 0) {
                $usuario = $this->dao->buscarPorId($id);
                if ($usuario) {
                    $setores = $this->setorDAO->listarTodos();
                    $_SESSION["listaSetor"] = Util::converterListaSetor($setores);
                    $_SESSION["usuarioEdit"] = $usuario;
                    include "./view/usuario/editarUsuario.php";
                    return;
                }
            }
            include "./view/error.php";
            return;
        }

        if ($requisicao === "POST") {
            $setorDAO = new SetorDAO();
            $setor = $setorDAO->buscarPorId((int) $_POST["setor"]);
            
            $model = new Usuario(
                $_POST["nome"],
                $_POST["sobrenome"],
                $_POST["matricula"],
                $_POST["telefone"],
                $_POST["cargo"],
                $_POST["dataAdmissao"],
                $_POST["cpf"],
                $_POST["senha"],
                $_POST["email"],
                $setor,
                $_POST["dataDemissao"],
                $_POST["idUsuario"],
                isset($_POST["admUsuario"]) ? (bool)$_POST["admUsuario"] : false
            );

            $resposta = $this->dao->atualizar($model, (int) $_POST["setor"]);
            if ($resposta) {
                header("Location: /code/epi-app-t3/usuario/lista");
                exit;
            } else {
                include "./view/error.php";
                exit;
            }
        }
    }

    public function listaTodosUsuarios(string $requisicao) {
        if ($requisicao === "GET") {
            $respostaBD = $this->dao->listarTodos();
            
            if (!empty($respostaBD)) {
                $respostaCliente = $this->converterListaUsuario($respostaBD);                
                $_SESSION['listaUsuario'] = $respostaCliente;
                include "./view/usuario/listaUsuario.php";
            } else {                
                $_SESSION['listaUsuario'] = [];
                include "./view/usuario/listaUsuario.php";
            }
        }
    }

    public function excluirUsuario(string $requisicao) {
        if ($requisicao === "GET") {
            $resposta = $this->dao->excluir(isset($_GET['idUsuario']) ? (int) $_GET['idUsuario'] : 0);
            if ($resposta) {
                header("Location: /code/epi-app-t3/usuario/lista");
                exit;
            }
        }
    }

    public function converterListaUsuario(array $lista) {
        $novaLista = [];
        foreach ($lista as $u) {
            $setor = new Setor($u["nome_setor"], $u["id_setor"]);
            $model = new Usuario(
                $u['nome_usuario'],
                $u['sobrenome_usuario'],
                $u['matricula_usuario'],
                $u['telefone_usuario'],
                $u['cargo_usuario'],
                $u['data_admissao_usuario'],
                $u['cpf_usuario'],
                $u['senha_usuario'],
                $u['email_usuario'],
                $setor,
                $u['data_demissao_usuario'] ?? null,
                (int) $u['id_usuario'],
                (bool) ($u['adm_usuario'] ?? false)
            );
            $novaLista[] = $model;
        }
        return $novaLista;
    }

    public function login(string $requisicao) {
        if ($requisicao === 'GET') {
            include "./view/usuario/login.php";
            return;
        }

        if ($requisicao === 'POST') {
            $matricula = trim($_POST['matricula'] ?? '');
            $senha = $_POST['senha'] ?? '';

            if (empty($matricula) || empty($senha)) {
                $_SESSION['loginError'] = 'Preencha matrícula e senha.';
                include "./view/usuario/login.php";
                return;
            }

            $usuario = $this->dao->buscarPorMatricula($matricula);
            if (!$usuario) {
                $_SESSION['loginError'] = 'Usuário não encontrado.';
                include "./view/usuario/login.php";
                return;
            }

            // Não há autenticação complexa — comparar senha em texto conforme pedido
            if ($usuario->getSenha() !== $senha) {
                $_SESSION['loginError'] = 'Senha incorreta.';
                include "./view/usuario/login.php";
                return;
            }

            // Login simples: gravar usuário na sessão
            $_SESSION['usuarioLogado'] = $usuario;
            
            // Redirecionar baseado no tipo de usuário
            if ($usuario->getAdmUsuario()) {
                header('Location: /code/epi-app-t3/');
            } else {
                header('Location: /code/epi-app-t3/funcionario/home');
            }
            exit;
        }
    }

    public function logout(string $requisicao) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['usuarioLogado']);
        header('Location: /code/epi-app-t3/');
        exit;
    }

    public function recuperarSenha(string $requisicao) {
        if ($requisicao === "GET") {
            include "./view/usuario/esqueci-senha.php";
        }

        if ($requisicao === "POST") {
            $email = trim($_POST['email'] ?? '');
            $matricula = trim($_POST['matricula'] ?? '');

            if (empty($email) || empty($matricula)) {
                $_SESSION['errorMessage'] = 'Preencha todos os campos.';
                include "./view/usuario/esqueci-senha.php";
                return;
            }

            $usuario = $this->dao->buscarPorMatricula($matricula);
            
            if (!$usuario) {
                $_SESSION['errorMessage'] = 'Matrícula não encontrada.';
                include "./view/usuario/esqueci-senha.php";
                return;
            }

            if (strtolower($usuario->getEmail()) !== strtolower($email)) {
                $_SESSION['errorMessage'] = 'E-mail não corresponde ao cadastrado.';
                include "./view/usuario/esqueci-senha.php";
                return;
            }

            // Gerar nova senha temporária
            $novaSenha = $this->gerarSenhaTemporaria();
            
            // Atualizar senha no banco
            $usuario->setSenha($novaSenha);
            $idSetor = $usuario->getSetor() ? $usuario->getSetor()->getId() : 0;
            $atualizado = $this->dao->atualizar($usuario, $idSetor);

            if ($atualizado) {
                $_SESSION['successMessage'] = "Sua senha foi redefinida. Nova senha: $novaSenha (Guarde-a em local seguro!)";
            } else {
                $_SESSION['errorMessage'] = 'Erro ao redefinir senha. Tente novamente.';
            }
            
            include "./view/usuario/esqueci-senha.php";
        }
    }

    private function gerarSenhaTemporaria(): string {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%';
        $senha = '';
        for ($i = 0; $i < 8; $i++) {
            $senha .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        return $senha;
    }

}
