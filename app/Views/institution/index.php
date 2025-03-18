<div class="row mb-4">
    <div class="col-md-6">
        <h2>Instituições</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="/instituicao/nova" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Instituição
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="card-title mb-0">Lista de Instituições</h5>
            </div>
            <div class="col-md-6">
                <form action="/instituicao" method="GET" class="d-flex">
                    <input type="text" name="busca" class="form-control me-2" placeholder="Buscar instituição..." value="<?php echo $busca ?? ''; ?>">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <?php if (empty($instituicoes)): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle me-2"></i> Nenhuma instituição encontrada.
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CNPJ</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Plano</th>
                        <th>Validade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($instituicoes as $instituicao): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if (!empty($instituicao['logo'])): ?>
                                <img src="<?php echo $instituicao['logo']; ?>" alt="Logo" class="me-2 rounded-circle" style="width: 32px; height: 32px;">
                                <?php else: ?>
                                <div class="avatar-circle me-2">
                                    <span class="avatar-text"><?php echo substr($instituicao['nome'], 0, 1); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php echo $instituicao['nome']; ?>
                            </div>
                        </td>
                        <td><?php echo $instituicao['cnpj']; ?></td>
                        <td><?php echo $instituicao['email']; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $instituicao['status'] === 'ativo' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($instituicao['status']); ?>
                            </span>
                        </td>
                        <td><?php echo $instituicao['plano'] ?? 'Padrão'; ?></td>
                        <td>
                            <?php if (!empty($instituicao['data_expiracao'])): ?>
                                <?php 
                                $hoje = new DateTime();
                                $expiracao = new DateTime($instituicao['data_expiracao']);
                                $expirado = $hoje > $expiracao;
                                ?>
                                <span class="<?php echo $expirado ? 'text-danger' : ''; ?>">
                                    <?php echo date('d/m/Y', strtotime($instituicao['data_expiracao'])); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">N/A</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="/instituicao/<?php echo $instituicao['id']; ?>" class="btn btn-sm btn-outline-secondary" title="Visualizar">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="/instituicao/<?php echo $instituicao['id']; ?>/editar" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir" 
                                        data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                        data-id="<?php echo $instituicao['id']; ?>"
                                        data-nome="<?php echo $instituicao['nome']; ?>">
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
                        <a class="page-link" href="?pagina=<?php echo $paginacao['pagina_atual'] - 1; ?><?php echo $busca ? '&busca=' . $busca : ''; ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    
                    <?php for ($i = 1; $i <= $paginacao['total_paginas']; $i++): ?>
                    <li class="page-item <?php echo $i === $paginacao['pagina_atual'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo $busca ? '&busca=' . $busca : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                    
                    <li class="page-item <?php echo $paginacao['pagina_atual'] >= $paginacao['total_paginas'] ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $paginacao['pagina_atual'] + 1; ?><?php echo $busca ? '&busca=' . $busca : ''; ?>">
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
                <p>Tem certeza que deseja excluir a instituição <strong id="instituicaoNome"></strong>?</p>
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
            
            document.getElementById('instituicaoNome').textContent = nome;
            document.getElementById('deleteForm').action = '/instituicao/' + id + '/excluir';
        });
    }
});
</script>