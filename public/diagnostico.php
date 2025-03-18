<?php
/**
 * Script de diagnóstico do Portal Escolar
 * 
 * Este script verifica as configurações do sistema e ajuda a identificar possíveis problemas.
 */

// Desativar limite de tempo de execução
set_time_limit(0);

// Mostrar todos os erros
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Definir constantes
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__DIR__));

// Função para exibir cabeçalho
function exibirCabecalho($titulo) {
    echo "<h2 style='color:#3498db;margin-top:30px;border-bottom:1px solid #ddd;padding-bottom:10px;'>{$titulo}</h2>";
}

// Função para exibir resultados
function exibirResultado($titulo, $status, $mensagem = '', $sucesso = true) {
    $cor = $sucesso ? '#2ecc71' : '#e74c3c';
    echo "<div style='margin:10px 0;padding:10px;border-radius:3px;border-left:5px solid {$cor};background:#f9f9f9;'>";
    echo "<strong style='color:{$cor}'>{$titulo}:</strong> {$status}";
    if (!empty($mensagem)) {
        echo "<p style='margin:5px 0 0 0;font-size:0.9em;color:#7f8c8d;'>{$mensagem}</p>";
    }
    echo "</div>";
}

// Função para verificar permissões de diretório
function verificarDiretorio($caminho, $nomeDiretorio) {
    $caminho = ROOT . DS . $caminho;
    $existe = is_dir($caminho);
    $permissoes = $existe ? substr(sprintf('%o', fileperms($caminho)), -4) : 'N/A';
    $legivel = $existe ? is_readable($caminho) : false;
    $gravavel = $existe ? is_writable($caminho) : false;
    
    $status = "Existe: " . ($existe ? "Sim" : "Não") . 
              " | Permissões: {$permissoes}" .
              " | Legível: " . ($legivel ? "Sim" : "Não") .
              " | Gravável: " . ($gravavel ? "Sim" : "Não");
              
    $sucesso = $existe && $legivel && $gravavel;
    $mensagem = '';
    
    if (!$existe) {
        $mensagem = "O diretório não existe. Crie-o com: mkdir -p {$caminho}";
    } elseif (!$legivel || !$gravavel) {
        $mensagem = "Ajuste as permissões com: chmod -R 755 {$caminho}";
    }
    
    exibirResultado("Diretório {$nomeDiretorio}", $status, $mensagem, $sucesso);
    
    return $sucesso;
}

// Função para verificar permissões de arquivo
function verificarArquivo($caminho, $nomeArquivo) {
    $caminho = ROOT . DS . $caminho;
    $existe = file_exists($caminho);
    $permissoes = $existe ? substr(sprintf('%o', fileperms($caminho)), -4) : 'N/A';
    $legivel = $existe ? is_readable($caminho) : false;
    $gravavel = $existe ? is_writable($caminho) : false;
    
    $status = "Existe: " . ($existe ? "Sim" : "Não") . 
              " | Permissões: {$permissoes}" .
              " | Legível: " . ($legivel ? "Sim" : "Não") .
              " | Gravável: " . ($gravavel ? "Sim" : "Não");
              
    $sucesso = $existe && $legivel;
    $mensagem = '';
    
    if (!$existe) {
        $mensagem = "O arquivo não existe.";
    } elseif (!$legivel) {
        $mensagem = "Ajuste as permissões com: chmod 644 {$caminho}";
    }
    
    exibirResultado("Arquivo {$nomeArquivo}", $status, $mensagem, $sucesso);
    
    return $sucesso;
}

// Configurar o estilo da página
echo '<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnóstico - Portal Escolar</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            color: #3498db;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        code {
            background: #f4f4f4;
            padding: 2px 5px;
            border-radius: 3px;
            font-family: monospace;
        }
        .section {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Diagnóstico do Portal Escolar</h1>
        <p>Este script verifica as configurações do sistema e ajuda a identificar possíveis problemas.</p>';

// Informações do PHP
exibirCabecalho("Informações do PHP");
exibirResultado("Versão do PHP", phpversion(), "Recomendado: PHP 8.0 ou superior", version_compare(phpversion(), '8.0.0', '>='));
exibirResultado("Extensão PDO MySQL", extension_loaded('pdo_mysql') ? "Habilitada" : "Desabilitada", "Necessária para conexão ao banco de dados", extension_loaded('pdo_mysql'));
exibirResultado("Extensão Mbstring", extension_loaded('mbstring') ? "Habilitada" : "Desabilitada", "Recomendada para manipulação de strings", extension_loaded('mbstring'));
exibirResultado("Extensão JSON", extension_loaded('json') ? "Habilitada" : "Desabilitada", "Necessária para processamento de JSON", extension_loaded('json'));

// Permissões de diretórios
exibirCabecalho("Permissões de Diretórios");
verificarDiretorio('public', 'Public');
verificarDiretorio('public/uploads', 'Uploads');
verificarDiretorio('public/uploads/fotos', 'Fotos');
verificarDiretorio('public/uploads/logos', 'Logos');
verificarDiretorio('public/assets', 'Assets');
verificarDiretorio('app', 'App');
verificarDiretorio('config', 'Config');

// Permissões de arquivos importantes
exibirCabecalho("Arquivos Importantes");
verificarArquivo('public/index.php', 'index.php');
verificarArquivo('config/routes.php', 'routes.php');
verificarArquivo('config/database.php', 'database.php');
verificarArquivo('.env', '.env');
verificarArquivo('.htaccess', '.htaccess');

// Rotas registradas
exibirCabecalho("Rotas Registradas");
try {
    // Simplificação para obter rotas
    require_once ROOT . DS . 'app' . DS . 'Core' . DS . 'Router.php';
    $router = new App\Core\Router();
    
    // Carregar rotas
    $routesFile = ROOT . DS . 'config' . DS . 'routes.php';
    if (file_exists($routesFile)) {
        ob_start();
        require $routesFile;
        ob_end_clean();
        
        // Acessar propriedade routes (mesmo que privada)
        $reflection = new ReflectionClass($router);
        $property = $reflection->getProperty('routes');
        $property->setAccessible(true);
        $routes = $property->getValue($router);
        
        if (empty($routes)) {
            exibirResultado("Rotas", "Nenhuma rota registrada", "Verifique o arquivo routes.php", false);
        } else {
            exibirResultado("Rotas", "Total de rotas: " . count($routes), "As rotas estão registradas corretamente", true);
            
            echo "<div style='margin:10px 0;padding:10px;background:#f9f9f9;border-radius:3px;'>";
            echo "<table style='width:100%;border-collapse:collapse;'>";
            echo "<tr style='background:#3498db;color:white;'><th style='text-align:left;padding:8px;'>Padrão</th><th style='text-align:left;padding:8px;'>Controlador</th><th style='text-align:left;padding:8px;'>Ação</th><th style='text-align:left;padding:8px;'>Método</th></tr>";
            
            $i = 0;
            foreach ($routes as $pattern => $params) {
                $bg = $i % 2 == 0 ? '#f2f2f2' : 'white';
                echo "<tr style='background:{$bg};'>";
                echo "<td style='padding:8px;'>" . htmlspecialchars($pattern) . "</td>";
                echo "<td style='padding:8px;'>" . htmlspecialchars($params['controller']) . "</td>";
                echo "<td style='padding:8px;'>" . htmlspecialchars($params['action']) . "</td>";
                echo "<td style='padding:8px;'>" . htmlspecialchars($params['method']) . "</td>";
                echo "</tr>";
                $i++;
            }
            
            echo "</table>";
            echo "</div>";
        }
    } else {
        exibirResultado("Arquivo de Rotas", "Não encontrado", "O arquivo routes.php não existe", false);
    }
} catch (Exception $e) {
    exibirResultado("Erro ao verificar rotas", $e->getMessage(), "Ocorreu um erro ao tentar verificar as rotas", false);
}

// Verificar conexão com o banco de dados
exibirCabecalho("Banco de Dados");
try {
    $dbConfigFile = ROOT . DS . 'config' . DS . 'database.php';
    if (file_exists($dbConfigFile)) {
        $dbConfig = require $dbConfigFile;
        
        try {
            $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
            
            // Verificar se as tabelas existem
            $tables = ['instituicoes', 'usuarios'];
            $missingTables = [];
            
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
                if ($stmt->rowCount() == 0) {
                    $missingTables[] = $table;
                }
            }
            
            if (empty($missingTables)) {
                exibirResultado("Conexão com o Banco de Dados", "Sucesso", "Conexão estabelecida e tabelas verificadas", true);
            } else {
                exibirResultado("Conexão com o Banco de Dados", "Sucesso, mas tabelas ausentes", "As seguintes tabelas estão faltando: " . implode(', ', $missingTables), false);
            }
            
        } catch (PDOException $e) {
            exibirResultado("Conexão com o Banco de Dados", "Falha", "Erro: " . $e->getMessage(), false);
        }
    } else {
        exibirResultado("Arquivo de Configuração do BD", "Não encontrado", "O arquivo database.php não existe", false);
    }
} catch (Exception $e) {
    exibirResultado("Erro ao verificar banco de dados", $e->getMessage(), "Ocorreu um erro ao tentar verificar o banco de dados", false);
}

// Informações da Requisição
exibirCabecalho("Informações da Requisição");
exibirResultado("URI da Requisição", $_SERVER['REQUEST_URI'] ?? 'N/A');
exibirResultado("Método HTTP", $_SERVER['REQUEST_METHOD'] ?? 'N/A');
exibirResultado("Servidor Web", $_SERVER['SERVER_SOFTWARE'] ?? 'N/A');
exibirResultado("Caminho do Document Root", $_SERVER['DOCUMENT_ROOT'] ?? 'N/A');
exibirResultado("Caminho do Script", $_SERVER['SCRIPT_FILENAME'] ?? 'N/A');

// Finalizar HTML
echo '
    </div>
</body>
</html>';