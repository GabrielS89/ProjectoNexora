<?php
$page_title = 'Login - Proyecto 3ro MA';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<div style="text-align: center; max-width: 560px; margin: 0 auto;">
    <h1 style="margin-bottom: 0.5rem;">Iniciar Sesión</h1>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Ingresa tus credenciales para acceder a Nexora</p>
    
    <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'campos_vacios') {
                echo '<p class="alert" style="background: rgba(220, 53, 69, 0.1); color: #d32f2f; border: 1px solid rgba(220, 53, 69, 0.3);">Por favor, completa todos los campos.</p>';
            } elseif ($_GET['error'] == 'password_invalido') {
                echo '<p class="alert" style="background: rgba(220, 53, 69, 0.1); color: #d32f2f; border: 1px solid rgba(220, 53, 69, 0.3);"><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($_GET['mensaje']) . '</p>';
            } elseif ($_GET['error'] == 'credenciales_invalidas') {
                echo '<p class="alert" style="background: rgba(220, 53, 69, 0.1); color: #d32f2f; border: 1px solid rgba(220, 53, 69, 0.3);">Email o contraseña inválidos.</p>';
            }
        }
        if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
            echo '<p class="alert" style="background: rgba(13, 125, 83, 0.12); color: var(--primary-green); border: 1px solid rgba(13, 125, 83, 0.3);">¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
        }
        if (isset($_GET['logout']) && $_GET['logout'] == 'exito') {
            echo '<p class="alert" style="background: rgba(13, 125, 83, 0.12); color: var(--primary-green); border: 1px solid rgba(13, 125, 83, 0.3);">Sesión cerrada correctamente.</p>';
        }
    ?>
    
    <form action="../src/controllers/autenticador.php" method="POST" style="text-align: left;">
        <input type="hidden" name="action" value="login">

        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="••••••••" required>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>

    <p style="color: var(--text-muted); margin-top: 1.5rem;">
        ¿No tienes cuenta? <a href="<?php echo PUBLIC_PATH; ?>/registro.php" style="font-weight: 600;">Regístrate aquí</a>
    </p>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>