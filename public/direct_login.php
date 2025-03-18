<?php
// Arquivo temporário para processar login diretamente
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));

// Carregar autoloader
require_once ROOT . DS . 'vendor' . DS . 'autoload.php';

// Carregar variáveis de ambiente
if (class_exists('Dotenv\\Dotenv')) {
    $dotenv = Dotenv\Dotenv::createImmutable(ROOT);
    $dotenv->safeLoad(); // Usa safeLoad para não lançar exceção se o arquivo não existir
}

// Verificar se as variáveis de ambiente foram carregadas
if (!isset($_ENV['DB_HOST']) || !isset($_ENV['DB_NAME']) || !isset($_ENV['DB_USER'])) {
    echo "Erro: Variáveis de ambiente de banco de dados não encontradas! <br>";
    echo "Verifique se o arquivo .env existe na raiz do projeto e contém as configurações de banco de dados.";
    exit;
}

// Iniciar sessão
session_start();

// Importar classes
use App\Core\Session;
use App\Models\User;
use App\Models\Institution;

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        Session::set('error', 'Por favor, preencha todos os campos.');
        header('Location: /login');
        exit;
    }
    
    try {
        // Criar instâncias dos modelos
        $userModel = new User();
        $institutionModel = new Institution();
        
        // Autenticar usuário
        $usuario = $userModel->authenticate($email, $senha);
        
        if (!$usuario) {
            Session::set('error', 'E-mail ou senha inválidos.');
            header('Location: /login');
            exit;
        }
        
        // Verificar instituição
        $instituicao = $institutionModel->find($usuario['institution_id'] ?? $usuario['instituicao_id']);
        
        if (!$instituicao || ($instituicao['status'] !== 'active' && $instituicao['status'] !== 'ativo')) {
            Session::set('error', 'Sua instituição está inativa. Entre em contato com o administrador.');
            header('Location: /login');
            exit;
        }
        
        // Armazenar dados na sessão
        Session::set('usuario_id', $usuario['id']);
        Session::set('usuario', $usuario);
        Session::set('instituicao_id', $instituicao['id']);
        Session::set('instituicao', $instituicao);
        
        header('Location: /dashboard');
        exit;
    } catch (Exception $e) {
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin: 15px; border-radius: 5px;'>";
        echo "<h3>Erro ao processar login:</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<p>File: " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
        echo "<p><a href='/login'>Voltar para o login</a></p>";
        echo "</div>";
        exit;
    }
} else {
    // Se não for POST, redirecionar para a página de login
    header('Location: /login');
    exit;
}