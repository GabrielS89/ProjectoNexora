<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
?>

<div class="pagina-panel">
    <div class="encabezado-pagina">
        <div>
            <p class="texto-previo">Espacio de trabajo</p>
            <h1>Panel de Control</h1>
            <p class="descripcion-pagina">Gestiona tu cuenta y accede rápidamente a las herramientas disponibles.</p>
        </div>
        <a class="btn btn-danger" href="<?php echo PUBLIC_PATH; ?>/logout.php">Cerrar Sesión</a>
    </div>

    <section class="feature-card bienvenida-panel">
        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario'); ?>!</h2>
        <p class="texto-bienvenida">Este es tu panel de control personal. Desde aquí puedes consultar tu información y continuar con tus tareas.</p>
        <div class="datos-cuenta">
            <p><strong>ID de Usuario:</strong> #<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></p>
        </div>
    </section>

    <div class="rejilla-panel">
<?php if ($es_admin): ?>
        <section class="feature-card tarjeta-panel tarjeta-admin">
            <p class="texto-previo">Administración</p>
            <h3>Panel Administrativo</h3>
            <p>Tienes acceso a herramientas de gestión de usuarios y sistema.</p>
            <a class="btn btn-primary" href="<?php echo VIEWS_PATH; ?>/adminpanel.php">Acceder al Panel de Admin</a>
        </section>
<?php endif; ?>

        <section class="feature-card tarjeta-panel">
            <p class="texto-previo">Cuenta</p>
            <h3>Mi Perfil</h3>
            <p>Consulta y edita la información de tu perfil y tus preferencias de cuenta.</p>
            <a class="btn btn-outline" href="<?php echo VIEWS_PATH; ?>/perfil.php">Ver Perfil</a>
        </section>
    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
