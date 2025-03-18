<?php
use App\Core\Session;

// Mensagens de erro
if (Session::has('error')) {
    $error = Session::get('error');
    Session::remove('error');
    ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php
}

// Mensagens de sucesso
if (Session::has('success')) {
    $success = Session::get('success');
    Session::remove('success');
    ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php
}

// Mensagens de informação
if (Session::has('info')) {
    $info = Session::get('info');
    Session::remove('info');
    ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        <?php echo $info; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php 
}

// Mensagens de alerta
if (Session::has('warning')) {
    $warning = Session::get('warning');
    Session::remove('warning');
    ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        <?php echo $warning; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
    <?php
}
?>