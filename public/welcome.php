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
    <div>
        <h1>¡Bienvenido!</h1>
        <p>Tu sesión se ha iniciado correctamente</p>
        
        <div>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
            <p><strong>Tipo de Usuario:</strong> <?php echo ucfirst($_SESSION['usuario_tipo']); ?></p>
        </div>
        
        <div>
            <a href="/git/login/Proyecto3ro/public/dashboard.php">Ir al Panel de Control</a><br>
            <a href="/git/login/Proyecto3ro/public/logout.php">Cerrar Sesión</a>
        </div>
        
        <p>ID de Usuario: #<?php echo $_SESSION['usuario_id']; ?></p>
    </div>
</body>
</html>
