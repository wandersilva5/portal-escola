<?php
namespace App\Controllers;

use App\Core\Session;

abstract class BaseController
{
    protected $data = [];
    protected $viewPath = '../app/Views/';
    protected $layout = 'layouts/admin.php';
    
    public function __construct()
    {
        // Verificar se o usuário está autenticado, exceto para o AuthController
        if (!(get_class($this) === 'App\\Controllers\\AuthController')) {
            $this->requireAuth();
        }
    }
    
    /**
     * Verifica se o usuário está autenticado
     */
    protected function requireAuth()
    {
        if (!Session::get('usuario_id')) {
            header('Location: /login');
            exit;
        }
        
        // Verificar permissões por instituição
        $instituicaoId = Session::get('instituicao_id');
        if (!$instituicaoId) {
            header('Location: /login?error=sem-instituicao');
            exit;
        }
        
        // Adicionar dados do usuário e instituição ao template
        $this->data['usuario'] = Session::get('usuario');
        $this->data['instituicao'] = Session::get('instituicao');
    }
    
    /**
     * Renderiza a view dentro do layout
     */
    protected function render($view, $data = [])
    {
        // Mesclar dados
        $this->data = array_merge($this->data, $data);
        
        // Extrair variáveis para uso na view
        extract($this->data);
        
        // Carregar conteúdo da view específica
        ob_start();
        require $this->viewPath . $view;
        $content = ob_get_clean();
        
        // Renderizar dentro do layout
        require $this->viewPath . $this->layout;
    }
    
    /**
     * Renderiza apenas a view, sem layout
     */
    protected function renderPartial($view, $data = [])
    {
        // Mesclar dados
        $viewData = array_merge($this->data, $data);
        
        // Extrair variáveis para uso na view
        extract($viewData);
        
        // Carregar conteúdo da view
        require $this->viewPath . $view;
    }
    
    /**
     * Redireciona para a URL específica
     */
    protected function redirect($url)
    {
        header("Location: $url");
        exit;
    }
    
    /**
     * Retorna dados em formato JSON
     */
    protected function json($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}