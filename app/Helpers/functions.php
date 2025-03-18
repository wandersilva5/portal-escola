<?php
/**
 * Portal Escolar - Funções auxiliares globais
 * 
 * Este arquivo contém funções auxiliares que podem ser usadas em todo o sistema.
 */
use App\Helpers\UrlHelper;
if (!function_exists('env')) {
    /**
     * Obtém uma variável de ambiente ou valor padrão
     *
     * @param string $key     Nome da variável
     * @param mixed  $default Valor padrão caso a variável não exista
     * @return mixed
     */
    function env($key, $default = null)
    {
        $value = $_ENV[$key] ?? getenv($key);
        
        if ($value === false) {
            return $default;
        }
        
        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
            case 'empty':
            case '(empty)':
                return '';
        }
        
        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Obtém um valor de configuração
     *
     * @param string $key     Chave de configuração (formato: arquivo.chave.subchave)
     * @param mixed  $default Valor padrão caso a configuração não exista
     * @return mixed
     */
    function config($key, $default = null)
    {
        $parts = explode('.', $key);
        $file = array_shift($parts);
        
        $configFile = dirname(__DIR__, 2) . '/config/' . $file . '.php';
        
        if (!file_exists($configFile)) {
            return $default;
        }
        
        $config = require $configFile;
        
        foreach ($parts as $part) {
            if (!isset($config[$part])) {
                return $default;
            }
            $config = $config[$part];
        }
        
        return $config;
    }
}

if (!function_exists('asset')) {
    /**
     * Gera URL para um asset
     *
     * @param string $path Caminho relativo ao diretório public
     * @return string URL completa do asset
     */
    function asset($path)
    {
        $baseUrl = env('APP_URL', '');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    /**
     * Gera uma URL para uma rota
     *
     * @param string $path Caminho relativo à raiz
     * @return string URL completa
     */
    function url($path = '')
    {
        $baseUrl = env('APP_URL', '');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redireciona para uma URL
     *
     * @param string $url URL para redirecionamento
     * @param int    $code Código HTTP (301, 302, etc)
     */
    function redirect($url, $code = 302)
    {
        header('Location: ' . $url, true, $code);
        exit;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Gera ou obtém um token CSRF
     *
     * @return string Token CSRF
     */
    function csrf_token()
    {
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Gera um campo hidden com o token CSRF
     *
     * @return string HTML do campo
     */
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('old')) {
    /**
     * Obtém um valor antigo do formulário
     *
     * @param string $key     Nome do campo
     * @param mixed  $default Valor padrão
     * @return mixed
     */
    function old($key, $default = '')
    {
        return $_SESSION['_old_input'][$key] ?? $default;
    }
}

if (!function_exists('formatarData')) {
    /**
     * Formata uma data
     *
     * @param string $data    Data no formato Y-m-d ou timestamp
     * @param string $formato Formato desejado
     * @return string
     */
    function formatarData($data, $formato = 'd/m/Y')
    {
        if (empty($data)) {
            return '';
        }
        
        if (is_numeric($data)) {
            return date($formato, $data);
        }
        
        return date($formato, strtotime($data));
    }
}

if (!function_exists('formatarMoeda')) {
    /**
     * Formata um valor em moeda
     *
     * @param float  $valor   Valor a ser formatado
     * @param string $simbolo Símbolo da moeda
     * @return string
     */
    function formatarMoeda($valor, $simbolo = 'R$')
    {
        return $simbolo . ' ' . number_format($valor, 2, ',', '.');
    }
}

if (!function_exists('sanitizeFileName')) {
    /**
     * Sanitiza um nome de arquivo
     *
     * @param string $nome Nome do arquivo
     * @return string
     */
    function sanitizeFileName($nome)
    {
        // Remove caracteres especiais
        $nome = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $nome);
        // Evita duplo underline
        $nome = preg_replace('/_+/', '_', $nome);
        // Limita o tamanho
        $nome = substr($nome, 0, 100);
        
        return $nome;
    }
}

if (!function_exists('debug')) {
    /**
     * Exibe informações de debug
     *
     * @param mixed $var Variável a ser exibida
     * @param bool  $die Se true, encerra a execução
     */
    function debug($var, $die = true)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        
        if ($die) {
            die();
        }
    }
}



if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return UrlHelper::baseUrl($path);
    }
}

