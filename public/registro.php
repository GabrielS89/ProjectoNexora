<?php
$page_title = 'Registro - Proyecto 3ro';
require_once(__DIR__ . '/../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');



// Mensajes de estado/errores devueltos por el autenticador
$alert = '';
$err = isset($_GET['error']) ? $_GET['error'] : null;
$msg = isset($_GET['mensaje']) ? urldecode($_GET['mensaje']) : '';

if ($err !== null) {
    switch ($err) {
        case 'campos_vacios':
            $alert = '<p class="alert-error-text"><strong>Error:</strong> Por favor, rellena todos los campos obligatorios.</p>';
            break;
        case 'password_invalido':
            $alert = '<p class="alert-error-text"><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($msg) . '</p>';
            break;
        case 'email_usado':
            $alert = '<p class="alert-error-text"><strong>Error:</strong> El email ya está registrado.</p>';
            break;
        case 'telefono_usado':
            $alert = '<p class="alert-error-text"><strong>Error:</strong> El teléfono ya está registrado.</p>';
            break;
        case 'rut_usado':
            $alert = '<p class="alert-error-text"><strong>Error:</strong> El RUT ya está registrado.</p>';
            break;
        case 'nombre_empresa_usado':
            $alert = '<p class="alert-error-text"><strong>Error:</strong> El nombre de la empresa ya está registrado.</p>';
            break;
        default:
            // Mensaje genérico: si hay detalle lo mostramos, si no mostramos la clave de error
            $alert = '<p class="alert-error-text"><strong>Error:</strong> ' . htmlspecialchars($msg ?: $err) . '</p>';
    }
}

if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    $alert = '<p class="alert-success-text"><strong>Registro exitoso:</strong> Ahora puedes iniciar sesión.</p>';
}

// Mostrar excepción interna si el autenticador la pasó en 'e'
if (isset($_GET['e']) && $_GET['e'] !== '') {
    $exc = urldecode($_GET['e']);
    $alert .= '<pre class="error-pre"><strong>Excepción:</strong> ' . htmlspecialchars($exc) . '</pre>';
}

if ($alert !== '') echo $alert;
?>

<div>

    <h1>Registro de Usuario</h1>
    <p>Selecciona el tipo de usuario:</p>
    <div class="hero-buttons">
        <button type="button" id="btnEmpresa" class="btn btn-primary" onclick="mostrarFormulario('empresa')">Registrarse como Empresa</button>
        <button type="button" id="btnPersona" class="btn btn-primary" onclick="mostrarFormulario('persona')">Registrarse como Persona</button>
        <div class="g-signin2" data-onsuccess="onSignIn"></div>
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

        <label>Calle:</label><br>
        <input type="text" name="calle" required><br><br>
        <label>Esquina:</label><br>
        <input type="text" name="esquina" required><br><br>
        <label>Número de Puerta:</label><br>
        <input type="text" name="num-puerta" required><br><br>

        <label>Sector o Industria:</label><br>
        <select name="categoria" required>
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

    <script src="https://apis.google.com/js/platform.js" async defer></script>

    

</div>

<!-- FORMULARIO EMPRESA -->
<div id="formEmpresa" class="d-none">
    <form action="../src/controllers/autenticador.php" method="POST" class="text-left">
        <h2 class="section-heading-lg-green">Registro Corporativo / Empresa</h2>
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

        <div class="divider-dashed">
            <h3 class="section-heading-green">Datos del Representante Legal / Contacto</h3>

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

<div class="text-center mt-15rem">
    <p class="text-muted-custom">¿Ya tienes una cuenta? <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="font-weight-600">Iniciar Sesión</a></p>
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