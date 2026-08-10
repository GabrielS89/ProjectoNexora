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

<div style="text-align: center; max-width: 650px; margin: 0 auto;">
    <h1 style="margin-bottom: 0.5rem;">Registro de Usuario</h1>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">Crea tu cuenta en Nexora para acceder a nuestros servicios y soluciones.</p>

    <!-- ALERTAS Y MENSAJES -->
    <?php
    if (isset($_GET['error'])) {
        $msg = 'Ocurrió un error al procesar la solicitud.';
        if ($_GET['error'] == 'campos_vacios') {
            $msg = 'Por favor, rellena todos los campos obligatorios.';
        } elseif ($_GET['error'] == 'password_invalido') {
            $msg = '<strong>Contraseña inválida:</strong> ' . htmlspecialchars($_GET['mensaje'] ?? '');
        } elseif ($_GET['error'] == 'registro_fallido') {
            $msg = 'Error al registrar: El Email o RUT ingresado ya está registrado.';
        }
        echo '<p class="alert" style="background: rgba(220, 53, 69, 0.1); color: #d32f2f; border: 1px solid rgba(220, 53, 69, 0.3);">' . $msg . '</p>';
    }
    if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
        echo '<p class="alert" style="background: rgba(13, 125, 83, 0.12); color: var(--primary-green); border: 1px solid rgba(13, 125, 83, 0.3);">¡Registro exitoso! Ya puedes iniciar sesión.</p>';
    }
    ?>

    <!-- BOTONES SELECTORES DE TIPO DE REGISTRO -->
    <div style="background: rgba(255, 255, 255, 0.7); padding: 6px; border-radius: 12px; display: inline-flex; gap: 8px; border: 1px solid var(--card-border); margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
        <button type="button" id="btnPersona" class="btn btn-primary" onclick="mostrarFormulario('persona')">Cliente Personal</button>
        <button type="button" id="btnEmpresa" class="btn btn-outline" onclick="mostrarFormulario('empresa')">Empresa / Corporativo</button>
    </div>
</div>

<!-- FORMULARIO PERSONA (Por defecto activo) -->
<div id="formPersona" style="display: block;">
    <form action="../src/controllers/autenticador.php" method="POST" style="text-align: left;">
        <h2 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center; font-size: 1.5rem;">Registro Cliente Personal</h2>
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="persona">

        <label for="nombre_p">Nombre Completo:</label>
        <input type="text" id="nombre_p" name="nombre" placeholder="Ej. Juan Pérez" required>

        <label for="email_p">Correo Electrónico:</label>
        <input type="email" id="email_p" name="email" placeholder="ejemplo@correo.com" required>

        <label for="pass_p">Contraseña:</label>
        <small>Mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número y un símbolo (!@#$%^&*)</small>
        <input type="password" id="pass_p" name="password" placeholder="••••••••" required>

        <label for="tel_p">Número de Teléfono:</label>
        <input type="text" id="tel_p" name="telefono" placeholder="+598 99 123 456" required>

        <button type="submit" class="btn btn-primary">Crear Cuenta Personal</button>
    </form>
</div>

<!-- FORMULARIO EMPRESA -->
<div id="formEmpresa" style="display: none;">
    <form action="../src/controllers/autenticador.php" method="POST" style="text-align: left;">
        <h2 style="color: var(--primary-green); margin-bottom: 1.5rem; text-align: center; font-size: 1.5rem;">Registro Corporativo / Empresa</h2>
        <input type="hidden" name="action" value="register">
        <input type="hidden" name="tipo_usuario" value="empresa">

        <label for="nombre_emp">Nombre de la Empresa:</label>
        <input type="text" id="nombre_emp" name="nombre_empresa" placeholder="Ej. Tech Solutions S.A." required>

        <label for="email_emp">Email Corporativo:</label>
        <input type="email" id="email_emp" name="email" placeholder="contacto@empresa.com" required>

        <label for="pass_emp">Contraseña:</label>
        <small>Mínimo 8 caracteres, al menos una mayúscula, minúscula, número y carácter especial</small>
        <input type="password" id="pass_emp" name="password" placeholder="••••••••" required>

        <label for="tel_emp">Teléfono de Contacto:</label>
        <input type="text" id="tel_emp" name="telefono" placeholder="+598 2900 0000" required>

        <label for="rut_emp">RUT / Identificación Fiscal:</label>
        <input type="text" id="rut_emp" name="rut" placeholder="219998880011" required>

        <label for="dir_emp">Dirección Fiscal:</label>
        <input type="text" id="dir_emp" name="direccion" placeholder="Av. 18 de Julio 1234" required>

        <label for="sector_emp">Sector o Industria:</label>
        <select id="sector_emp" name="sector" required>
            <option value="">-- Selecciona un sector --</option>
            <option value="tecnologia">Tecnología e Informática</option>
            <option value="comercio">Comercio y Retail</option>
            <option value="industria">Industria y Logística</option>
            <option value="servicios">Servicios Profesionales</option>
        </select>

        <label for="tamano_emp">Tamaño de la Empresa:</label>
        <select id="tamano_emp" name="tamano">
            <option value="pequena">1 a 5 empleados (Pequeña)</option>
            <option value="mediana">6 a 20 empleados (Mediana)</option>
            <option value="grande">Más de 20 empleados (Grande)</option>
        </select>

        <label for="desc_emp">Descripción Breve / Objetivos:</label>
        <textarea id="desc_emp" name="descripcion" rows="3" maxlength="200" placeholder="Describe brevemente las necesidades de tu empresa..." required></textarea>

        <div style="border-top: 1.5px dashed var(--card-border); margin: 1.5rem 0; padding-top: 1rem;">
            <h3 style="color: var(--primary-green); font-size: 1.1rem; margin-bottom: 1rem;">Datos del Representante Legal / Contacto</h3>

            <label for="rep_nom">Nombre Completo del Responsable:</label>
            <input type="text" id="rep_nom" name="rep_nombre" placeholder="Ej. Carlos Rodríguez" required>

            <label for="rep_car">Cargo:</label>
            <input type="text" id="rep_car" name="rep_cargo" placeholder="Ej. Gerente de TI" required>

            <label for="rep_em">Email Directo:</label>
            <input type="email" id="rep_em" name="rep_email" placeholder="carlos@empresa.com" required>

            <label for="rep_tel">Teléfono Directo:</label>
            <input type="text" id="rep_tel" name="rep_telefono" placeholder="+598 99 888 777" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Empresa</button>
    </form>
</div>

<div style="text-align: center; margin-top: 1.5rem;">
    <p style="color: var(--text-muted);">¿Ya tienes una cuenta? <a href="<?php echo PUBLIC_PATH; ?>/login.php" style="font-weight: 600;">Iniciar Sesión</a></p>
</div>

<script>
function mostrarFormulario(tipo) {
    const formPersona = document.getElementById("formPersona");
    const formEmpresa = document.getElementById("formEmpresa");
    const btnPersona = document.getElementById("btnPersona");
    const btnEmpresa = document.getElementById("btnEmpresa");
    
    if (tipo === "empresa") {
        formEmpresa.style.display = "block";
        formPersona.style.display = "none";
        btnEmpresa.className = "btn btn-primary";
        btnPersona.className = "btn btn-outline";
    } else {
        formPersona.style.display = "block";
        formEmpresa.style.display = "none";
        btnPersona.className = "btn btn-primary";
        btnEmpresa.className = "btn btn-outline";
    }
}
</script>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
