<?php
// Header persistente con navbar. Este archivo debe incluirse al principio de cada página

// Incluir configuración y sesión
require_once(INCLUDES_DIR . '/config.php');

// Obtener información del usuario si está logueado
$isLoggedIn = isUserLoggedIn();
$currentUser = getCurrentUser();

// Verificación de autenticación requerida
// Las páginas pueden definir $require_login = true antes de incluir este archivo
if (isset($require_login) && $require_login && !$isLoggedIn) {
    header('Location: ' . PUBLIC_PATH . '/login.php');
    exit();
}

// Verificación de permisos de administrador
// Las páginas pueden definir $require_admin = true antes de incluir este archivo
if (isset($require_admin) && $require_admin) {
    if (!$isLoggedIn || !isset($_SESSION['usuario_rol']) || !in_array($_SESSION['usuario_rol'], [2, 3])) {
        header('Location: ' . PUBLIC_PATH . '/index.php?error=acceso_restringido');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Fuentes Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Conexión CSS -->
    <link rel="stylesheet" href="<?php echo INCLUDES_PATH; ?>/css/style.css">
    <!-- Script temprano para evitar parpadeo de tema -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <title><?php echo $page_title ?? 'Proyecto 3ro'; ?></title>

</head>
<!-- Abrir Body en Header-->

<body>

<!-- Navbar -->
<nav>

            <div class="navbar-container">
            <a href="<?php echo PUBLIC_PATH; ?>/index.php" class="navbar-brand"> Nexora </a>
            <ul class="navbar-menu">

<?php if ($isLoggedIn): ?>
<!-- Menú para usuarios logueados -->
                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/dashboard.php">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo PUBLIC_PATH; ?>/index.php">
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/tienda.php">
                            Tienda
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo PUBLIC_PATH; ?>/index.php#planes">
                            Planes
                        </a>
                    </li>

                    <?php else: ?>
                    <!-- Menú para usuarios no logueados -->

                    <li>
                        <a href="<?php echo PUBLIC_PATH; ?>/index.php">
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo PUBLIC_PATH; ?>/index.php#acercaDe">
                            Acerca de
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo PUBLIC_PATH; ?>/contacto.php">
                            Contacto
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            <div class="auth-buttons">
                <!-- SWITCH CAMBIO DE TEMA CLARO / OSCURO -->
                <div class="theme-switch-wrapper" title="Modo Claro / Oscuro">
                    <label class="theme-switch" for="theme-toggle">
                        <input type="checkbox" id="theme-toggle" onchange="toggleTheme(this)">
                        <span class="theme-slider"></span>
                    </label>
                </div>

                <?php if ($isLoggedIn): ?>

<?php if ($isLoggedIn): ?>
<!-- Usuario logueado -->

                    <div class="user-info">
                        <strong><?php echo htmlspecialchars($currentUser['email']); ?></strong>
                    </div>
                    <div class="user-avatar">
                        <img
                                src="<?php echo htmlspecialchars($currentUser['avatar'] ?? VIEWS_PATH . '/uploads/default-avatar.png'); ?>"
                                alt="Avatar"
                                class="avatar-img"
                                style="width: 40px; height: 40px; border-radius: 50%;"
                            >
                    </div>
                    <a href="<?php echo PUBLIC_PATH; ?>/logout.php" class="btn btn-danger">
                        Cerrar Sesión
                    </a>



<?php else: ?>
<!-- Usuario no logueado -->

                        <div class="user-avatar">
                            <img
                                src="<?php echo VIEWS_PATH; ?>/uploads/default-avatar.png"
                                alt="Avatar predeterminado"
                                class="avatar-img"
                                style="width: 40px; height: 40px; border-radius: 50%;"
                            >
                        </div>
                        <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-primary">
                            Iniciar Sesión
                        </a>
                        <a href="<?php echo PUBLIC_PATH; ?>/registro.php" class="btn btn-primary">
                            Registro
                        </a>
<?php endif; ?>


            <!-- SCRIPT GESTOR DE TEMA CLARO / OSCURO -->
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const currentTheme = localStorage.getItem('theme') || 'light';
                    const toggleCheckbox = document.getElementById('theme-toggle');
                    if (toggleCheckbox) {
                        toggleCheckbox.checked = (currentTheme === 'dark');
                    }
                });

                function toggleTheme(checkbox) {
                    const theme = checkbox.checked ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', theme);
                    localStorage.setItem('theme', theme);
                }
            </script>

        </div>
    </div>
</nav>



    <div class="page-wrapper">
    <div class="main-content">
