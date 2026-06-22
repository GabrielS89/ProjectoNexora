<?php
$page_title = 'Registro Empresa - Proyecto 3ro';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Mostrar mensajes de error
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'campos_vacios') {
        echo '<p>Por favor, rellena todos los campos obligatorios.</p>';
    } elseif ($_GET['error'] == 'password_invalido') {
        echo '<p><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($_GET['mensaje']) . '</p>';
    } elseif ($_GET['error'] == 'registro_fallido') {
        echo '<p>Error al registrar: El Email o el RUT ya podrían estar en uso.</p>';
    }
}
if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    echo '<p>¡Registro exitoso! Ahora puedes iniciar sesión.</p>';
}
?>

<div>
    <h1>Registro para Empresas</h1>
    <form action="../src/controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="empresa">

        <h3>Datos de la Cuenta</h3>
        <label>Nombre de la Empresa:</label><br>
        <input type="text" name="nombre_empresa" required><br><br>

        <label>Email de la Empresa:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <small>Mínimo 8 caracteres, mayúscula, minúscula, número y carácter especial (!@#$%^&*)</small><br>
        <input type="password" name="password" required><br><br>

        <label>Número de contacto:</label><br>
        <input type="text" name="telefono" required><br><br>

        <h3>Información Corporativa</h3>
        <label>RUT / Identificación Fiscal:</label><br>
        <input type="text" name="rut" required><br><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccion" required><br><br>

        <label>Sector o Industria:</label><br>
        <select name="sector" required>
            <option value="">-- Selecciona un sector --</option>
            <option value="tecnologia">Tecnología</option>
            <option value="comercio">Comercio</option>
            <option value="industria">Industria</option>
        </select><br><br>

        <label>Tamaño de Empresa:</label><br>
        <select name="tamano">
            <option value="pequena">1 a 5 empleados</option>
            <option value="mediana">6 a 20 empleados</option>
            <option value="grande">Más de 20 empleados</option>
        </select><br><br>

        <label>Descripción breve (Objetivos):</label><br>
        <textarea name="descripcion" maxlength="200" required></textarea><br><br>

        <h3>Datos del Representante</h3>
        <label>Nombre completo del responsable:</label><br>
        <input type="text" name="rep_nombre" required><br><br>

        <label>Cargo:</label><br>
        <input type="text" name="rep_cargo" required><br><br>

        <label>Email personal:</label><br>
        <input type="email" name="rep_email" required><br><br>

        <label>Teléfono directo:</label><br>
        <input type="text" name="rep_telefono" required><br><br>

        <button type="submit">Registrar Empresa</button>
    </form>
    <a href="<?php echo PUBLIC_PATH; ?>/login.php">Volver al login</a>
</div>

<div>
    <hr>
    <p>¿Eres una persona individual? Regístrate aquí:</p>
    <a href="<?php echo PUBLIC_PATH; ?>/registro_persona.php">👤 Registrarse como Usuario Unipersonal</a>
    <hr>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>