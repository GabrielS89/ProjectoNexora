<?php
$page_title = 'Inicio - Proyecto 3ro';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>
<div class="hero-section">
    <h1>¡Bienvenido a <span>Nexora</span>!</h1>
    <p class="hero-subtitle">Servicio integral de Infraestructura y Tecnología</p>

    <div class="hero-buttons">
        <a class="btn btn-primary" href="<?php echo PUBLIC_PATH; ?>/login.php">Iniciar Sesión</a>
        <a class="btn btn-outline" href="<?php echo PUBLIC_PATH; ?>/registro.php">Registro</a>
    </div>
</div>

<section id="acercaDe" class="features-grid">
    <div class="feature-card">
        <h3>Gestión de Usuarios</h3>
        <p>Administra usuarios, roles y permisos con controles sencillos.</p>
    </div>
    <div class="feature-card">
        <h3>Panel Administrativo</h3>
        <p>Accede a herramientas avanzadas para supervisar y configurar tu plataforma.</p>
    </div>
    <div class="feature-card">
        <h3>Seguridad y Soporte</h3>
        <p>Protección de datos y asistencia técnica para tu entorno productivo.</p>
    </div>
</section>



    <?php // Incluir modal de planes dentro de index (se renderiza condicionalmente)
    require_once(__DIR__ . '/../src/views/planes.php');

    require_once(INCLUDES_DIR . '/footer.php'); ?>