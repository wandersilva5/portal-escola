<?php
namespace App\Helpers;

use App\Core\Session;

class AuthHelper
{
    /**
     * Verifica se o usuário está autenticado
     */
    public static function isAuthenticated()
    {
        return Session::has('usuario_id');
    }
    
    /**
     * Retorna o ID do usuário autenticado
     */
    public static function getUserId()
    {
        return Session::get('usuario_id');
    }
    
    /**
     * Retorna os dados do usuário autenticado
     */
    public static function getUser()
    {
        return Session::get('usuario');
    }
    
    /**
     * Retorna o ID da instituição do usuário autenticado
     */
    public static function getInstituicaoId()
    {
        return Session::get('instituicao_id');
    }
    
    /**
     * Retorna os dados da instituição do usuário autenticado
     */
    public static function getInstituicao()
    {
        return Session::get('instituicao');
    }
    
    /**
     * Verifica se o usuário tem um determinado tipo
     */
    public static function hasRole($role)
    {
        $user = self::getUser();
        return $user && $user['tipo'] === $role;
    }
    
    /**
     * Verifica se o usuário é administrador
     */
    public static function isAdmin()
    {
        return self::hasRole('admin');
    }
    
    /**
     * Verifica se o usuário é administrador da instituição
     */
    public static function isInstituicaoAdmin()
    {
        return self::hasRole('instituicao_admin');
    }
}