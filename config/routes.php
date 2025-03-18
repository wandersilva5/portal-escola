<?php
/**
 * Definição de rotas do sistema
 * 
 * Este arquivo é incluído pelo Application.php
 */

// Obter instância do Router diretamente da instância atual
$router = $this->getRouter();

// Rotas de autenticação
// Rotas de autenticação
$router->add('/', 'AuthController', 'login', 'GET');
$router->add('login', 'AuthController', 'login', 'GET');
$router->add('auth/login', 'AuthController', 'authenticate', 'POST'); // Caminho diferente para o POST
$router->add('logout', 'AuthController', 'logout', 'GET');
$router->add('recuperar-senha', 'AuthController', 'recuperarSenha', 'GET');
$router->add('recuperar-senha', 'AuthController', 'processarRecuperacao', 'POST');

// Rotas do dashboard
$router->add('dashboard', 'DashboardController', 'index', 'GET');

// Rotas de instituição
$router->add('instituicao', 'InstituicaoController', 'index', 'GET');
$router->add('instituicao/nova', 'InstituicaoController', 'create', 'GET');
$router->add('instituicao/nova', 'InstituicaoController', 'store', 'POST');
$router->add('instituicao/{id}', 'InstituicaoController', 'show', 'GET');
$router->add('instituicao/{id}/editar', 'InstituicaoController', 'edit', 'GET');
$router->add('instituicao/{id}/editar', 'InstituicaoController', 'update', 'POST');
$router->add('instituicao/{id}/excluir', 'InstituicaoController', 'delete', 'POST');

// Rotas de usuários
$router->add('usuarios', 'UsuarioController', 'index', 'GET');
$router->add('usuarios/novo', 'UsuarioController', 'create', 'GET');
$router->add('usuarios/novo', 'UsuarioController', 'store', 'POST');
$router->add('usuarios/{id}', 'UsuarioController', 'show', 'GET');
$router->add('usuarios/{id}/editar', 'UsuarioController', 'edit', 'GET');
$router->add('usuarios/{id}/editar', 'UsuarioController', 'update', 'POST');
$router->add('usuarios/{id}/excluir', 'UsuarioController', 'delete', 'POST');