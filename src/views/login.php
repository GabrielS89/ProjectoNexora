<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Proyecto 3ro MA</title>
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <form action="../controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="login">

        <label>Correo electrónico (Gmail):</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Entrar</button>
    </form>

    <p>¿No tienes cuenta? Regístrate como:</p>
    <ul>
        <li><a href="registro_unipersonal.php">Usuario Unipersonal</a></li>
        <li><a href="registro_empresa.php">Empresa</a></li>
    </ul>
</body>
</html>