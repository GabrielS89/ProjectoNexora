<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
?>

<div class="dashboard-header-flex">
    <div>
        <h1 class="dashboard-title">Panel de Control</h1>
        <p class="text-muted-custom">Gestiona tu información y servicios desde un solo lugar.</p>
    </div>
    <a href="<?php echo PUBLIC_PATH; ?>/logout.php" class="btn btn-danger">Cerrar Sesión</a>
</div>

    
    <div>
        
        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario'); ?>!</h2>
        <p>Este es tu panel de control personal donde puedes gestionar tu cuenta y acceder a las herramientas disponibles.</p>
        
    
        <br>
    
    <div>
        
<?php if ($es_admin): ?>

    <div>
        <h3>Panel Administrativo</h3>
        <p>Tienes acceso a herramientas de gestión de usuarios y sistema.</p>
        <a href="<?php echo VIEWS_PATH; ?>/adminpanel.php">Acceder al Panel de Admin</a>
    </div>

<?php endif; ?>

        <br>

    <div>
        <h3>Mi Perfil</h3>
        <p>Ver y editar la información de tu perfil así como tus preferencias de cuenta.</p>
        <a href="<?php echo VIEWS_PATH; ?>/perfil.php">Ver Perfil</a>
    </div>
        
        <br>



    <!-- Grilla de Acciones Rápidas -->
    <div class="features-grid mb-1rem">
        <?php if ($es_admin): ?>
        <div class="feature-card">
            <h3>Panel Administrativo</h3>
            <p class="mb-12rem">Acceso completo a la gestión de usuarios, roles y configuración global del sistema.</p>
            <a href="<?php echo VIEWS_PATH; ?>/adminpanel.php" class="btn btn-primary">Acceder al Panel Admin</a>
        </div>
        <?php endif; ?>

        <div class="feature-card">
            <h3>Mi Perfil</h3>
            <p class="mb-12rem">Ver y editar la información de tu cuenta, avatar y datos personales.</p>
            <a href="<?php echo VIEWS_PATH; ?>/dashboard.php" class="btn btn-outline">Ver Perfil</a>
        </div>
        
        <div class="feature-card">
            <h3>Soporte Técnico</h3>
            <p class="mb-12rem">Si necesitas asistencia o resolver dudas, nuestro equipo está disponible.</p>
            <a href="<?php echo PUBLIC_PATH; ?>/contacto.php" class="btn btn-outline">Contactar Soporte</a>
        </div>

    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
