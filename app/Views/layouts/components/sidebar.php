<?php
use App\Helpers\AuthHelper;

// Obter a URL atual para destacar o menu ativo
$currentUrl = $_SERVER['REQUEST_URI'];
$currentPage = explode('?', $currentUrl)[0];
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <?php if (!empty($instituicao['logo'])): ?>
            <img src="<?php echo $instituicao['logo']; ?>" alt="Logo da instituição" class="logo">
        <?php else: ?>
            <div class="text-logo"><?php echo substr($instituicao['nome'] ?? 'Portal Escolar', 0, 1); ?></div>
        <?php endif; ?>
        <h3><?php echo $instituicao['nome'] ?? 'Portal Escolar'; ?></h3>
    </div>
    
    <div class="sidebar-content">
        <nav class="sidebar-nav">
            <ul class="nav-menu">
                <li class="nav-item <?php echo $currentPage === '/dashboard' ? 'active' : ''; ?>">
                    <a href="/dashboard" class="nav-link">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <?php if (AuthHelper::isAdmin() || AuthHelper::isInstituicaoAdmin()): ?>
                <li class="nav-item <?php echo strpos($currentPage, '/usuarios') === 0 ? 'active' : ''; ?>">
                    <a href="/usuarios" class="nav-link">
                        <i class="bi bi-people"></i>
                        <span>Usuários</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (AuthHelper::isAdmin()): ?>
                <li class="nav-item <?php echo strpos($currentPage, '/instituicao') === 0 ? 'active' : ''; ?>">
                    <a href="/instituicao" class="nav-link">
                        <i class="bi bi-building"></i>
                        <span>Instituições</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item <?php echo strpos($currentPage, '/perfil') === 0 ? 'active' : ''; ?>">
                    <a href="/perfil" class="nav-link">
                        <i class="bi bi-person"></i>
                        <span>Meu Perfil</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/logout" class="nav-link">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Sair</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>