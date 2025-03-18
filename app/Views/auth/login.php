<div class="auth-logo text-center mb-4">
    <h1 class="fw-bold">Portal Escolar</h1>
    <p>Sistema de Gestão Escolar</p>
</div>

<div class="card shadow">
    <div class="card-body p-4">
        <h2 class="text-center mb-4">Entrar no sistema</h2>

        <form action="<?php echo base_url('auth/login'); ?>" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="seu.email@exemplo.com" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="lembrar" name="lembrar">
                    <label class="form-check-label" for="lembrar">Lembrar-me</label>
                </div>
                <a href="/recuperar-senha" class="text-decoration-none">Esqueceu a senha?</a>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Entrar</button>
            </div>
        </form>
    </div>
</div>

<div class="text-center mt-3">
    <p class="text-muted">
        &copy; <?php echo date('Y'); ?> Portal Escolar - Todos os direitos reservados
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        console.log('Formulário sendo enviado com método:', form.method);
        console.log('Action:', form.action);
        // Remova este 'prevent' após o debug
        // e.preventDefault();
    });
});
</script>