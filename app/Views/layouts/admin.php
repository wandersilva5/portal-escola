<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <title><?php echo $titulo ?? 'Portal Escolar'; ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php require $this->viewPath . 'layouts/components/sidebar.php'; ?>
        
        <div class="main-content">
            <!-- Header -->
            <?php require $this->viewPath . 'layouts/components/header.php'; ?>
            
            <main class="content">
                <!-- Alertas -->
                <?php require $this->viewPath . 'layouts/components/alerts.php'; ?>
                
                <!-- Conteúdo principal -->
                <?php echo $content; ?>
            </main>
            
            <!-- Footer -->
            <?php require $this->viewPath . 'layouts/components/footer.php'; ?>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/admin.js"></script>
    <script src="/assets/js/main.js"></script>
</body>
</html>