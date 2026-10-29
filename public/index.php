<?php
$page_title = 'Nexora - Infraestructura y Gestión Eficiente';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'acceso_restringido'): ?>
    <div class="alerta alerta--error">
        <h3>Acceso Restringido al Panel de Administración</h3>
        <p>Se requieren permisos de <strong>Administrador</strong> o <strong>Super Administrador</strong> para ingresar.</p>
        <p>Inicia sesión con las credenciales de administrador:</p>
    </div>
<?php endif; ?>

<!-- HERO SECTION -->
<div class="hero-section">
    <span class="texto-sutil">Plataforma Empresarial 2026</span>
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
    <div class="feature-card">
        <h3>Gestión de Usuarios</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
    </div>
    <div class="feature-card">
        <h3>Panel Administrativo</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</p>
    </div>
    <div class="feature-card">
        <h3>Seguridad &amp; Garantía</h3>
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam  </p>
    </div>
</section>

<!-- PLANES DE INFRAESTRUCTURA -->
<section id="planes" class="planes-section">

<?php
// Incluir la vista de planes embebida en la portada
$is_embedded = true;
require_once(__DIR__ . '/../src/views/planes.php');
?>

</section>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
