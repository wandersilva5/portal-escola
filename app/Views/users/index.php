<div class="row mb-4">
    <div class="col-md-6">
        <h2>Usuários</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="/usuarios/novo" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Usuário
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-md-8">
                <form action="/usuarios" method="GET" class="d-flex gap-2">
                    <div class="input-group">
                        <input type="text" name="busca" class="form-control" placeholder="Buscar usuário..." value="<?php echo $busca ?? ''; ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    
                    <select name="tipo" class="form-select" style="width: auto;" onchange="this.form.submit()">
                        <option value="">Todos os tipos</option>
                        <?php foreach ($tipos as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $filtroTipo === $value ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <?php if (isset($_GET['busca']) || isset($_GET['tipo'])): ?>
                    <a href="/usuarios" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($usuarios)): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i> Nenhum usuário encontrado.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Último Acesso</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($usuario['foto'])): ?>
                                <img src="<?php echo $usuario['foto']; ?>" alt="Foto" class="me-2 rounded-circle" style="width: 32px; height: 32px;">
                                <?php else: ?>
                                <div class="avatar-circle me-2">
                                    <span class="avatar-text"><?php echo substr($usuario['nome'], 0, 1); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php echo $usuario['nome']; ?>
                            </div>
                        </td>
                        <td><?php echo $usuario['email']; ?></td>
                        <td>
                            <?php echo $tipos[$usuario['tipo']] ?? $usuario['tipo']; ?>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo $usuario['status'] === 'ativo' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($usuario['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($usuario['ultimo_acesso'])): ?>
                            <?php echo date('d/m/Y H:i', strtotime($usuario['ultimo_acesso'])); ?>
                            <?php else: ?>
                            <em class="text-muted">Nunca acessou</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="/usuarios/<?php echo $usuario['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/usuarios/<?php echo $usuario['id']; ?>/editar" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                        data-id="<?php echo $usuario['id']; ?>"
                                        data-nome="<?php echo $usuario['nome']; ?>">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($paginacao)): ?>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>
                <p class="text-muted">
                    Exibindo <?php echo $paginacao['inicio']; ?> até <?php echo $paginacao['fim']; ?> 
                    de <?php echo $paginacao['total']; ?> resultados
                </p>
            </div>
            <nav>
                <ul class="pagination">
                    <li class="page-item <?php echo $paginacao['pagina_atual'] <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $paginacao['pagina_atual'] - 1; ?><?php echo $busca ? '&busca=' . $busca : ''; ?><?php echo $filtroTipo ? '&tipo=' . $filtroTipo : ''; ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $paginacao['total_paginas']; $i++): ?>
                    <li class="page-item <?php echo $i === $paginacao['pagina_atual'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo $busca ? '&busca=' . $busca : ''; ?><?php echo $filtroTipo ? '&tipo=' . $filtroTipo : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $paginacao['pagina_atual'] >= $paginacao['total_paginas'] ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $paginacao['pagina_atual'] + 1; ?><?php echo $busca ? '&busca=' . $busca : ''; ?><?php echo $filtroTipo ? '&tipo=' . $filtroTipo : ''; ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o usuário <strong id="usuarioNome"></strong>?</p>
                <p class="text-danger">Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" action="" method="POST">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nome = button.getAttribute('data-nome');
            
            document.getElementById('usuarioNome').textContent = nome;
            document.getElementById('deleteForm').action = '/usuarios/' + id + '/excluir';
        });
    }
});
</script>