<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Perfil da Instituição</h5>
                <?php if ($usuarioPodeEditar): ?>
                <a href="/instituicao/<?php echo $instituicao['id']; ?>/editar" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil-square"></i> Editar
                </a>
                <?php endif; ?>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-4">
                        <?php if (!empty($instituicao['logo'])): ?>
                        <img src="<?php echo $instituicao['logo']; ?>" alt="Logo da instituição" class="img-fluid rounded mb-3" style="max-height: 150px;">
                        <?php else: ?>
                        <div class="institution-logo-placeholder mb-3">
                            <span><?php echo substr($instituicao['nome'], 0, 1); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-9">
                        <h3 class="mb-3"><?php echo $instituicao['nome']; ?></h3>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="fw-bold">CNPJ</h6>
                                    <p><?php echo $instituicao['cnpj']; ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fw-bold">E-mail</h6>
                                    <p><?php echo $instituicao['email']; ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fw-bold">Telefone</h6>
                                    <p><?php echo $instituicao['telefone']; ?></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="fw-bold">Status</h6>
                                    <p>
                                        <span class="badge bg-<?php echo $instituicao['status'] === 'ativo' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($instituicao['status']); ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fw-bold">Plano</h6>
                                    <p><?php echo $instituicao['plano'] ?? 'Padrão'; ?></p>
                                </div>
                                
                                <div class="mb-3">
                                    <h6 class="fw-bold">Validade</h6>
                                    <p>
                                        <?php if (!empty($instituicao['data_expiracao'])): ?>
                                            <?php echo date('d/m/Y', strtotime($instituicao['data_expiracao'])); ?>
                                            
                                            <?php 
                                            $hoje = new DateTime();
                                            $expiracao = new DateTime($instituicao['data_expiracao']);
                                            $diff = $hoje->diff($expiracao);
                                            
                                            if ($hoje > $expiracao): 
                                            ?>
                                                <span class="badge bg-danger">Expirado</span>
                                            <?php elseif ($diff->days <= 30): ?>
                                                <span class="badge bg-warning">Expira em <?php echo $diff->days; ?> dias</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Sem data de expiração
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="mb-3">Endereço</h5>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <p>
                                    <?php echo $instituicao['endereco']; ?><br>
                                    <?php echo $instituicao['cidade']; ?> - <?php echo $instituicao['estado']; ?><br>
                                    CEP: <?php echo $instituicao['cep']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas da Instituição -->
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Estatísticas</h5>
            </div>
            
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card text-center p-3">
                            <div class="stat-icon bg-primary rounded-circle mb-3">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <h4><?php echo $estatisticas['administradores']; ?></h4>
                            <p class="text-muted">Administradores</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="stat-card text-center p-3">
                            <div class="stat-icon bg-success rounded-circle mb-3">
                                <i class="bi bi-person-video3"></i>
                            </div>
                            <h4><?php echo $estatisticas['professores']; ?></h4>
                            <p class="text-muted">Professores</p>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="stat-card text-center p-3">
                            <div class="stat-icon bg-info rounded-circle mb-3">
                                <i class="bi bi-mortarboard"></i>
                            </div>
                            <h4><?php echo $estatisticas['alunos']; ?></h4>
                            <p class="text-muted">Alunos</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>