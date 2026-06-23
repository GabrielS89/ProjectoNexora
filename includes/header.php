<?php
// Header persistente con navbar Este archivo debe incluirse al principio de cada página

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
        <!-- Fuentes de Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <!-- CONEXIÓN DINÁMICA AL CSS -->
    <link rel="stylesheet" href="<?php echo INCLUDES_PATH; ?>/css/style.css">
    <title><?php echo $page_title ?? 'Proyecto 3ro'; ?></title>
</head>
    <!-- Abrir Body en Header-->

<body>
    <!-- NAVBAR -->
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
                    <a href="<?php echo PUBLIC_PATH; ?>/registro_empresa.php" class="btn btn-primary">
                        Registrarse como Empresa
                    </a>
                    <a href="<?php echo PUBLIC_PATH; ?>/registro_persona.php" class="btn btn-primary">
                        Registrarse como Persona
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </nav>

<div class="page-wrapper">
    <div class="main-content">
