<header class="header">
    <div class="header-left">
        <button id="sidebar-toggle" class="btn btn-link sidebar-toggle">
            <i class="bi bi-list"></i>
        </button>
    </div>
    
    <div class="header-title">
        <h4><?php echo $titulo ?? 'Portal Escolar'; ?></h4>
    </div>
    
    <div class="header-right">
        <div class="dropdown">
            <button class="btn btn-link dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <?php if (!empty($usuario['foto'])): ?>
                    <img src="<?php echo $usuario['foto']; ?>" alt="Foto de perfil" class="avatar">
                <?php else: ?>
                    <i class="bi bi-person-circle"></i>
                <?php endif; ?>
                <span class="ms-2"><?php echo $usuario['nome'] ?? 'Usuário'; ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="/perfil"><i class="bi bi-person me-2"></i> Meu Perfil</a></li>
                <li><a class="dropdown-item" href="/instituicao"><i class="bi bi-building me-2"></i> Minha Instituição</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right me-2"></i> Sair</a></li>
            </ul>
        </div>
    </div>
</header>