<?php
$page_title = 'Inicio - Proyecto 3ro';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>
<div class="hero-section">
    <h1>¡Bienvenido a <span>Nexora</span>!</h1>
    <p class="hero-subtitle">Una plataforma segura para gestionar usuarios y empresas con herramientas administrativas, control y soporte.</p>

    <div class="hero-buttons">
        <a class="btn btn-primary" href="<?php echo PUBLIC_PATH; ?>/login.php">Iniciar Sesión</a>
        <a class="btn btn-outline" href="<?php echo PUBLIC_PATH; ?>/registro_persona.php">Registrarse como Persona</a>
        <a class="btn btn-outline" href="<?php echo PUBLIC_PATH; ?>/registro_empresa.php">Registrarse como Empresa</a>
    </div>
</div>

<section id="AcercaDe" class="features-grid">
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

<section id="planes" class="planes-section">
    <h2>Planes de Infraestructura</h2>
    <p class="section-desc">Paquetes prediseñados para la rápida implementación de espacios de trabajo.</p>

    <div class="plans-grid planes-grid">
        <div class="plan-card destacado">
            <div class="badge">Popular</div>
            <h3>Plan Start-Up</h3>
            <p class="plan-subtitle">4 Estaciones</p>
            <ul>
                <li>Suministro de 4 equipos.</li>
                <li>Instalación de cableado estructurado.</li>
                <li>Red Wi‑Fi y configuración básica.</li>
            </ul>
            <a class="btn btn-primary" href="#">Contratar</a>
        </div>

        <div class="plan-card">
            <h3>Plan Business</h3>
            <p class="plan-subtitle">8 Estaciones + Sala de Juntas</p>
            <ul>
                <li>Suministro de 8 equipos.</li>
                <li>Instalación de infraestructura en áreas comunes.</li>
                <li>Soporte y configuración avanzada.</li>
            </ul>
            <a class="btn btn-outline" href="#">Contratar</a>
        </div>

        <div class="plan-card">
            <h3>Plan Custom</h3>
            <p class="plan-subtitle">Personalizado</p>
            <ul>
                <li>Consultoría y selección de componentes.</li>
                <li>Software bajo licencia según necesidad.</li>
                <li>Distribución y puesta en marcha a medida.</li>
            </ul>
            <a class="btn btn-outline" href="#">Solicitar cotización</a>
        </div>
    </div>
</section>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>