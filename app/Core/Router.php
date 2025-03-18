<?php
namespace App\Core;

class Router
{
    protected $routes = [];
    protected $params = [];
    
    /**
     * Adiciona uma rota ao router
     */
    public function add($route, $controller, $action, $method = 'GET')
    {
        // Lidar especificamente com a rota raiz
        if ($route === '') {
            $route = '/';
        }
        
        // Converter a rota para um padrão de expressão regular
        $route_regex = $this->routeToRegex($route);
        
        // Adicionar a rota e seus parâmetros
        $this->routes[$route_regex] = [
            'controller' => $controller,
            'action' => $action,
            'method' => $method,
            'original' => $route
        ];
    }
    
    /**
     * Converte uma rota em expressão regular
     */
    private function routeToRegex($route)
    {
        if ($route === '/') {
            return '/^\\/?$/i';
        }
        
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-_]+)', $route);
        return '/^' . $route . '$/i';
    }
    
    /**
     * Verifica se uma URL corresponde a alguma rota
     */
    public function match($url)
    {
        // Normalizar URL vazia para '/'
        if ($url === '') {
            $url = '/';
        }
        
        // Remover barra final (exceto para raiz)
        if ($url !== '/' && substr($url, -1) === '/') {
            $url = rtrim($url, '/');
        }
        
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                // Adicionar parâmetros capturados pela expressão regular
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                
                $this->params = $params;
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Despacha a requisição para o controlador correto
     */
    public function dispatch($url)
    {
        if (DEBUG) {
            echo "URL: " . $url . "<br>";
            echo "Método: " . $_SERVER['REQUEST_METHOD'] . "<br>";
            echo "Rotas disponíveis: <pre>";
            print_r($this->routes);
            echo "</pre>";
        }
        // Debug para desenvolvimento (removido na produção)
        $debug = defined('DEBUG') && DEBUG;
        
        // Normalizar URL vazia para '/'
        if (empty($url) || $url === '') {
            $url = '/';
        }
        
        // Remover query string
        $url = $this->removeQueryStringVariables($url);
        
        // Remover barra final (exceto para raiz)
        if ($url !== '/' && substr($url, -1) === '/') {
            $url = rtrim($url, '/');
        }
        
        // Esta parte só é executada no modo de debug
        if ($debug) {
            echo '<div style="padding: 10px; margin: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">';
            echo '<h3>Router Debug</h3>';
            echo '<p><strong>URL recebida:</strong> ' . htmlspecialchars($url) . '</p>';
            echo '<p><strong>Método HTTP:</strong> ' . $_SERVER['REQUEST_METHOD'] . '</p>';
            echo '<p><strong>Rotas disponíveis:</strong></p>';
            echo '<ul>';
            foreach ($this->routes as $route => $params) {
                echo '<li><strong>' . htmlspecialchars($params['original'] ?? 'unknown') . '</strong> => ' 
                     . htmlspecialchars($params['controller'] . '@' . $params['action']) 
                     . ' [' . $params['method'] . ']</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $action = $this->params['action'];
            $method = $this->params['method'];
            
            // Verificar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== $method) {
                header('HTTP/1.1 405 Method Not Allowed');
                echo "Método HTTP não permitido. Esperado: $method, recebido: " . $_SERVER['REQUEST_METHOD'];
                return;
            }
            
            $controllerName = "App\\Controllers\\{$controller}";
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                
                if (method_exists($controller, $action)) {
                    // Remover parâmetros que não são da rota
                    unset($this->params['controller']);
                    unset($this->params['action']);
                    unset($this->params['method']);
                    unset($this->params['original']);
                    
                    call_user_func_array([$controller, $action], $this->params);
                } else {
                    header('HTTP/1.1 404 Not Found');
                    echo "Método '{$action}' não encontrado no controlador '{$controllerName}'";
                }
            } else {
                header('HTTP/1.1 404 Not Found');
                echo "Controlador '{$controllerName}' não encontrado";
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            echo "Rota não encontrada: {$url}";
            
            // Depuração detalhada (apenas no modo DEBUG)
            if ($debug) {
                echo '<div style="padding: 10px; margin: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px;">';
                echo '<h3>Detalhes de Depuração</h3>';
                
                // Exibir todas as rotas
                echo '<h4>Rotas Registradas:</h4>';
                echo '<ul>';
                foreach ($this->routes as $pattern => $routeInfo) {
                    echo '<li>';
                    echo '<strong>Padrão:</strong> ' . htmlspecialchars($pattern) . '<br>';
                    echo '<strong>Rota Original:</strong> ' . htmlspecialchars($routeInfo['original'] ?? 'N/A') . '<br>';
                    echo '<strong>Controller:</strong> ' . htmlspecialchars($routeInfo['controller']) . '<br>';
                    echo '<strong>Action:</strong> ' . htmlspecialchars($routeInfo['action']) . '<br>';
                    echo '<strong>Método HTTP:</strong> ' . htmlspecialchars($routeInfo['method']);
                    echo '</li>';
                }
                echo '</ul>';
                
                // Exibir tentativa de correspondência
                echo '<h4>Tentativa de Correspondência:</h4>';
                echo '<p><strong>URL Normalizada:</strong> ' . htmlspecialchars($url) . '</p>';
                
                // Fazer teste manual de cada rota
                echo '<h4>Teste Manual de Rotas:</h4>';
                echo '<ul>';
                foreach ($this->routes as $pattern => $routeInfo) {
                    $matches = [];
                    $result = preg_match($pattern, $url, $matches);
                    echo '<li>';
                    echo '<strong>Padrão:</strong> ' . htmlspecialchars($pattern) . '<br>';
                    echo '<strong>Resultado:</strong> ' . ($result ? 'Correspondência encontrada' : 'Sem correspondência') . '<br>';
                    if ($result && count($matches) > 0) {
                        echo '<strong>Captures:</strong><pre>' . htmlspecialchars(print_r($matches, true)) . '</pre>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
                
                echo '</div>';
            }
        }
    }
    
    /**
     * Remove variáveis da query string da URL
     */
    protected function removeQueryStringVariables($url)
    {
        if ($url !== '') {
            $parts = explode('?', $url, 2);
            $url = $parts[0];
        }
        
        return $url;
    }
    
    /**
     * Obtém os parâmetros da rota atual
     */
    public function getParams()
    {
        return $this->params;
    }
    
    /**
     * Obtém todas as rotas registradas
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}