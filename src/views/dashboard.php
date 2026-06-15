<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
?>

<div>
    <h1>Panel de Control</h1>
    <a href="<?php echo PUBLIC_PATH; ?>/logout.php">Cerrar Sesión</a>
</div>

<div>
    <div>
        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario'); ?>!</h2>
        <p>Este es tu panel de control personal donde puedes gestionar tu cuenta y acceder a las herramientas disponibles.</p>
        
        <div>
            <p><strong>ID de Usuario:</strong> #<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></p>
        </div>
    </div>
    <br>
    <div>

    <h2>Actualice su Foto de Perfil</h2>
    <p>Sube una nueva foto de perfil para personalizar tu cuenta.</p>

    </div>
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
            <a href="<?php echo VIEWS_PATH; ?>/dashboard.php">Ver Perfil</a>
        </div>
        
        <br>

        <div>
            <h3>Configuración</h3>
            <p>Ajusta tus preferencias de privacidad y notificaciones del sistema.</p>
            <a href="<?php echo VIEWS_PATH; ?>/settings.php">Ir a Configuración</a>
        </div>
        
        <br>

        <div>
            <h3>Soporte</h3>
            <p>Si tienes dudas o necesitas ayuda, contáctate con nuestro equipo de soporte.</p>
            <a href="<?php echo VIEWS_PATH; ?>/support.php">Contactar Soporte</a>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
