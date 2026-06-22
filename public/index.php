<?php
$page_title = 'Inicio - Proyecto 3ro';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<div>
    <h1>¡Bienvenido a Nexora!</h1>
    <p>
        Una plataforma segura para gestionar usuarios y empresas.
    </p>

    
    <div>
        <a href="<?php echo PUBLIC_PATH; ?>/login.php">Iniciar Sesión</a>
        <a href="<?php echo PUBLIC_PATH; ?>/registro_persona.php">Registrarse como Usuario Persona</a>
        <a href="<?php echo PUBLIC_PATH; ?>/registro_empresa.php">Registrarse como Empresa</a>
    </div>

<div class=info>
    <h2>¿Qué es Nexora?</h2>
    <p>
        Nexora es una plataforma diseñada para facilitar la gestión de usuarios y empresas, ofreciendo herramientas de administración, seguridad y soporte.
    </p>

    <div>
        <h3>Características Principales</h3>
        <ul>
            <li>Gestión de Usuarios: Administra tus usuarios de manera eficiente.</li>
            <li>Panel Administrativo: Accede a herramientas avanzadas para la gestión del sistema.</li>
            <li>Seguridad: Protege tu información con nuestras medidas de seguridad robustas.</li>
            <li>Soporte: Contáctanos para cualquier duda o asistencia que necesites.</li>
        </ul>
</div>

<div class="planes">

    <h2>Planes de Infraestructura para Oficinas</h2>
    <p>Paquetes prediseñados para la rápida implementación de espacios de trabajo:</p>
        <ul>
        <li><strong>Plan Start-Up (4 Estaciones):</strong> Suministro de 4 equipos más instalación completa de cableado estructurado y red Wi-Fi.
    </li>

        <li><strong>Plan Business (8 Estaciones + Sala de Juntas):</strong> Suministro de 8 equipos, instalación de infraestructura en área común y configuración de oficina privada/sala de servidores.
    </li>

        <li><strong>Plan Custom (Personalizado):</strong> Consultoría para que el cliente seleccione componentes específicos, software bajo licencia y distribución física a medida.
    </li>
        </ul>

</div>

</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>