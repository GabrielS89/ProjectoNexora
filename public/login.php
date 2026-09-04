<?php
$page_title = 'Login - Proyecto 3ro MA';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
?>

<div style="max-width: 500px; margin: 2rem auto; padding: 0 1rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1>Iniciar <span style="color: var(--primary-green-hover);">Sesión</span></h1>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Accede a tu cuenta corporativa o personal en Nexora.</p>
    </div>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert-box alert-danger">
            <span class="alert-tag">ERROR</span>
            <div>
                <?php
                    if ($_GET['error'] == 'campos_vacios') {
                        echo 'Por favor, completa todos los campos requeridos.';
                    } elseif ($_GET['error'] == 'password_invalido') {
                        echo 'Contraseña inválida: ' . htmlspecialchars($_GET['mensaje'] ?? '');
                    } elseif ($_GET['error'] == 'credenciales_invalidas') {
                        echo 'Correo electrónico o contraseña incorrectos. Verifica tus datos.';
                    } elseif ($_GET['error'] == 'acceso_restringido') {
                        echo 'No tienes permisos para acceder al área solicitada.';
                    } else {
                        echo 'Ocurrió un error al iniciar sesión.';
                    }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['registro']) && $_GET['registro'] == 'exito'): ?>
        <div class="alert-box alert-success">
            <span class="alert-tag">ÉXITO</span>
            <div>¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta.</div>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['logout']) && $_GET['logout'] == 'exito'): ?>
        <div class="alert-box alert-success">
            <span class="alert-tag">INFO</span>
            <div>Sesión cerrada correctamente.</div>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo CONTROLLERS_PATH ? '/Proyecto3ro/src/controllers/autenticador.php' : '../src/controllers/autenticador.php'; ?>" method="POST">
        <input type="hidden" name="action" value="login">

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" placeholder="ejemplo@empresa.com" required autocomplete="email">

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required autocomplete="current-password">

        <button type="submit" class="btn btn-primary btn-block" style="margin-top: 0.5rem;">Entrar al Sistema</button>
    </form>

    <div style="text-align: center; margin-top: 1.5rem; color: var(--text-muted);">
        <p>¿Aún no tienes una cuenta?</p>
        <a href="<?php echo PUBLIC_PATH; ?>/registro.php" class="btn btn-outline btn-sm" style="margin-top: 0.5rem;">Crear Nueva Cuenta</a>
    </div>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>