<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 2.2rem;">Panel de Control</h1>
        <p style="color: var(--text-muted);">Gestiona tu información y servicios desde un solo lugar.</p>
    </div>
    <a href="<?php echo PUBLIC_PATH; ?>/logout.php" class="btn btn-danger">Cerrar Sesión</a>
</div>

<div style="display: flex; flex-direction: column; gap: 2rem;">
    <!-- Tarjeta de Bienvenida y Datos -->
    <div class="feature-card" style="background: var(--card-bg);">
        <h2 style="color: var(--primary-green); margin-bottom: 0.5rem;">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario'); ?>!</h2>
        <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Este es tu panel de control personal en Nexora.</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; background: var(--bg-color); padding: 1.2rem; border-radius: 12px; border: 1px solid var(--card-border);">
            <p><strong>ID de Usuario:</strong> <span style="color: var(--primary-green);">#<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></span></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <span style="font-weight: 600;"><?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></span></p>
        </div>
    </div>

    <!-- Grilla de Acciones Rápidas -->
    <div class="features-grid" style="margin-top: 0; margin-bottom: 1rem;">
        <?php if ($es_admin): ?>
        <div class="feature-card">
            <h3>Panel Administrativo</h3>
            <p style="margin-bottom: 1.2rem;">Acceso completo a la gestión de usuarios, roles y configuración global del sistema.</p>
            <a href="<?php echo VIEWS_PATH; ?>/adminpanel.php" class="btn btn-primary">Acceder al Panel Admin</a>
        </div>
        <?php endif; ?>

        <div class="feature-card">
            <h3>Mi Perfil</h3>
            <p style="margin-bottom: 1.2rem;">Ver y editar la información de tu cuenta, avatar y datos personales.</p>
            <a href="<?php echo VIEWS_PATH; ?>/dashboard.php" class="btn btn-outline">Ver Perfil</a>
        </div>

        <div class="feature-card">
            <h3>Configuración</h3>
            <p style="margin-bottom: 1.2rem;">Ajusta tus preferencias de privacidad y notificaciones de tu espacio de trabajo.</p>
            <a href="<?php echo VIEWS_PATH; ?>/settings.php" class="btn btn-outline">Ir a Configuración</a>
        </div>
        
        <div class="feature-card">
            <h3>Soporte Técnico</h3>
            <p style="margin-bottom: 1.2rem;">Si necesitas asistencia o resolver dudas, nuestro equipo está disponible.</p>
            <a href="<?php echo PUBLIC_PATH; ?>/contacto.php" class="btn btn-outline">Contactar Soporte</a>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
