<?php
$require_login = true;
$page_title = 'Bienvenido - Proyecto 3ro MA';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>
<div class="pagina-panel welcome-panel">
    <div class="encabezado-pagina">
        <div>
            <p class="texto-previo">Acceso confirmado</p>
            <h1>¡Bienvenido!</h1>
            <p class="descripcion-pagina">Tu sesión se ha iniciado correctamente. Desde aquí puedes continuar con tus tareas.</p>
        </div>
        <a class="btn btn-danger" href="<?php echo PUBLIC_PATH; ?>/logout.php">Cerrar Sesión</a>
    </div>

    <section class="feature-card bienvenida-panel">
        <h2>Tu espacio Nexora</h2>
        <p class="texto-bienvenida">Estos son los datos asociados a tu cuenta.</p>
        <div class="datos-cuenta">
            <p><strong>ID de Usuario:</strong> #<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></p>
        </div>
    </section>

    <div class="rejilla-panel welcome-actions">
        <section class="feature-card tarjeta-panel">
            <p class="texto-previo">Continuar</p>
            <h3>Panel de Control</h3>
            <p>Accede a tus herramientas y gestiona tu cuenta desde tu espacio personal.</p>
            <a class="btn btn-primary" href="<?php echo VIEWS_PATH; ?>/dashboard.php">Ir al Panel de Control</a>
        </section>
    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
