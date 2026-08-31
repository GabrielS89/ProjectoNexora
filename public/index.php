<?php
$page_title = 'Nexora - Infraestructura y Gestión Eficiente';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'acceso_restringido'): ?>
    <div class="alert alert-access-restricted">
        <h3 class="alert-title-danger">⚠️ Acceso Restringido al Panel de Administración</h3>
        <p class="mb-08rem">Se requieren permisos de <strong>Administrador (Rol 2)</strong> o <strong>Super Administrador (Rol 3)</strong> para ingresar.</p>
        <p class="font-09rem mb-04rem">Inicia sesión con las credenciales de administrador:</p>
        <p class="font-09rem"><strong>Email:</strong> <code>admin@nexora.com</code> &nbsp;|&nbsp; <strong>Clave:</strong> <code>Admin123!</code></p>
    </div>
<?php endif; ?>

<!-- HERO SECTION -->
<div class="hero-section">
    <span class="badge-hero-tag">
        Plataforma Empresarial 2026
    </span>
    <h1>Soluciones Inteligentes para <span>Nexora</span></h1>
    <p class="hero-subtitle">Plataforma integral para gestionar usuarios, empresas, infraestructura IT y herramientas administrativas con máxima seguridad.</p>

    <div class="hero-buttons">
        <?php if (isUserLoggedIn()): ?>
            <a class="btn btn-primary" href="<?php echo VIEWS_PATH; ?>/dashboard.php">Ir a mi Panel de Control</a>
            <a class="btn btn-outline" href="<?php echo VIEWS_PATH; ?>/tienda.php">Explorar Tienda</a>
        <?php else: ?>
            <a class="btn btn-primary" href="<?php echo PUBLIC_PATH; ?>/login.php">Iniciar Sesión</a>
            <a class="btn btn-outline" href="<?php echo PUBLIC_PATH; ?>/registro.php">Crear Cuenta Gratis</a>
        <?php endif; ?>
    </div>
</div>

<!-- FEATURES CARDS -->
<section id="acercaDe" class="features-grid">
    <div class="feature-card feature-card-bg">
        <div class="feature-card-icon">👥</div>
        <h3>Gestión de Usuarios</h3>
        <p>Administra clientes individuales y corporativos, asignando roles, permisos y niveles de acceso de forma centralizada.</p>
    </div>
    <div class="feature-card feature-card-bg">
        <div class="feature-card-icon">📊</div>
        <h3>Panel Administrativo</h3>
        <p>Herramientas avanzadas para supervisar la plataforma, auditar cuentas y configurar parámetros del entorno.</p>
    </div>
    <div class="feature-card feature-card-bg">
        <div class="feature-card-icon">🔒</div>
        <h3>Seguridad & Garantía</h3>
        <p>Encriptación BCRYPT de credenciales, control de sesiones persistentes y soporte técnico profesional.</p>
    </div>
</section>



    <?php
    $is_embedded = true;
    require_once(__DIR__ . '/../src/views/planes.php');

    require_once(INCLUDES_DIR . '/footer.php'); ?>
