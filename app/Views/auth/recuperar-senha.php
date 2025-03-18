<div class="auth-logo text-center mb-4">
    <h1 class="fw-bold">Portal Escolar</h1>
    <p>Sistema de Gestão Escolar</p>
</div>

<div class="card shadow">
    <div class="card-body p-4">
        <h2 class="text-center mb-4">Recuperar Senha</h2>
        
        <p class="text-center mb-4">
            Digite seu e-mail de cadastro. Enviaremos instruções para redefinir sua senha.
        </p>
        
        <form action="/recuperar-senha" method="POST">
            <div class="mb-4">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="seu.email@exemplo.com" required>
                </div>
            </div>
            
            <div class="d-grid gap-2 mb-3">
                <button type="submit" class="btn btn-primary">Enviar instruções</button>
            </div>
            
            <div class="text-center">
                <a href="/login" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Voltar para o login
                </a>
            </div>
        </form>
    </div>
</div>

<div class="text-center mt-3">
    <p class="text-muted">
        &copy; <?php echo date('Y'); ?> Portal Escolar - Todos os direitos reservados
    </p>
</div>