<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Proyecto 3ro MA</title>
</head>
<body>
    <div>
        <h1>Iniciar Sesión</h1>
        
        <?php
            if (isset($_GET['error'])) {
                if ($_GET['error'] == 'campos_vacios') {
                    echo '<p>Por favor, completa todos los campos.</p>';
                } elseif ($_GET['error'] == 'credenciales_invalidas') {
                    echo '<p>Email o contraseña inválidos.</p>';
                }
            }
            if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
                echo '<p>¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
            }
            if (isset($_GET['logout']) && $_GET['logout'] == 'exito') {
                echo '<p>Sesión cerrada correctamente.</p>';
            }
        ?>
        
        <form action="../src/controllers/autenticador.php" method="POST">
            <input type="hidden" name="action" value="login">

            <label for="email">Correo electrónico:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Entrar</button>
        </form>

        <p>¿No tienes cuenta? Regístrate como:</p>
        <ul>
            <li><a href="registro_unipersonal.php">Usuario Unipersonal</a></li>
            <li><a href="registro_empresa.php">Empresa</a></li>
        </ul>
    </div>
</body>
</html>