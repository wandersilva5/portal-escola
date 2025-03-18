<?php
/**
 * Portal Escolar - Ponto de entrada da aplicação
 * 
 * Este arquivo inicializa a aplicação, carrega as configurações
 * e direciona a requisição para o controlador correto.
 */

// Definir constantes
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));

// Implementação simples de autoloader para classes
spl_autoload_register(function($className) {
    // Converter namespace separators para directory separators
    $className = str_replace('\\', DS, $className);
    $filePath = ROOT . DS . $className . '.php';
    
    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }
    return false;
});

// Carregar variáveis de ambiente manualmente se o arquivo existir
function loadEnv() {
    $envFile = ROOT . DS . '.env';
    if (file_exists($envFile)) {
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Ignorar comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Processar variáveis de ambiente
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remover aspas do valor se houver
            if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            }
            
            // Substituir ${VAR} com valores já definidos
            if (preg_match('/\${([A-Za-z0-9_]+)}/', $value, $matches)) {
                $varName = $matches[1];
                if (isset($_ENV[$varName])) {
                    $value = str_replace('${' . $varName . '}', $_ENV[$varName], $value);
                }
            }
            
            $_ENV[$name] = $value;
            putenv("$name=$value");
        }
    }
}

loadEnv();

// Configurar tratamento de erros
error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? false);

// Iniciar sessão
session_start();

// Inicializar aplicação
$app = new App\Core\Application();

// Executar aplicação
$app->run();