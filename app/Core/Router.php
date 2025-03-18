<?php
namespace App\Core;

class Router
{
    protected $routes = [];
    protected $params = [];
    
    public function add($route, $controller, $action, $method = 'GET')
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z0-9-]+)', $route);
        $route = '/^' . $route . '$/i';
        
        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action,
            'method' => $method
        ];
    }
    
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
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
    
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);
        
        if ($this->match($url)) {
            if ($_SERVER['REQUEST_METHOD'] !== $this->params['method']) {
                header('HTTP/1.1 405 Method Not Allowed');
                echo "Método não permitido";
                return;
            }
            
            $controller = $this->params['controller'];
            $controllerName = "App\\Controllers\\{$controller}";
            
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                
                $action = $this->params['action'];
                
                if (method_exists($controller, $action)) {
                    unset($this->params['controller']);
                    unset($this->params['action']);
                    unset($this->params['method']);
                    
                    call_user_func_array([$controller, $action], $this->params);
                } else {
                    header('HTTP/1.1 404 Not Found');
                    echo "Método {$action} não encontrado no controlador {$controller}";
                }
            } else {
                header('HTTP/1.1 404 Not Found');
                echo "Controlador {$controller} não encontrado";
            }
        } else {
            header('HTTP/1.1 404 Not Found');
            echo "Rota não encontrada";
        }
    }
    
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);
            
            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }
        
        return $url;
    }
}