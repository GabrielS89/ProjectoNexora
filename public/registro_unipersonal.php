<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Unipersonal</title>
</head>

<?php
    // Mostrar mensajes de error si existen en la URL
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'campos_vacios') {
            echo '<p>Por favor, rellena todos los campos obligatorios (Nombre, Email, Contraseña).</p>';
        } elseif ($_GET['error'] == 'registro_fallido') {
            echo '<p>Error al registrar: El email ya podría estar en uso.</p>';
        }
    }
    if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
        echo '<p>¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
    }
?>

<body>
    <h1>Registro para Clientes (Unipersonal)</h1>
    <form action="../src/controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="unipersonal">

        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Gmail:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Número de teléfono:</label><br>
        <input type="text" name="telefono"><br><br>

        <button type="submit">Crear cuenta personal</button>
    </form>
    <a href="/git/login/Proyecto3ro/public/login.php">Volver al login</a>
</body>
</html>