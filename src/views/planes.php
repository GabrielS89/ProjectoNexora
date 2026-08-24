<?php
$page_title = 'Planes - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>
<div class="hero-section">
    <h1>¡Bienvenido a <span>Nexora</span>!</h1>
    <p class="hero-subtitle">Página de Gestión y Contratación de planes</p>
</div>

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
                <a class="btn btn-primary open-planes" href="#">Contratar</a>
        </div>

        <div class="plan-card">
            <h3>Plan Business</h3>
            <p class="plan-subtitle">8 Estaciones + Sala de Juntas</p>
            <ul>
                <li>Suministro de 8 equipos.</li>
                <li>Instalación de infraestructura en áreas comunes.</li>
                <li>Soporte y configuración avanzada.</li>
            </ul>
                <a class="btn btn-outline open-planes" href="#">Contratar</a>
        </div>

        <div class="plan-card">
            <h3>Plan Custom</h3>
            <p class="plan-subtitle">Personalizado</p>
            <ul>
                <li>Consultoría y selección de componentes.</li>
                <li>Software bajo licencia según necesidad.</li>
                <li>Distribución y puesta en marcha a medida.</li>
            </ul>
                <a class="btn btn-outline open-planes" href="#">Solicitar cotización</a>
        </div>
    </div>
</section>


<?php    require_once(INCLUDES_DIR . '/footer.php'); ?>