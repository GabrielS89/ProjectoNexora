<?php
$require_admin = true;
$page_title = 'Panel de Administración - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Determinar qué página/subdivisión mostrar
$current_page = isset($_GET['page']) ? $_GET['page'] : 'usuarios';
$usuario_rol_actual = isset($_SESSION['usuario_rol']) ? (int)$_SESSION['usuario_rol'] : 0;

if ($usuario_rol_actual === 4) {
    $valid_pages = ['instalacion'];
    $current_page = 'instalacion';
    if (isset($_GET['page']) && $_GET['page'] !== 'instalacion') {
        header('Location: ?page=instalacion');
        exit();
    }
} elseif (in_array($usuario_rol_actual, [2, 3], true)) {
    $valid_pages = ['usuarios', 'tienda', 'proveedores', 'cotizaciones', 'instalacion'];
} else {
    $valid_pages = ['instalacion'];
    $current_page = 'instalacion';
}

if (!in_array($current_page, $valid_pages, true)) {
    $current_page = 'usuarios';
}

if ($current_page === 'instalacion' && !in_array($usuario_rol_actual, [2, 3, 4], true)) {
    header('Location: ' . VIEWS_PATH . '/dashboard.php?error=acceso_restringido');
    exit();
}
?>

<div class="encabezado-admin">
    
<!-- NAVEGACIÓN SUB-PESTAÑAS DE ADMINISTRACIÓN -->
<div class="subpestanas-admin">
    <?php if ($usuario_rol_actual === 4): ?>
        <a href="?page=instalacion" class="pestana <?php echo ($current_page === 'instalacion') ? 'activa' : ''; ?>">Departamento de Instalación</a>
    <?php else: ?>
        <a href="?page=usuarios" class="pestana <?php echo ($current_page === 'usuarios') ? 'activa' : ''; ?>">Gestión de Usuarios</a>
        <a href="?page=tienda" class="pestana <?php echo ($current_page === 'tienda') ? 'activa' : ''; ?>">Gestión de Productos (Tienda)</a>
        <a href="?page=proveedores" class="pestana <?php echo ($current_page === 'proveedores') ? 'activa' : ''; ?>">Gestión de Proveedores (Tienda)</a>
        <a href="?page=cotizaciones" class="pestana <?php echo ($current_page === 'cotizaciones') ? 'activa' : ''; ?>">Gestión de Solicitudes (Planes)</a>
        <a href="?page=instalacion" class="pestana <?php echo ($current_page === 'instalacion') ? 'activa' : ''; ?>">Departamento de Instalación</a>
    <?php endif; ?>
</div>

<div>
        <h1 class="titulo-admin">
            <?php 
                if ($current_page === 'usuarios') echo 'Gestión de Usuarios';
                elseif ($current_page === 'tienda') echo 'Administración de Tienda';
                elseif ($current_page === 'cotizaciones') echo 'Gestión de Solicitudes';
                elseif ($current_page === 'instalacion') echo 'Departamento de Instalación';
                else echo 'Panel de Administración';
            ?>
        </h1>
    
    </div>
</div>

<?php if ($current_page === 'usuarios'): ?>
    <?php $_REQUEST['partial'] = 1; include(__DIR__ . '/usuariospanel.php'); ?>
<?php elseif ($current_page === 'tienda'): ?>
    <?php $_REQUEST['partial'] = 1; include(__DIR__ . '/adminpanelTienda.php'); ?>
<?php elseif ($current_page === 'proveedores'): ?>
    <?php $_REQUEST['partial'] = 1; include(__DIR__ . '/proveedorespanel.php'); ?>
<?php elseif ($current_page === 'cotizaciones'): ?>
    <?php $_REQUEST['partial'] = 1; include(__DIR__ . '/cotizaciones_panel.php'); ?>
<?php elseif ($current_page === 'instalacion'): ?>
    <?php $_REQUEST['partial'] = 1; include(__DIR__ . '/instalacion_panel.php'); ?>
<?php endif; ?>
