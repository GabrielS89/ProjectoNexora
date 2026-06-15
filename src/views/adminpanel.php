<?php
$require_admin = true;
$page_title = 'Área Administrativa - PoisonIvy';
require_once(INCLUDES_DIR . '/header.php');
?>

<div>
    <h1>Área Restringida para Administradores</h1>
    <p>Bienvenido, has accedido con privilegios elevados.</p>
    
    <div>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <p><strong>Nivel de Acceso:</strong>
            <?php echo ($_SESSION['usuario_rol'] == 3) ? 'Super Administrador' : 'Administrador'; ?>
        </p>
    </div>
    
    <div>
        <a href="<?php echo PUBLIC_PATH; ?>/logout.php">Cerrar Sesión</a>
    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>