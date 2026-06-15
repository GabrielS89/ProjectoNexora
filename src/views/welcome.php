<?php
$require_login = true;
$page_title = 'Bienvenido - Proyecto 3ro MA';
require_once(INCLUDES_DIR . '/header.php');
?>
    <div class="container">
        <h1>¡Bienvenido!</h1>
        <p class="welcome-message">Tu sesión se ha iniciado correctamente</p>
        
        <div class="user-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong></p>
            <span class="user-type"><?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></span>
        </div>
        
        <div class="btn-group">
            <a href="<?php echo PUBLIC_PATH; ?>/dashboard.php" class="btn btn-primary">Ir al Panel de Control</a>
            <a href="<?php echo PUBLIC_PATH; ?>/logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        
        <p class="footer-message">ID de Usuario: #<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></p>
    </div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
