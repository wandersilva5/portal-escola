<?php
namespace App\Controllers;

use App\Core\Session;
use App\Models\User;
use App\Models\Institution;
use App\Helpers\AuthHelper;

class DashboardController extends BaseController
{
    protected $usuarioModel;
    protected $instituicaoModel;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->usuarioModel = new User();
        $this->instituicaoModel = new Institution();
    }
    
    /**
     * Página inicial do dashboard
     */
    public function index()
    {
        // Obter ID da instituição do usuário logado
        $instituicaoId = AuthHelper::getInstituicaoId();
        
        // Contagem de usuários por tipo para esta instituição
        $administradores = $this->usuarioModel->getPorTipo('admin', $instituicaoId);
        $professores = $this->usuarioModel->getPorTipo('professor', $instituicaoId);
        $alunos = $this->usuarioModel->getPorTipo('aluno', $instituicaoId);
        
        // Verificar validade da instituição
        $instituicaoValida = $this->instituicaoModel->dentroDaValidade($instituicaoId);
        
        // Preparar dados para a view
        $dados = [
            'titulo' => 'Dashboard',
            'contadores' => [
                'administradores' => count($administradores),
                'professores' => count($professores),
                'alunos' => count($alunos)
            ],
            'instituicaoValida' => $instituicaoValida
        ];
        
        // Renderizar a view
        $this->render('dashboard/index.php', $dados);
    }
}