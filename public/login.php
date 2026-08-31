<?php
$page_title = 'Login - Proyecto 3ro MA';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<div class="container-centered-sm">
    <h1 class="mb-05rem">Iniciar Sesión</h1>
    <p class="text-muted-custom mb-15rem">Ingresa tus credenciales para acceder a Nexora</p>
    
    <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'campos_vacios') {
                echo '<p class="alert alert-danger-box">Por favor, completa todos los campos.</p>';
            } elseif ($_GET['error'] == 'password_invalido') {
                echo '<p class="alert alert-danger-box"><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($_GET['mensaje']) . '</p>';
            } elseif ($_GET['error'] == 'credenciales_invalidas') {
                echo '<p class="alert alert-danger-box">Email o contraseña inválidos.</p>';
            }
        }
        if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
            echo '<p class="alert alert-success-box">¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
        }
        if (isset($_GET['logout']) && $_GET['logout'] == 'exito') {
            echo '<p class="alert alert-success-box">Sesión cerrada correctamente.</p>';
        }
    ?>
    
    <form action="../src/controllers/autenticador.php" method="POST" class="text-left">
        <input type="hidden" name="action" value="login">

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>

    <div class="test-accounts-box">
        <p class="test-accounts-title">🔑 Cuentas de Prueba Disponibles:</p>
        <p class="mb-2px"><strong>Super Admin:</strong> <code>admin@nexora.com</code> &nbsp;|&nbsp; <strong>Clave:</strong> <code>Admin123!</code></p>
        <p class="mb-2px"><strong>Admin Soporte:</strong> <code>admin2@nexora.com</code> &nbsp;|&nbsp; <strong>Clave:</strong> <code>Admin123!</code></p>
        <p class="mb-2px"><strong>Usuario Cliente:</strong> <code>usuario@nexora.com</code> &nbsp;|&nbsp; <strong>Clave:</strong> <code>User123!</code></p>
    </div>

    <p class="text-muted-custom mt-15rem">
        ¿No tienes cuenta? <a href="<?php echo PUBLIC_PATH; ?>/registro.php" class="font-weight-600">Regístrate aquí</a>
    </p>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>