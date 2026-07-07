<?php
$page_title = 'Registro - Proyecto 3ro';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Mensajes de error
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
    <h1>Registro de Usuario</h1>
    <p>Selecciona el tipo de usuario:</p>
    <div class="hero-buttons">
        <button type="button" id="btnEmpresa" class="btn btn-primary" onclick="mostrarFormulario('empresa')">Registrarse como Empresa</button>
        <button type="button" id="btnPersona" class="btn btn-primary" onclick="mostrarFormulario('persona')">Registrarse como Persona</button>
    </div>
</div>

<!-- FORMULARIO EMPRESA -->
<div id="formEmpresa">
    <h2>Registro para Empresas</h2>
    <form action="../src/controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="empresa">

        <label>Nombre de la Empresa:</label><br>
        <input type="text" name="nombre_empresa" required><br><br>

        <label>Email de la Empresa:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <small>Mínimo 8 caracteres, mayúscula, minúscula, número y carácter especial (!@#$%^&*)</small><br>
        <input type="password" name="password" required><br><br>

        <label>Número de contacto:</label><br>
        <input type="text" name="telefono" required><br><br>

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
</div>

<!-- FORMULARIO PERSONA -->
<div id="formPersona">
    <h2>Registro para Clientes Regulares</h2>
    <form action="../src/controllers/autenticador.php" method="POST">
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="persona">

        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Contraseña:</label><br>
        <small>Mínimo 8 caracteres, mayúscula, minúscula, número y carácter especial (!@#$%^&*)</small><br>
        <input type="password" name="password" required><br><br>

        <label>Número de teléfono:</label><br>
        <input type="text" name="telefono" required><br><br>

        <button type="submit">Crear cuenta personal</button>
    </form>
</div>

<a href="<?php echo PUBLIC_PATH; ?>/login.php">Volver al login</a>

<script>
function mostrarFormulario(tipo) {
    document.getElementById("formEmpresa").style.display = (tipo === "empresa") ? "block" : "none";
    document.getElementById("formPersona").style.display = (tipo === "persona") ? "block" : "none";
    
    // Cambiar estilo de los botones
    var btnEmpresa = document.getElementById("btnEmpresa");
    var btnPersona = document.getElementById("btnPersona");
    
    if (tipo === "empresa") {
        btnEmpresa.style.opacity = "1";
        btnPersona.style.opacity = "0.5";
    } else if (tipo === "persona") {
        btnPersona.style.opacity = "1";
        btnEmpresa.style.opacity = "0.5";
    }
}
</script>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
