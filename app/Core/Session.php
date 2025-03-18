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
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Obtém uma mensagem flash e a remove da sessão
     */
    public static function getFlash($key, $default = null)
    {
        self::start();
        $value = $_SESSION['_flash'][$key] ?? $default;
        
        if (isset($_SESSION['_flash'][$key])) {
            unset($_SESSION['_flash'][$key]);
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
     * Destrói todos os dados da sessão
     */
    public static function destroy()
    {
        self::start();
        session_destroy();
        $_SESSION = [];
    }
}