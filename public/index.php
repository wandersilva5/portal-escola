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
define('DEBUG', false); // Ative para desenvolvimento, desative para produção

// Configurar tratamento de erros
error_reporting(E_ALL);
ini_set('display_errors', DEBUG ? 1 : 0);

// Função para tratar erros de maneira amigável
function exibirErro($mensagem, $arquivo = null, $linha = null, $stackTrace = null) {
    echo '<div style="background-color: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px; border: 1px solid #f5c6cb;">';
    echo '<h2 style="margin-top: 0;">Erro</h2>';
    echo '<p><strong>Mensagem:</strong> ' . $mensagem . '</p>';
    
    if ($arquivo) {
        echo '<p><strong>Arquivo:</strong> ' . $arquivo . '</p>';
    }
    
    if ($linha) {
        echo '<p><strong>Linha:</strong> ' . $linha . '</p>';
    }
    
    if ($stackTrace) {
        echo '<h3>Stack Trace:</h3>';
        echo '<pre>' . $stackTrace . '</pre>';
    } else {
        echo '<h3>Stack Trace:</h3>';
        echo '<pre>';
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo '</pre>';
    }
    
    echo '</div>';
}

try {
    // Tentar carregar o autoloader do Composer
    $composerAutoloader = ROOT . DS . 'vendor' . DS . 'autoload.php';
    
    if (file_exists($composerAutoloader)) {
        require_once $composerAutoloader;
    } else {
        throw new Exception("Autoloader do Composer não encontrado. Execute 'composer install' na raiz do projeto.");
    }
    
    // Carregar variáveis de ambiente
    if (class_exists('Dotenv\\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(ROOT);
        $dotenv->safeLoad(); // Usa safeLoad para não lançar exceção se o arquivo não existir
    }
    
    // Iniciar sessão
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Verificar diretórios de upload
    $uploadsDir = ROOT . DS . 'public' . DS . 'uploads';
    $uploadSubDirs = ['logos', 'fotos', 'documentos'];
    
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
        
        foreach ($uploadSubDirs as $subDir) {
            if (!is_dir($uploadsDir . DS . $subDir)) {
                mkdir($uploadsDir . DS . $subDir, 0755, true);
            }
        }
    }
    
    // Inicializar e executar a aplicação
    $app = new App\Core\Application();
    $app->run();
    
} catch (Exception $e) {
    exibirErro($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
} catch (Error $e) {
    exibirErro($e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
}