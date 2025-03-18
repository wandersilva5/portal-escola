<?php
namespace App\Controllers;

use App\Core\Session;
use App\Helpers\AuthHelper;
use App\Helpers\ValidationHelper;
use App\Models\Institution;
use App\Models\User;

class UserController extends BaseController
{
    protected $usuarioModel;
    protected $instituicaoModel;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->usuarioModel = new User();
        $this->instituicaoModel = new Institution();
        
        // Verificar permissões - apenas admin e instituicao_admin podem acessar
        if (!AuthHelper::isAdmin() && !AuthHelper::isInstituicaoAdmin()) {
            Session::set('error', 'Você não tem permissão para acessar esta área.');
            $this->redirect('/dashboard');
        }
    }
    
    /**
     * Lista todos os usuários
     */
    public function index()
    {
        // Parâmetros de paginação e busca
        $pagina = $_GET['pagina'] ?? 1;
        $porPagina = 10;
        $busca = $_GET['busca'] ?? '';
        $filtroTipo = $_GET['tipo'] ?? '';
        
        // Configurar filtros
        $where = '';
        $params = [];
        
        // Filtrar por instituição se for admin de instituição
        if (!AuthHelper::isAdmin()) {
            $instituicaoId = AuthHelper::getInstituicaoId();
            $where .= "instituicao_id = :instituicao_id";
            $params['instituicao_id'] = $instituicaoId;
        }
        
        // Filtrar por tipo
        if (!empty($filtroTipo)) {
            $where .= !empty($where) ? " AND " : "";
            $where .= "tipo = :tipo";
            $params['tipo'] = $filtroTipo;
        }
        
        // Filtrar por busca
        if (!empty($busca)) {
            $where .= !empty($where) ? " AND " : "";
            $where .= "(nome LIKE :busca OR email LIKE :busca)";
            $params['busca'] = "%{$busca}%";
        }
        
        // Obter usuários
        if (!empty($where)) {
            $usuarios = $this->usuarioModel->where($where, $params);
            $total = count($usuarios);
            
            // Aplicar paginação manualmente
            $offset = ($pagina - 1) * $porPagina;
            $usuarios = array_slice($usuarios, $offset, $porPagina);
        } else {
            // Se for admin, pode ver todos os usuários
            if (AuthHelper::isAdmin()) {
                $usuarios = $this->usuarioModel->paginate($pagina, $porPagina);
                $total = $this->usuarioModel->count();
            } else {
                // Se for admin de instituição, ver apenas da sua instituição
                $instituicaoId = AuthHelper::getInstituicaoId();
                $usuarios = $this->usuarioModel->where("instituicao_id = :instituicao_id", ['instituicao_id' => $instituicaoId]);
                $total = count($usuarios);
                
                // Aplicar paginação manualmente
                $offset = ($pagina - 1) * $porPagina;
                $usuarios = array_slice($usuarios, $offset, $porPagina);
            }
        }
        
        // Configurar paginação
        $totalPaginas = ceil($total / $porPagina);
        $inicio = ($pagina - 1) * $porPagina + 1;
        $fim = min($inicio + $porPagina - 1, $total);
        
        $paginacao = [
            'pagina_atual' => (int)$pagina,
            'total_paginas' => $totalPaginas,
            'por_pagina' => $porPagina,
            'total' => $total,
            'inicio' => $inicio,
            'fim' => $fim
        ];
        
        // Obter instituições para o filtro (apenas para admin)
        $instituicoes = [];
        if (AuthHelper::isAdmin()) {
            $instituicoes = $this->instituicaoModel->all();
        }
        
        // Renderizar view
        $this->render('users/index.php', [
            'titulo' => 'Usuários',
            'usuarios' => $usuarios,
            'paginacao' => $paginacao,
            'busca' => $busca,
            'filtroTipo' => $filtroTipo,
            'instituicoes' => $instituicoes,
            'tipos' => [
                'admin' => 'Administrador',
                'instituicao_admin' => 'Admin. Instituição',
                'professor' => 'Professor',
                'aluno' => 'Aluno',
                'responsavel' => 'Responsável'
            ]
        ]);
    }
    
    /**
     * Mostra formulário para criar novo usuário
     */
    public function create()
    {
        // Obter instituições para o select (apenas para admin)
        $instituicoes = [];
        if (AuthHelper::isAdmin()) {
            $instituicoes = $this->instituicaoModel->all();
        } else {
            $instituicaoId = AuthHelper::getInstituicaoId();
            $instituicoes = [$this->instituicaoModel->find($instituicaoId)];
        }
        
        $this->render('users/create.php', [
            'titulo' => 'Novo Usuário',
            'instituicoes' => $instituicoes,
            'tipos' => [
                'admin' => 'Administrador',
                'instituicao_admin' => 'Admin. Instituição',
                'professor' => 'Professor',
                'aluno' => 'Aluno',
                'responsavel' => 'Responsável'
            ]
        ]);
    }
    
    /**
     * Processa o formulário de criação
     */
    public function store()
    {
        // Validar inputs
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $instituicaoId = $_POST['instituicao_id'] ?? AuthHelper::getInstituicaoId();
        $status = $_POST['status'] ?? 'ativo';
        
        // Validação básica
        ValidationHelper::required($nome, 'nome');
        ValidationHelper::required($email, 'email');
        ValidationHelper::email($email, 'email');
        ValidationHelper::required($senha, 'senha');
        ValidationHelper::minLength($senha, 'senha', 6);
        ValidationHelper::matches($senha, 'senha', $confirmarSenha, 'confirmar_senha');
        ValidationHelper::required($tipo, 'tipo');
        
        // Admin de instituição só pode criar usuários para sua instituição
        if (!AuthHelper::isAdmin() && $instituicaoId != AuthHelper::getInstituicaoId()) {
            Session::set('error', 'Você não tem permissão para criar usuários para outras instituições.');
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/novo');
        }
        
        // Admin de instituição não pode criar usuários admin
        if (!AuthHelper::isAdmin() && $tipo === 'admin') {
            Session::set('error', 'Você não tem permissão para criar administradores do sistema.');
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/novo');
        }
        
        // Verificar se email já existe
        $usuarioExistente = $this->usuarioModel->findWhere('email = :email', ['email' => $email]);
        if ($usuarioExistente) {
            Session::set('error', 'Este e-mail já está cadastrado no sistema.');
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/novo');
        }
        
        // Se houver erros, retorna para o formulário
        if (ValidationHelper::hasErrors()) {
            Session::set('error', 'Corrija os erros no formulário.');
            Session::set('form_errors', ValidationHelper::getErrors());
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/novo');
        }
        
        // Upload da foto, se houver
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/fotos/';
            $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fileName)) {
                $foto = '/uploads/fotos/' . $fileName;
            }
        }
        
        // Preparar dados para inserção
        $data = [
            'nome' => $nome,
            'email' => $email,
            'senha' => $senha, // Será hasheado no modelo
            'tipo' => $tipo,
            'telefone' => $telefone,
            'instituicao_id' => $instituicaoId,
            'status' => $status,
            'foto' => $foto,
            'ultimo_acesso' => null
        ];
        
        // Inserir no banco
        $usuarioId = $this->usuarioModel->createUser($data);
        
        if ($usuarioId) {
            Session::set('success', 'Usuário criado com sucesso!');
            $this->redirect('/usuarios/' . $usuarioId);
        } else {
            Session::set('error', 'Erro ao criar usuário. Tente novamente.');
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/novo');
        }
    }
    
    /**
     * Exibe detalhes de um usuário
     */
    public function show($id)
    {
        $usuario = $this->usuarioModel->find($id);
        
        if (!$usuario) {
            Session::set('error', 'Usuário não encontrado.');
            $this->redirect('/usuarios');
        }
        
        // Verificar permissão (admin pode ver todos, instituicao_admin apenas de sua instituição)
        if (!AuthHelper::isAdmin() && $usuario['instituicao_id'] != AuthHelper::getInstituicaoId()) {
            Session::set('error', 'Você não tem permissão para visualizar este usuário.');
            $this->redirect('/usuarios');
        }
        
        // Obter dados da instituição
        $instituicao = $this->instituicaoModel->find($usuario['instituicao_id']);
        
        $this->render('users/show.php', [
            'titulo' => $usuario['nome'],
            'usuario' => $usuario,
            'instituicao' => $instituicao,
            'tipos' => [
                'admin' => 'Administrador',
                'instituicao_admin' => 'Admin. Instituição',
                'professor' => 'Professor',
                'aluno' => 'Aluno',
                'responsavel' => 'Responsável'
            ]
        ]);
    }
    
    /**
     * Exibe formulário para editar usuário
     */
    public function edit($id)
    {
        $usuario = $this->usuarioModel->find($id);
        
        if (!$usuario) {
            Session::set('error', 'Usuário não encontrado.');
            $this->redirect('/usuarios');
        }
        
        // Verificar permissão (admin pode editar todos, instituicao_admin apenas de sua instituição)
        if (!AuthHelper::isAdmin() && $usuario['instituicao_id'] != AuthHelper::getInstituicaoId()) {
            Session::set('error', 'Você não tem permissão para editar este usuário.');
            $this->redirect('/usuarios');
        }
        
        // Obter instituições para o select (apenas para admin)
        $instituicoes = [];
        if (AuthHelper::isAdmin()) {
            $instituicoes = $this->instituicaoModel->all();
        } else {
            $instituicaoId = AuthHelper::getInstituicaoId();
            $instituicoes = [$this->instituicaoModel->find($instituicaoId)];
        }
        
        $this->render('users/edit.php', [
            'titulo' => 'Editar Usuário',
            'usuario' => $usuario,
            'instituicoes' => $instituicoes,
            'tipos' => [
                'admin' => 'Administrador',
                'instituicao_admin' => 'Admin. Instituição',
                'professor' => 'Professor',
                'aluno' => 'Aluno',
                'responsavel' => 'Responsável'
            ]
        ]);
    }
    
    /**
     * Processa o formulário de edição
     */
    public function update($id)
    {
        $usuario = $this->usuarioModel->find($id);
        
        if (!$usuario) {
            Session::set('error', 'Usuário não encontrado.');
            $this->redirect('/usuarios');
        }
        
        // Verificar permissão (admin pode editar todos, instituicao_admin apenas de sua instituição)
        if (!AuthHelper::isAdmin() && $usuario['instituicao_id'] != AuthHelper::getInstituicaoId()) {
            Session::set('error', 'Você não tem permissão para editar este usuário.');
            $this->redirect('/usuarios');
        }
        
        // Validar inputs
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        $confirmarSenha = $_POST['confirmar_senha'] ?? '';
        $tipo = $_POST['tipo'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $instituicaoId = $_POST['instituicao_id'] ?? $usuario['instituicao_id'];
        $status = $_POST['status'] ?? 'ativo';
        
        // Validação básica
        ValidationHelper::required($nome, 'nome');
        ValidationHelper::required($email, 'email');
        ValidationHelper::email($email, 'email');
        
        // Validar senha apenas se foi informada
        if (!empty($senha)) {
            ValidationHelper::minLength($senha, 'senha', 6);
            ValidationHelper::matches($senha, 'senha', $confirmarSenha, 'confirmar_senha');
        }
        
        ValidationHelper::required($tipo, 'tipo');
        
        // Admin de instituição só pode alterar usuários para sua instituição
        if (!AuthHelper::isAdmin() && $instituicaoId != AuthHelper::getInstituicaoId()) {
            Session::set('error', 'Você não tem permissão para alterar a instituição do usuário.');
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/' . $id . '/editar');
        }
        
        // Admin de instituição não pode alterar usuários para admin
        if (!AuthHelper::isAdmin() && $tipo === 'admin') {
            Session::set('error', 'Você não tem permissão para criar administradores do sistema.');
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/' . $id . '/editar');
        }
        
        // Verificar se email já existe (exceto para o próprio usuário)
        $usuarioExistente = $this->usuarioModel->findWhere('email = :email AND id != :id', ['email' => $email, 'id' => $id]);
        if ($usuarioExistente) {
            Session::set('error', 'Este e-mail já está cadastrado no sistema.');
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/' . $id . '/editar');
        }
        
        // Se houver erros, retorna para o formulário
        if (ValidationHelper::hasErrors()) {
            Session::set('error', 'Corrija os erros no formulário.');
            Session::set('form_errors', ValidationHelper::getErrors());
            Session::set('form_data', $_POST);
            $this->redirect('/usuarios/' . $id . '/editar');
        }
        
        // Upload da foto, se houver
        $foto = $usuario['foto'];
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/fotos/';
            $fileName = uniqid() . '_' . basename($_FILES['foto']['name']);
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fileName)) {
                // Remover foto antiga, se existir
                if (!empty($usuario['foto']) && file_exists(dirname(__DIR__, 2) . '/public' . $usuario['foto'])) {
                    unlink(dirname(__DIR__, 2) . '/public' . $usuario['foto']);
                }
                
                $foto = '/uploads/fotos/' . $fileName;
            }
        }
        
        // Preparar dados para atualização
        $data = [
            'nome' => $nome,
            'email' => $email,
            'tipo' => $tipo,
            'telefone' => $telefone,
            'instituicao_id' => $instituicaoId,
            'status' => $status,
            'foto' => $foto
        ];
        
        // Adicionar senha apenas se foi informada
        if (!empty($senha)) {
            $data['senha'] = $senha; // Será hasheado no modelo
        }
        
        // Atualizar no banco
        $atualizado = $this->usuarioModel->updateUser($id, $data);
        
        if ($atualizado) {
            Session::set('success', 'Usuário atualizado com sucesso!');
            $this->redirect('/usuarios/' . $id);
        } else {
            Session::set('error', 'Erro ao atualizar usuário. Tente novamente.');
            $this->redirect('/usuarios/' . $id . '/editar');
        }
    }
    
    /**
     * Exclui um usuário
     */
    public function delete($id)
    {
        $usuario = $this->usuarioModel->find($id);
        
        if (!$usuario) {
            Session::set('error', 'Usuário não encontrado.');
            $this->redirect('/usuarios');
        }
        
        // Verificar permissão (admin pode excluir todos, instituicao_admin apenas de sua instituição)
        if (!AuthHelper::isAdmin() && $usuario['instituicao_id'] != AuthHelper::getInstituicaoId()) {
            Session::set('error', 'Você não tem permissão para excluir este usuário.');
            $this->redirect('/usuarios');
        }
        
        // Não permitir excluir o próprio usuário
        if ($usuario['id'] == AuthHelper::getUserId()) {
            Session::set('error', 'Você não pode excluir seu próprio usuário.');
            $this->redirect('/usuarios');
        }
        
        // Excluir o usuário
        $excluido = $this->usuarioModel->delete($id);
        
        if ($excluido) {
            // Remover foto, se existir
            if (!empty($usuario['foto']) && file_exists(dirname(__DIR__, 2) . '/public' . $usuario['foto'])) {
                unlink(dirname(__DIR__, 2) . '/public' . $usuario['foto']);
            }
            
            Session::set('success', 'Usuário excluído com sucesso!');
        } else {
            Session::set('error', 'Erro ao excluir usuário. Tente novamente.');
        }
        
        $this->redirect('/usuarios');
    }
}