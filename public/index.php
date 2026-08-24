<?php
$page_title = 'Nexora - Infraestructura y Gestión Eficiente';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'acceso_restringido'): ?>
    <div class="alert" style="background: rgba(220, 53, 69, 0.12); color: #d32f2f; border: 1.5px solid rgba(220, 53, 69, 0.3); text-align: center; margin: 1.5rem auto; max-width: 700px; padding: 1.2rem; border-radius: 12px; backdrop-filter: blur(10px);">
        <h3 style="color: #d32f2f; margin-bottom: 0.4rem;">⚠️ Acceso Restringido al Panel de Administración</h3>
        <p style="margin-bottom: 0.8rem;">Se requieren permisos de <strong>Administrador (Rol 2)</strong> o <strong>Super Administrador (Rol 3)</strong> para ingresar.</p>
        <p style="font-size: 0.9rem; margin-bottom: 0.4rem;">Inicia sesión con las credenciales de administrador:</p>
        <p style="font-size: 0.9rem;"><strong>Email:</strong> <code>admin@nexora.com</code> &nbsp;|&nbsp; <strong>Clave:</strong> <code>Admin123!</code></p>
    </div>
<?php endif; ?>

<!-- HERO SECTION -->
<div class="hero-section">
    <span style="background: var(--light-green-tint); color: var(--primary-green); padding: 6px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; margin-bottom: 1rem;">
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
    <div class="feature-card" style="background: var(--card-bg);">
        <div style="font-size: 2rem; margin-bottom: 0.5rem;">👥</div>
        <h3>Gestión de Usuarios</h3>
        <p>Administra clientes individuales y corporativos, asignando roles, permisos y niveles de acceso de forma centralizada.</p>
    </div>
    <div class="feature-card" style="background: var(--card-bg);">
        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📊</div>
        <h3>Panel Administrativo</h3>
        <p>Herramientas avanzadas para supervisar la plataforma, auditar cuentas y configurar parámetros del entorno.</p>
    </div>
    <div class="feature-card" style="background: var(--card-bg);">
        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔒</div>
        <h3>Seguridad & Garantía</h3>
        <p>Encriptación BCRYPT de credenciales, control de sesiones persistentes y soporte técnico profesional.</p>
    </div>
</section>

<!-- PLANES DE INFRAESTRUCTURA -->
<section id="planes" class="planes-section">
    <h2>Planes de Infraestructura</h2>
    <p class="section-desc">Paquetes integrales prediseñados para la puesta en marcha inmediata de estaciones de trabajo y red.</p>

    <div class="plans-grid planes-grid">
        <div class="plan-card destacado">
            <div class="badge">Popular</div>
            <h3>Plan Start-Up</h3>
            <p class="plan-subtitle">4 Estaciones de Trabajo</p>
            <ul>
                <li>Suministro de 4 equipos de cómputo.</li>
                <li>Cableado estructurado de red local.</li>
                <li>Conectividad Wi-Fi de alta velocidad.</li>
                <li>Configuración inicial de software.</li>
            </ul>
            <a class="btn btn-primary" href="<?php echo PUBLIC_PATH; ?>/contacto.php">Contratar Plan</a>
        </div>

        <div class="plan-card">
            <h3>Plan Business</h3>
            <p class="plan-subtitle">8 Estaciones + Sala de Juntas</p>
            <ul>
                <li>Suministro de 8 equipos de cómputo.</li>
                <li>Infraestructura en áreas comunes.</li>
                <li>Access Point corporativo empresarial.</li>
                <li>Soporte técnico prioritario.</li>
            </ul>
            <a class="btn btn-outline" href="<?php echo PUBLIC_PATH; ?>/contacto.php">Contratar Plan</a>
        </div>

        <div class="plan-card">
            <h3>Plan Custom</h3>
            <p class="plan-subtitle">100% Personalizado</p>
            <ul>
                <li>Consultoría técnica presencial.</li>
                <li>Selección de hardware y componentes.</li>
                <li>Licenciamiento de software a medida.</li>
                <li>Puesta en marcha e integración.</li>
            </ul>
            <a class="btn btn-outline" href="<?php echo PUBLIC_PATH; ?>/contacto.php">Solicitar Cotización</a>
        </div>
    </div>
</section>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>