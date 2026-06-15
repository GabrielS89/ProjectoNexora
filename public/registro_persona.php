<?php
$page_title = 'Registro Unipersonal - Proyecto 3ro';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Mostrar mensajes de error
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'campos_vacios') {
        echo '<p>Por favor, rellena todos los campos obligatorios (Nombre, Email, Contraseña, Teléfono).</p>';
    } elseif ($_GET['error'] == 'password_invalido') {
        echo '<p><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($_GET['mensaje']) . '</p>';
    } elseif ($_GET['error'] == 'registro_fallido') {
        echo '<p>Error al registrar: El email ya podría estar en uso.</p>';
    }
}
if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    echo '<p>¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
}
?>

<div>
    <h1>Registro para Clientes (Unipersonal)</h1>
    <form action="../src/controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="unipersonal">

        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Gmail:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <small>Mínimo 8 caracteres, mayúscula, minúscula, número y carácter especial (!@#$%^&*)</small><br>
        <input type="password" name="password" required><br><br>

        <label>Número de teléfono:</label><br>
        <input type="text" name="telefono" required><br><br>

        <button type="submit">Crear cuenta personal</button>
    </form>
    <a href="<?php echo PUBLIC_PATH; ?>/login.php">Volver al login</a>
</div>

<div>
    <hr>
    <p>¿Eres una empresa? Regístrate aquí:</p>
    <a href="<?php echo PUBLIC_PATH; ?>/registro_empresa.php">Registrarse como Empresa</a>
    <hr>
    
</div>
<?php require_once(INCLUDES_DIR . '/footer.php'); ?>