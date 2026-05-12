<?php
session_start();

// 1. Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /git/login/Proyecto3ro/public/login.php');
    exit();
}

// 2. Verificar el Rol (Admin = 2, Super_Admin = 3)
// IMPORTANTE: Asegúrate de que en tu archivo de login estés guardando:
// $_SESSION['usuario_rol'] = $fila['ID_Rol'];
$roles_permitidos = [2, 3];

if (!in_array($_SESSION['usuario_rol'], $roles_permitidos)) {
    // Si el rol no está en la lista (es decir, es un usuario común con ID_Rol 1)
    header('Location: /git/login/Proyecto3ro/public/index.php?error=acceso_restringido');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área Administrativa - PoisonIvy</title>
</head>
<body>
    <div>
        <h1>Área Restringida para Administradores</h1>
        <p>Bienvenido, has accedido con privilegios elevados.</p>
        
        <div>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
            <p><strong>Nivel de Acceso:</strong> 
                <?php echo ($_SESSION['usuario_rol'] == 3) ? 'Super Administrador' : 'Administrador'; ?>
            </p>
        </div>
        
        <div>
            <a href="/git/login/Proyecto3ro/public/logout.php">Cerrar Sesión</a>
        </div>
    </div>
</body>
</html>