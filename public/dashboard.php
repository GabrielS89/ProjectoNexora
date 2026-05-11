<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: /git/login/Proyecto3ro/public/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Proyecto 3ro MA</title>
</head>
<body>
    <div>
        <h1>Panel de Control</h1>
        <a href="/git/login/Proyecto3ro/public/logout.php">Cerrar Sesión</a>
    </div>
    
    <div>
        <div>
            <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_email']); ?>!</h2>
            <p>Este es tu panel de control personal donde puedes gestionar tu cuenta y acceder a las herramientas disponibles.</p>
            
            <div>
                <p><strong>ID de Usuario:</strong> #<?php echo $_SESSION['usuario_id']; ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
                <p><strong>Tipo de Usuario:</strong> <?php echo ucfirst($_SESSION['usuario_tipo']); ?></p>
            </div>
        </div>
        
        <div>
            <div>
                <h3>Mi Perfil</h3>
                <p>Ver y editar la información de tu perfil así como tus preferencias de cuenta.</p>
                <a href="dashboard.php">Ver Perfil</a>
            </div>
            
            <div>
                <h3>Configuración</h3>
                <p>Ajusta tus preferencias de privacidad y notificaciones del sistema.</p>
                <a href="dashboard.php">Ir a Configuración</a>
            </div>
            
            <div>
                <h3>Soporte</h3>
                <p>Si tienes dudas o necesitas ayuda, contáctate con nuestro equipo de soporte.</p>
                <a href="dashboard.php">Contactar Soporte</a>
            </div>
        </div>
    </div>
</body>
</html>
