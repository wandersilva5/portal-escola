<?php
namespace App\Core;

class Session
{
    /**
     * Inicia a sessão se ainda não estiver iniciada
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Define um valor na sessão
     */
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    /**
     * Obtém um valor da sessão
     */
    public static function get($key, $default = null)
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Remove um valor da sessão
     */
    public static function remove($key)
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }
    
    /**
     * Verifica se um valor existe na sessão
     */
    public static function has($key)
    {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    /**
     * Define uma mensagem flash que será exibida apenas uma vez
     */
    public static function flash($key, $value)
    {
        self::start();
        if (!isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = [];
        }
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Obtém uma mensagem flash e a remove da sessão
     */
    public static function getFlash($key, $default = null)
    {
        self::start();
        if (!isset($_SESSION['_flash'])) {
            return $default;
        }
        
        $value = $_SESSION['_flash'][$key] ?? $default;
        
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
        }
        
        if (empty($_SESSION['_flash'])) {
            unset($_SESSION['_flash']);
        }
        
        return $value;
    }
    
    /**
     * Verifica se existe uma mensagem flash
     */
    public static function hasFlash($key)
    {
        self::start();
        return isset($_SESSION['_flash'][$key]);
    }
    
    /**
     * Retorna todas as mensagens flash e as remove da sessão
     */
    public static function getAllFlash()
    {
        self::start();
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }
    
    /**
     * Armazena valores antigos de formulários
     */
    public static function setOldInput(array $values)
    {
        self::set('_old_input', $values);
    }
    
    /**
     * Obtém valores antigos de formulários
     */
    public static function getOldInput($key = null, $default = null)
    {
        $oldInput = self::get('_old_input', []);
        
        if ($key === null) {
            return $oldInput;
        }
        
        return $oldInput[$key] ?? $default;
    }
    
    /**
     * Armazena erros de validação
     */
    public static function setErrors(array $errors)
    {
        self::set('_errors', $errors);
    }
    
    /**
     * Obtém erros de validação
     */
    public static function getErrors($key = null, $default = [])
    {
        $errors = self::get('_errors', []);
        
        if ($key === null) {
            return $errors;
        }
        
        return $errors[$key] ?? $default;
    }
    
    /**
     * Verifica se existem erros de validação
     */
    public static function hasErrors($key = null)
    {
        $errors = self::get('_errors', []);
        
        if ($key === null) {
            return !empty($errors);
        }
        
        return isset($errors[$key]);
    }
    
    /**
     * Destrói todos os dados da sessão
     */
    public static function destroy()
    {
        self::start();
        session_destroy();
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }
    
    /**
     * Regenera o ID da sessão
     */
    public static function regenerate($deleteOldSession = true)
    {
        self::start();
        return session_regenerate_id($deleteOldSession);
    }
    
    /**
     * Obtém o ID da sessão atual
     */
    public static function id()
    {
        self::start();
        return session_id();
    }
}