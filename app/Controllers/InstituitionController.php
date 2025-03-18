<?php

namespace App\Controllers;

use App\Core\Session;
use App\Helpers\AuthHelper;
use App\Helpers\ValidationHelper;
use App\Models\Institution;
use App\Models\User;

class InstitutionController extends BaseController
{
    protected $InstitutionModel;
    protected $userModel;

    public function __construct()
    {
        parent::__construct();

        $this->InstitutionModel = new Institution();
        $this->userModel = new User();

        // Verifica se é um super admin
        if (!AuthHelper::isAdmin()) {
            Session::set('error', 'Você não tem permissão para acessar esta área.');
            $this->redirect('/dashboard');
        }
    }

    /**
     * Lista todas as instituições
     */
    public function index()
    {
        // Parâmetros de paginação e busca
        $pagina = $_GET['pagina'] ?? 1;
        $porPagina = 10;
        $busca = $_GET['busca'] ?? '';

        // Obter instituições (com paginação e busca)
        if (!empty($busca)) {
            $sql = "SELECT * FROM instituicoes WHERE nome LIKE :busca OR email LIKE :busca OR cnpj LIKE :busca";
            $params = ['busca' => "%{$busca}%"];
            $instituicoes = $this->InstitutionModel->findAll($sql, $params, $pagina, $porPagina);
            $total = $this->InstitutionModel->count($sql, $params);
        } else {
            $instituicoes = $this->InstitutionModel->paginate($pagina, $porPagina);
            $total = $this->InstitutionModel->count();
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

        // Renderizar view
        $this->render('institution/index.php', [
            'titulo' => 'Instituições',
            'instituicoes' => $instituicoes,
            'paginacao' => $paginacao,
            'busca' => $busca
        ]);
    }

    /**
     * Mostra formulário para criar nova instituição
     */
    public function create()
    {
        $this->render('institution/create.php', [
            'titulo' => 'Nova Instituição'
        ]);
    }

    /**
     * Processa o formulário de criação
     */
    public function store()
    {
        // Validar inputs
        $nome = $_POST['nome'] ?? '';
        $cnpj = $_POST['cnpj'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $cidade = $_POST['cidade'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $cep = $_POST['cep'] ?? '';
        $status = $_POST['status'] ?? 'ativo';
        $plano = $_POST['plano'] ?? 'basico';
        $dataExpiracao = $_POST['data_expiracao'] ?? '';

        // Validação básica
        ValidationHelper::required($nome, 'nome');
        ValidationHelper::required($cnpj, 'cnpj');
        ValidationHelper::required($email, 'email');
        ValidationHelper::email($email, 'email');

        // Se houver erros, retorna para o formulário
        if (ValidationHelper::hasErrors()) {
            Session::set('error', 'Corrija os erros no formulário.');
            Session::set('form_errors', ValidationHelper::getErrors());
            Session::set('form_data', $_POST);
            $this->redirect('/Institution/nova');
        }

        // Upload do logo, se houver
        $logo = '';
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/logos/';
            $fileName = uniqid() . '_' . basename($_FILES['logo']['name']);

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName)) {
                $logo = '/uploads/logos/' . $fileName;
            }
        }

        // Preparar dados para inserção
        $data = [
            'nome' => $nome,
            'cnpj' => $cnpj,
            'email' => $email,
            'telefone' => $telefone,
            'endereco' => $endereco,
            'cidade' => $cidade,
            'estado' => $estado,
            'cep' => $cep,
            'logo' => $logo,
            'status' => $status,
            'plano' => $plano,
            'data_expiracao' => !empty($dataExpiracao) ? $dataExpiracao : null
        ];

        // Inserir no banco
        $InstitutionId = $this->InstitutionModel->create($data);

        if ($InstitutionId) {
            Session::set('success', 'Instituição criada com sucesso!');
            $this->redirect('/Institution/' . $InstitutionId);
        } else {
            Session::set('error', 'Erro ao criar instituição. Tente novamente.');
            Session::set('form_data', $_POST);
            $this->redirect('/Institution/nova');
        }
    }

    /**
     * Exibe detalhes de uma instituição
     */
    public function show($id)
    {
        $Institution = $this->InstitutionModel->find($id);

        if (!$Institution) {
            Session::set('error', 'Instituição não encontrada.');
            $this->redirect('/Institution');
        }

        // Obter estatísticas
        $administradores = $this->userModel->getPorTipo('admin', $id);
        $professores = $this->userModel->getPorTipo('professor', $id);
        $alunos = $this->userModel->getPorTipo('aluno', $id);

        $estatisticas = [
            'administradores' => count($administradores),
            'professores' => count($professores),
            'alunos' => count($alunos)
        ];

        // Permissão para editar
        $usuarioPodeEditar = AuthHelper::isAdmin();

        $this->render('institution/perfil.php', [
            'titulo' => $Institution['nome'],
            'Institution' => $Institution,
            'estatisticas' => $estatisticas,
            'usuarioPodeEditar' => $usuarioPodeEditar
        ]);
    }

    /**
     * Exibe formulário para editar instituição
     */
    public function edit($id)
    {
        $Institution = $this->InstitutionModel->find($id);

        if (!$Institution) {
            Session::set('error', 'Instituição não encontrada.');
            $this->redirect('/Institution');
        }

        $this->render('institution/edit.php', [
            'titulo' => 'Editar Instituição',
            'Institution' => $Institution
        ]);
    }

    /**
     * Processa o formulário de edição
     */
    public function update($id)
    {
        $Institution = $this->InstitutionModel->find($id);

        if (!$Institution) {
            Session::set('error', 'Instituição não encontrada.');
            $this->redirect('/Institution');
        }

        // Validar inputs
        $nome = $_POST['nome'] ?? '';
        $cnpj = $_POST['cnpj'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefone = $_POST['telefone'] ?? '';
        $endereco = $_POST['endereco'] ?? '';
        $cidade = $_POST['cidade'] ?? '';
        $estado = $_POST['estado'] ?? '';
        $cep = $_POST['cep'] ?? '';
        $status = $_POST['status'] ?? 'ativo';
        $plano = $_POST['plano'] ?? 'basico';
        $dataExpiracao = $_POST['data_expiracao'] ?? '';

        // Validação básica
        ValidationHelper::required($nome, 'nome');
        ValidationHelper::required($cnpj, 'cnpj');
        ValidationHelper::required($email, 'email');
        ValidationHelper::email($email, 'email');

        // Se houver erros, retorna para o formulário
        if (ValidationHelper::hasErrors()) {
            Session::set('error', 'Corrija os erros no formulário.');
            Session::set('form_errors', ValidationHelper::getErrors());
            Session::set('form_data', $_POST);
            $this->redirect('/Institution/' . $id . '/editar');
        }

        // Atualizar logo, se enviado
        $logo = $Institution['logo'];
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/logos/';
            $fileName = uniqid() . '_' . basename($_FILES['logo']['name']);

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName)) {
                // Remover logo antigo, se existir
                if (!empty($Institution['logo']) && file_exists(dirname(__DIR__, 2) . '/public' . $Institution['logo'])) {
                    unlink(dirname(__DIR__, 2) . '/public' . $Institution['logo']);
                }

                $logo = '/uploads/logos/' . $fileName;
            }
        }

        // Preparar dados para atualização
        $data = [
            'nome' => $nome,
            'cnpj' => $cnpj,
            'email' => $email,
            'telefone' => $telefone,
            'endereco' => $endereco,
            'cidade' => $cidade,
            'estado' => $estado,
            'cep' => $cep,
            'logo' => $logo,
            'status' => $status,
            'plano' => $plano,
            'data_expiracao' => !empty($dataExpiracao) ? $dataExpiracao : null
        ];

        // Atualizar no banco
        $atualizado = $this->InstitutionModel->update($id, $data);

        if ($atualizado) {
            Session::set('success', 'Instituição atualizada com sucesso!');
            $this->redirect('/Institution/' . $id);
        } else {
            Session::set('error', 'Erro ao atualizar instituição. Tente novamente.');
            $this->redirect('/Institution/' . $id . '/editar');
        }
    }

    /**
     * Exclui uma instituição
     */
    public function delete($id)
    {
        $Institution = $this->InstitutionModel->find($id);

        if (!$Institution) {
            Session::set('error', 'Instituição não encontrada.');
            $this->redirect('/Institution');
        }

        // Verificar se existem usuários vinculados
        $usuarios = $this->userModel->where('Institution_id = :Institution_id', ['Institution_id' => $id]);

        if (!empty($usuarios)) {
            Session::set('error', 'Não é possível excluir a instituição. Existem usuários vinculados a ela.');
            $this->redirect('/Institution');
        }

        // Excluir a instituição
        $excluido = $this->InstitutionModel->delete($id);

        if ($excluido) {
            // Remover logo, se existir
            if (!empty($Institution['logo']) && file_exists(dirname(__DIR__, 2) . '/public' . $Institution['logo'])) {
                unlink(dirname(__DIR__, 2) . '/public' . $Institution['logo']);
            }

            Session::set('success', 'Instituição excluída com sucesso!');
        } else {
            Session::set('error', 'Erro ao excluir instituição. Tente novamente.');
        }

        $this->redirect('/Institution');
    }

    /**
     * Métodos adicionais para o modelo
     */
    private function findAll($sql, $params, $pagina, $porPagina)
    {
        $offset = ($pagina - 1) * $porPagina;
        $limitedSql = $sql . " LIMIT :offset, :limit";

        $limitParams = array_merge($params, ['offset' => $offset, 'limit' => $porPagina]);
        return $this->InstitutionModel->db->fetchAll($limitedSql, $limitParams);
    }
}
