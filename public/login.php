<?php
$page_title = 'Login - Proyecto 3ro MA';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<div class="registro-shell login-shell">
    <h1 class="titulo-admin login-title">Iniciar Sesión</h1>
    <p class="texto-sutil login-subtitle">Ingresa tus credenciales para acceder a Nexora</p>

    <?php
    if (isset($_GET['error'])) {
        if ($_GET['error'] == 'campos_vacios') {
            echo '<p class="alerta alerta--error">Por favor, completa todos los campos.</p>';
        } elseif ($_GET['error'] == 'password_invalido') {
            echo '<p class="alerta alerta--error"><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($_GET['mensaje']) . '</p>';
        } elseif ($_GET['error'] == 'credenciales_invalidas') {
            echo '<p class="alerta alerta--error">Email o contraseña inválidos.</p>';
        }
    }
    if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
        echo '<p class="alerta alerta--exito">¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
    }
    if (isset($_GET['logout']) && $_GET['logout'] == 'exito') {
        echo '<p class="alerta alerta--exito">Sesión cerrada correctamente.</p>';
    }
    ?>

    <form action="../src/controllers/autenticador.php" method="POST" class="login-form">
        <input type="hidden" name="action" value="login">

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>

    <p class="texto-sutil login-register-link">
        ¿No tienes cuenta? <a href="<?php echo PUBLIC_PATH; ?>/registro.php">Regístrate aquí</a>
    </p>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>