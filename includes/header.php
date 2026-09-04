<?php
// Header persistente con navbar. Este archivo debe incluirse al principio de cada pagina

// Incluir configuración y sesion
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
    
    <!-- Script Anti-FOUC para Cargar Tema Inicial -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'light' || savedTheme === 'dark') {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>
    <!-- Conexión CSS -->
    <link rel="stylesheet" href="<?php echo INCLUDES_PATH; ?>/css/style.css">
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
                        <a href="<?php echo PUBLIC_PATH; ?>/index.php">
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/planes.php">
                            Planes
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/dashboard.php">
                            Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/tienda.php">
                            Tienda
                        </a>
                    </li>
                    <?php if (isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3])): ?>
                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/adminpanel.php" style="color: #4ade80; font-weight: 700;">
                            ⚙ Panel Admin
                        </a>
                    </li>
                    <?php endif; ?>
                    

                    <?php else: ?>
                    <!-- Menú para usuarios no logueados -->

                    <li>
                        <a href="<?php echo PUBLIC_PATH; ?>/index.php">
                            Inicio
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/planes.php">
                            Planes
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo VIEWS_PATH; ?>/tienda.php">
                            Tienda
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

                <!-- Switch Modo Claro / Oscuro (Estilo Píldora Minimalista) -->
                <button id="theme-toggle" class="theme-switch" type="button" aria-label="Cambiar modo claro / oscuro" title="Cambiar modo de tema">
                    <span class="theme-switch-thumb"></span>
                </button>

<?php if ($isLoggedIn): ?>
<!-- Usuario logueado -->

                    <div class="user-info">
                    <a href="<?php echo VIEWS_PATH; ?>/perfil.php" style="color:inherit; text-decoration:none;">    <strong><?php echo htmlspecialchars($currentUser['email']); ?></strong> </a>
                    </div>
                    <div class="user-avatar">
                    <a href="<?php echo VIEWS_PATH; ?>/perfil.php">  <img
                                src="<?php echo htmlspecialchars($currentUser['avatar'] ?? VIEWS_PATH . '/uploads/default-avatar.png'); ?>"
                                alt="Avatar"
                                class="avatar-img"
                                style="width: 40px; height: 40px; border-radius: 50%;"
                            >
                    </a>
                    
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


        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('theme-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        });
    }
});
</script>

    <div class="page-wrapper">
    <div class="main-content">
