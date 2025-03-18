<?php
namespace App\Controllers;

use App\Core\Session;
use App\Models\Usuario;
use App\Models\Instituicao;

class AuthController extends BaseController
{
    protected $usuarioModel;
    protected $instituicaoModel;
    
    public function __construct()
    {
        $this->usuarioModel = new Usuario();
        $this->instituicaoModel = new Instituicao();
        
        // Não chamar o construtor do pai para evitar o redirecionamento
    }
    
    /**
     * Exibe a página de login
     */
    public function login()
    {
        // Se já estiver autenticado, redireciona para o dashboard
        if (Session::get('usuario_id')) {
            $this->redirect('/dashboard');
        }
        
        $this->layout = 'layouts/auth.php';
        $this->render('auth/login.php');
    }
    
    /**
     * Processa a autenticação
     */
    public function authenticate()
    {
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        if (empty($email) || empty($senha)) {
            Session::set('error', 'Por favor, preencha todos os campos.');
            $this->redirect('/login');
        }
        
        $usuario = $this->usuarioModel->authenticate($email, $senha);
        
        if (!$usuario) {
            Session::set('error', 'E-mail ou senha inválidos.');
            $this->redirect('/login');
        }
        
        // Verifica se a instituição está ativa
        $instituicao = $this->instituicaoModel->find($usuario['instituicao_id']);
        
        if (!$instituicao || $instituicao['status'] !== 'ativo') {
            Session::set('error', 'Sua instituição está inativa. Entre em contato com o administrador.');
            $this->redirect('/login');
        }
        
        // Armazena dados na sessão
        Session::set('usuario_id', $usuario['id']);
        Session::set('usuario', $usuario);
        Session::set('instituicao_id', $instituicao['id']);
        Session::set('instituicao', $instituicao);
        
        $this->redirect('/dashboard');
    }
    
    /**
     * Encerra a sessão do usuário
     */
    public function logout()
    {
        Session::destroy();
        $this->redirect('/login');
    }
    
    /**
     * Exibe o formulário de recuperação de senha
     */
    public function recuperarSenha()
    {
        $this->layout = 'layouts/auth.php';
        $this->render('auth/recuperar-senha.php');
    }
    
    /**
     * Processa a solicitação de recuperação de senha
     */
    public function processarRecuperacao()
    {
        $email = $_POST['email'] ?? '';
        
        if (empty($email)) {
            Session::set('error', 'Por favor, informe seu e-mail.');
            $this->redirect('/recuperar-senha');
        }
        
        $usuario = $this->usuarioModel->findWhere('email = :email', ['email' => $email]);
        
        if (!$usuario) {
            Session::set('error', 'E-mail não encontrado.');
            $this->redirect('/recuperar-senha');
        }
        
        // Simulação de envio de e-mail
        // Em um cenário real, aqui seria gerado um token e enviado por e-mail
        
        Session::set('success', 'Um e-mail foi enviado com instruções para redefinir sua senha.');
        $this->redirect('/login');
    }
}