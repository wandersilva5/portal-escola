/**
 * Portal Escolar - JavaScript principal
 */

document.addEventListener('DOMContentLoaded', function() {
    // Preloader
    const preloader = document.querySelector('.preloader');
    if (preloader) {
        setTimeout(function() {
            preloader.classList.add('hide');
        }, 500);
    }
    
    // Verificação de sessão expirada
    let lastActivity = Date.now();
    const sessionTimeout = 30 * 60 * 1000; // 30 minutos
    
    function resetTimer() {
        lastActivity = Date.now();
    }
    
    function checkSession() {
        const now = Date.now();
        const idle = now - lastActivity;
        
        if (idle > sessionTimeout) {
            // Verificar se já existe alerta
            if (!document.querySelector('.session-alert')) {
                showSessionAlert();
            }
        }
    }
    
    function showSessionAlert() {
        const alert = document.createElement('div');
        alert.className = 'session-alert alert alert-warning alert-dismissible fade show';
        alert.setAttribute('role', 'alert');
        
        alert.innerHTML = `
            <h5><i class="bi bi-exclamation-triangle-fill me-2"></i> Sessão expirada</h5>
            <p>Sua sessão pode ter expirado devido à inatividade.</p>
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-primary" id="refreshSession">Renovar sessão</button>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        `;
        
        document.body.appendChild(alert);
        
        // Botão para renovar sessão
        document.getElementById('refreshSession').addEventListener('click', function() {
            window.location.reload();
        });
        
        // Auto-close
        setTimeout(function() {
            if (alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 10000);
    }
    
    // Adicionar listeners para resetar timer
    const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'];
    events.forEach(function(event) {
        document.addEventListener(event, resetTimer, true);
    });
    
    // Verificar sessão a cada minuto
    setInterval(checkSession, 60 * 1000);
    
    // Validação de formulários
    const forms = document.querySelectorAll('.needs-validation');
    
    forms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
});