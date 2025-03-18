<?php
use App\Helpers\AuthHelper;

// Verificar se a instituição está válida
if (!$instituicaoValida):
?>
<div class="alert alert-warning" role="alert">
    <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i> Atenção!</h4>
    <p>Sua instituição está com o período de acesso vencido. Entre em contato com o administrador do sistema para regularizar a situação.</p>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card welcome-card mb-4">
            <div class="card-body">
                <h2>Bem-vindo, <?php echo $usuario['nome']; ?>!</h2>
                <p class="mb-0">
                    <?php if (AuthHelper::isAdmin()): ?>
                        Você está conectado como Administrador do Sistema.
                    <?php elseif (AuthHelper::isInstituicaoAdmin()): ?>
                        Você está conectado como Administrador da instituição <?php echo $instituicao['nome']; ?>.
                    <?php else: ?>
                        Você está conectado na instituição <?php echo $instituicao['nome']; ?>.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row">
    <div class="col-md-4">
        <div class="card stat-card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="stat-content ms-3">
                        <h5 class="stat-title">Administradores</h5>
                        <h3 class="stat-value"><?php echo $contadores['administradores']; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success">
                        <i class="bi bi-person-video3"></i>
                    </div>
                    <div class="stat-content ms-3">
                        <h5 class="stat-title">Professores</h5>
                        <h3 class="stat-value"><?php echo $contadores['professores']; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card stat-card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info">
                        <i class="bi bi-mortarboard"></i>
                    </div>
                    <div class="stat-content ms-3">
                        <h5 class="stat-title">Alunos</h5>
                        <h3 class="stat-value"><?php echo $contadores['alunos']; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Atividades Recentes e Informações da Instituição -->
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Atividades Recentes</h5>
            </div>
            <div class="card-body">
                <!-- Lista de atividades recentes aqui -->
                <div class="activity-item">
                    <div class="activity-icon bg-light text-primary">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="activity-content">
                        <h6 class="mb-1">Login realizado</h6>
                        <small class="text-muted">Hoje, <?php echo date('H:i'); ?></small>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon bg-light text-success">
                        <i class="bi bi-building"></i>
                    </div>
                    <div class="activity-content">
                        <h6 class="mb-1">Último acesso</h6>
                        <small class="text-muted">
                            <?php echo $usuario['ultimo_acesso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])) : 'Primeiro acesso'; ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações da Instituição</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Nome:</span>
                        <span class="fw-bold"><?php echo $instituicao['nome']; ?></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Status:</span>
                        <span class="badge bg-<?php echo $instituicao['status'] === 'ativo' ? 'success' : 'danger'; ?> rounded-pill">
                            <?php echo ucfirst($instituicao['status']); ?>
                        </span>
                    </li>
                    <?php if (!empty($instituicao['data_expiracao'])): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Validade:</span>
                        <span class="fw-bold"><?php echo date('d/m/Y', strtotime($instituicao['data_expiracao'])); ?></span>
                    </li>
                    <?php endif; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>Plano:</span>
                        <span class="fw-bold"><?php echo $instituicao['plano'] ?? 'Padrão'; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>