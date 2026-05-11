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
    <title>Bienvenido - Proyecto 3ro MA</title>
    
</head>
<body>
    <div class="container">
        <h1>¡Bienvenido!</h1>
        <p class="welcome-message">Tu sesión se ha iniciado correctamente</p>
        
        <div class="user-info">
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
            <p><strong>Tipo de Usuario:</strong></p>
            <span class="user-type"><?php echo ucfirst($_SESSION['usuario_tipo']); ?></span>
        </div>
        
        <div class="btn-group">
            <a href="/git/login/Proyecto3ro/public/dashboard.php" class="btn btn-primary">Ir al Panel de Control</a>
            <a href="/git/login/Proyecto3ro/public/logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        
        <p class="footer-message">ID de Usuario: #<?php echo $_SESSION['usuario_id']; ?></p>
    </div>
</body>
</html>
