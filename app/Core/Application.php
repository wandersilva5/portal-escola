<?php

namespace App\Core;

class Application
{
    protected $router;
    private static $hasRun = false;

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
        if (self::$hasRun) {
            return;
        }
        self::$hasRun = true;

        // Obter a URI atual
        $url = $this->getRequestUrl();

        // Adicionar debug temporário
        echo "Application::run()<br>";
        echo "URL: " . $url . "<br>";
        echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "<br>";

        // Carregar as rotas (passando a instância do aplicativo)
        require_once dirname(__DIR__, 2) . '/config/routes.php';

        // Debug para mostrar rotas registradas
        echo "Rotas registradas:<pre>";
        print_r($this->router->getRoutes());
        echo "</pre>";

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
