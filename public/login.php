<?php
$page_title = 'Login - Proyecto 3ro MA';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<div>
    <hr>
    <h1>Iniciar Sesión</h1>
    
    <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'campos_vacios') {
                echo '<p> Por favor, completa todos los campos.</p>';
            } elseif ($_GET['error'] == 'password_invalido') {
                echo '<p><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($_GET['mensaje']) . '</p>';
            } elseif ($_GET['error'] == 'credenciales_invalidas') {
                echo '<p> Email o contraseña inválidos.</p>';
            }
        }
        if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
            echo '<p> ¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
        }
        if (isset($_GET['logout']) && $_GET['logout'] == 'exito') {
            echo '<p> Sesión cerrada correctamente.</p>';
        }
    ?>
    
    <form action="../src/controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="login">

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Entrar</button>
    </form>

    <hr>

    <p>¿No tienes cuenta? Regístrate como:</p>
    <div>
        <a href="<?php echo PUBLIC_PATH; ?>/registro_persona.php">Usuario Unipersonal</a>
        <a href="<?php echo PUBLIC_PATH; ?>/registro_empresa.php">Empresa</a>
    </div>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>