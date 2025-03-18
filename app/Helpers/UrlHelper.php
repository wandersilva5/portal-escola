<?php

namespace App\Helpers;

class UrlHelper
{
    private static $baseUrl;

    public static function init()
    {
        // Detecta se está usando HTTPS
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        
        // Pega o hostname
        $host = $_SERVER['HTTP_HOST'];
        
        // Define a baseUrl
        self::$baseUrl = $protocol . $host;
    }

    public static function baseUrl($path = '')
    {
        if (!isset(self::$baseUrl)) {
            self::init();
        }
        
        // Remove barras duplicadas e adiciona uma única barra no início do path
        $path = trim($path, '/');
        return self::$baseUrl . ($path ? '/' . $path : '');
    }
}