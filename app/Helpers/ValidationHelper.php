<?php
namespace App\Helpers;

class ValidationHelper
{
    protected static $errors = [];
    
    /**
     * Verifica se um campo está vazio
     */
    public static function required($value, $field)
    {
        if (empty($value)) {
            self::$errors[$field] = "O campo '{$field}' é obrigatório";
            return false;
        }
        
        return true;
    }
    
    /**
     * Verifica se um valor é um e-mail válido
     */
    public static function email($value, $field)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            self::$errors[$field] = "O campo '{$field}' deve ser um e-mail válido";
            return false;
        }
        
        return true;
    }
    
    /**
     * Verifica o tamanho mínimo de uma string
     */
    public static function minLength($value, $field, $length)
    {
        if (strlen($value) < $length) {
            self::$errors[$field] = "O campo '{$field}' deve ter pelo menos {$length} caracteres";
            return false;
        }
        
        return true;
    }
    
    /**
     * Verifica se o valor contém apenas números
     */
    public static function numeric($value, $field)
    {
        if (!is_numeric($value)) {
            self::$errors[$field] = "O campo '{$field}' deve conter apenas números";
            return false;
        }
        
        return true;
    }
    
    /**
     * Verifica se um valor é igual a outro
     */
    public static function matches($value, $field, $matchValue, $matchField)
    {
        if ($value !== $matchValue) {
            self::$errors[$field] = "O campo '{$field}' deve ser igual ao campo '{$matchField}'";
            return false;
        }
        
        return true;
    }
    
    /**
     * Verifica se um valor é uma data válida
     */
    public static function date($value, $field)
    {
        $date = date_parse($value);
        
        if ($date['error_count'] > 0 || !checkdate($date['month'], $date['day'], $date['year'])) {
            self::$errors[$field] = "O campo '{$field}' deve ser uma data válida";
            return false;
        }
        
        return true;
    }
    
    /**
     * Retorna todos os erros de validação
     */
    public static function getErrors()
    {
        return self::$errors;
    }
    
    /**
     * Limpa todos os erros
     */
    public static function clear()
    {
        self::$errors = [];
    }
    
    /**
     * Verifica se há erros
     */
    public static function hasErrors()
    {
        return !empty(self::$errors);
    }
}