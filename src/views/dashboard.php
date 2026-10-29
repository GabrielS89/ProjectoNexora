<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
// Definir si el usuario tiene permisos de administrador (Rol 2, 3 o 4)
$rolActual = 0;
if (isset($_SESSION['usuario_rol'])) {
    $rolActual = (int)$_SESSION['usuario_rol'];
} elseif (isset($_SESSION['user']['rol'])) {
    $rolActual = (int)$_SESSION['user']['rol'];
} elseif (isset($_SESSION['user_id'])) {
    require_once(__DIR__ . '/../../database/database.php');
    if (isset($pdo)) {
        $stmt = $pdo->prepare('SELECT ID_Rol FROM usuario WHERE ID_Usuario = ? LIMIT 1');
        $stmt->execute([$_SESSION['user_id']]);
        $userRole = $stmt->fetchColumn();
        if ($userRole !== false) {
            $rolActual = (int)$userRole;
            $_SESSION['usuario_rol'] = $rolActual;
            $_SESSION['user']['rol'] = $rolActual;
        }
    }
}
$es_admin = in_array($rolActual, [2, 3, 4], true);
?>

<div class="encabezado-admin">
    <div>
        <h1 class="titulo-admin">Panel de Control</h1>
        <p class="texto-sutil">Gestiona tu información y servicios desde un solo lugar.</p>
    </div>
</div>

<div class="dashboard-stack">
    <div class="feature-card">
        <h2 class="dashboard-welcome-title">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario'); ?>!</h2>
        <p class="dashboard-welcome-text">Este es tu panel de control personal en Nexora.</p>
        
        <div class="dashboard-user-grid">
            <p><strong>ID de Usuario:</strong> <span class="dashboard-user-highlight">#<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></span></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <span class="dashboard-user-role"><?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></span></p>
        </div>
    </div>

    <div class="features-grid dashboard-actions-grid">
        <?php if ($es_admin): ?>
        <div class="feature-card">
            <h3>Panel Administrativo</h3>
            <p>Acceso completo a la gestión de usuarios, roles y configuración global del sistema.</p>
            <a href="<?php echo VIEWS_PATH; ?>/adminpanel.php" class="btn btn-primary">Acceder al Panel Admin</a>
        </div>
        <?php endif; ?>

        <div class="feature-card">
            <h3>Mi Perfil</h3>
            <p>Ver y editar la información de tu cuenta, avatar y datos personales.</p>
            <a href="<?php echo VIEWS_PATH; ?>/dashboard.php" class="btn btn-outline">Ver Perfil</a>
        </div>

        <div class="feature-card">
            <h3>Configuración</h3>
            <p>Ajusta tus preferencias de privacidad y notificaciones de tu espacio de trabajo.</p>
            <a href="<?php echo VIEWS_PATH; ?>/settings.php" class="btn btn-outline">Ir a Configuración</a>
        </div>
        
        <div class="feature-card">
            <h3>Soporte Técnico</h3>
            <p>Si necesitas asistencia o resolver dudas, nuestro equipo está disponible.</p>
            <a href="<?php echo PUBLIC_PATH; ?>/contacto.php" class="btn btn-outline">Contactar Soporte</a>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
