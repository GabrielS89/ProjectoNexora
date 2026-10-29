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
            $alert = '<div class="alerta alerta--error">Por favor, rellena todos los campos obligatorios.</div>';
            break;
        case 'password_invalido':
            $alert = '<div class="alerta alerta--error"><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($msg) . '</div>';
            break;
        case 'email_usado':
            $alert = '<div class="alerta alerta--error">El email ya está registrado.</div>';
            break;
        case 'telefono_usado':
            $alert = '<div class="alerta alerta--error">El teléfono ya está registrado.</div>';
            break;
        case 'rut_usado':
            $alert = '<div class="alerta alerta--error">El RUT ya está registrado.</div>';
            break;
        case 'rut_invalido':
            $alert = '<div class="alerta alerta--error">El RUT debe tener exactamente 12 números.</div>';
            break;
        case 'nombre_empresa_usado':
            $alert = '<div class="alerta alerta--error">El nombre de la empresa ya está registrado.</div>';
            break;
        default:
            // Mensaje genérico: si hay detalle lo mostramos, si no mostramos la clave de error
            $alert = '<div class="alerta alerta--error">' . htmlspecialchars($msg ?: $err) . '</div>';
    }
}

if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    $alert = '<div class="alerta alerta--exito">Registro exitoso. Ahora puedes iniciar sesión.</div>';
}

// Mostrar excepción interna si el autenticador la pasó en 'e'
if (isset($_GET['e']) && $_GET['e'] !== '') {
    $exc = urldecode($_GET['e']);
    $alert .= '<div class="alerta alerta--error"><strong>Excepción:</strong> ' . htmlspecialchars($exc) . '</div>';
}
?>

<div class="registro-shell">
    <h1 class="titulo-admin registro-title">Registro de Usuario</h1>
    <p class="texto-sutil registro-subtitle">Crea tu cuenta en Nexora para acceder a nuestros servicios y soluciones.</p>

    <?php echo $alert; ?>

    <div class="registro-selector" aria-label="Selector de tipo de registro">
        <button type="button" id="btnPersona" class="btn btn-primary" onclick="mostrarFormulario('persona')">Cliente Personal</button>
        <button type="button" id="btnEmpresa" class="btn btn-outline" onclick="mostrarFormulario('empresa')">Empresa / Corporativo</button>
    </div>

    <!-- FORMULARIO PERSONA (Por defecto activo) -->
    <div id="formPersona" class="registro-form-panel registro-form-visible">
        <form action="../src/controllers/autenticador.php" method="POST" class="registro-form">
            <h2>Registro Cliente Personal</h2>
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
            <input type="text" id="tel_p" name="telefono" placeholder="099 999 999" required>

            <button type="submit" class="btn btn-primary">Crear Cuenta Personal</button>
        </form>
    </div>

    <!-- FORMULARIO EMPRESA -->
    <div id="formEmpresa" class="registro-form-panel">
        <form action="../src/controllers/autenticador.php" method="POST" class="registro-form">
            <h2>Registro Corporativo / Empresa</h2>
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
            <input type="text" id="tel_emp" name="telefono" placeholder="099 999 999" required>

            <label for="rut_emp">RUT / Identificación Fiscal:</label>
            <input type="text" id="rut_emp" name="rut" placeholder="219998880011" required>

            <label for="calle_emp">Calle:</label>
            <input type="text" id="calle_emp" name="calle" placeholder="Calle principal" required>

            <label for="esquina_emp">Esquina:</label>
            <input type="text" id="esquina_emp" name="esquina" placeholder="Esquina">

            <label for="numpuerta_emp">Número de Puerta:</label>
            <input type="text" id="numpuerta_emp" name="numpuerta" placeholder="123" required>

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

            <div class="registro-subsection">
                <h3>Datos del Representante Legal / Contacto</h3>

                <label for="rep_nom">Nombre Completo del Responsable:</label>
                <input type="text" id="rep_nom" name="rep_nombre" placeholder="Ej. Carlos Rodríguez" required>

                <label for="rep_car">Cargo:</label>
                <input type="text" id="rep_car" name="rep_cargo" placeholder="Ej. Gerente de TI" required>

                <label for="rep_em">Email Directo:</label>
                <input type="email" id="rep_em" name="rep_email" placeholder="carlos@empresa.com" required>

                <label for="rep_tel">Teléfono Directo:</label>
                <input type="text" id="rep_tel" name="rep_telefono" placeholder="099 999 999" required>
            </div>

            <button type="submit" class="btn btn-primary">Registrar Empresa</button>
        </form>
    </div>

    <div class="registro-login-link">
        <p>¿Ya tienes una cuenta? <a href="<?php echo PUBLIC_PATH; ?>/login.php">Iniciar Sesión</a></p>
    </div>
</div>

<script>
function mostrarFormulario(tipo) {
    const formPersona = document.getElementById('formPersona');
    const formEmpresa = document.getElementById('formEmpresa');
    const btnPersona = document.getElementById('btnPersona');
    const btnEmpresa = document.getElementById('btnEmpresa');

    const mostrarPersona = tipo === 'persona';
    formPersona.classList.toggle('registro-form-visible', mostrarPersona);
    formEmpresa.classList.toggle('registro-form-visible', !mostrarPersona);
    btnPersona.className = mostrarPersona ? 'btn btn-primary' : 'btn btn-outline';
    btnEmpresa.className = mostrarPersona ? 'btn btn-outline' : 'btn btn-primary';
}
</script>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>