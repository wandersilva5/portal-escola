<?php
namespace App\Core;

class Application
{
    protected $router;
    
    public function __construct()
    {
        // Iniciar sessão
        Session::start();
        
        // Iniciar o Router
        $this->router = new Router();
    }
    
    /**
     * Executa a aplicação
     */
    public function run()
    {
        // Obter a URI atual
        $url = $this->getRequestUrl();
        
        // Carregar as rotas (passando a instância do aplicativo)
        require_once dirname(__DIR__, 2) . '/config/routes.php';
        
        // Despachar a rota correspondente
        $this->router->dispatch($url);
    }
    
    /**
     * Obtém a URL requisitada
     */
    protected function getRequestUrl()
    {
        // Obter o caminho da URL
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Se for nulo ou vazio, retornar '/'
        if (empty($uri)) {
            return '/';
        }
        
        return $uri;
    }
    
    /**
     * Retorna a instância do Router
     */
    public function getRouter()
    {
        return $this->router;
    }
}