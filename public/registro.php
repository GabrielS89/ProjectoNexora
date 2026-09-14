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
            $alert = '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span>Por favor, rellena todos los campos obligatorios.</span></div>';
            break;
        case 'password_invalido':
            $alert = '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span><strong>Contraseña inválida:</strong><br>' . htmlspecialchars($msg) . '</span></div>';
            break;
        case 'email_usado':
            $alert = '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span>El email ya está registrado.</span></div>';
            break;
        case 'telefono_usado':
            $alert = '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span>El teléfono ya está registrado.</span></div>';
            break;
        case 'rut_usado':
            $alert = '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span>El RUT ya está registrado.</span></div>';
            break;
        case 'nombre_empresa_usado':
            $alert = '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span>El nombre de la empresa ya está registrado.</span></div>';
            break;
        default:
            // Mensaje genérico: si hay detalle lo mostramos, si no mostramos la clave de error
            $alert = '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span>' . htmlspecialchars($msg ?: $err) . '</span></div>';
    }
}

if (isset($_GET['registro']) && $_GET['registro'] == 'exito') {
    $alert = '<div class="alert-box alert-success"><span class="alert-tag">ÉXITO</span><span>Registro exitoso. Ahora puedes iniciar sesión.</span></div>';
}

// Mostrar excepción interna si el autenticador la pasó en 'e'
if (isset($_GET['e']) && $_GET['e'] !== '') {
    $exc = urldecode($_GET['e']);
    $alert .= '<div class="alert-box alert-danger"><span class="alert-tag">ERROR</span><span><strong>Excepción:</strong> ' . htmlspecialchars($exc) . '</span></div>';
}
?>

<main class="registro-page">
    <div class="encabezado-pagina registro-header">
        <div>
            <p class="texto-previo">Crear una cuenta</p>
            <h1>Registro de Usuario</h1>
            <p class="descripcion-pagina">Elige el perfil que mejor representa tu forma de usar Nexora.</p>
        </div>
        <a class="btn btn-outline" href="<?php echo PUBLIC_PATH; ?>/login.php">Ya tengo una cuenta</a>
    </div>

<?php if ($alert !== '') echo $alert; ?>

    <section class="registro-tipos">
        <div class="registro-tipos-heading">
            <div>
                <p class="texto-previo">Tipos de usuario</p>
                <h2>¿Cómo quieres participar?</h2>
            </div>
            <p>Podrás completar los datos específicos según el perfil que elijas.</p>
        </div>

        <div class="registro-tipos-grid">
            <article class="registro-tipo-card">
                <span class="registro-tipo-icon">EMP</span>
                <h3>Empresa</h3>
                <p>Para organizaciones que buscan mostrar su actividad, sector y necesidades.</p>
                <ul>
                    <li>Perfil corporativo</li>
                    <li>Datos de contacto y ubicación</li>
                    <li>Información del representante</li>
                </ul>
                <button type="button" id="btnEmpresa" class="btn btn-primary" data-registro-tipo="empresa">Registrarme como Empresa</button>
            </article>

            <article class="registro-tipo-card">
                <span class="registro-tipo-icon">PER</span>
                <h3>Persona</h3>
                <p>Para clientes que desean acceder a sus herramientas y gestionar su cuenta.</p>
                <ul>
                    <li>Cuenta personal</li>
                    <li>Registro rápido</li>
                    <li>Acceso a planes y servicios</li>
                </ul>
                <button type="button" id="btnPersona" class="btn btn-outline" data-registro-tipo="persona">Registrarme como Persona</button>
            </article>
        </div>
    </section>

    <!-- FORMULARIO EMPRESA -->
<div id="formEmpresa" class="registro-form-panel">
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
        <input type="text" name="numpuerta" required><br><br>

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

        <button type="submit" class="btn btn-primary btn-block">Registrar Empresa</button>
    </form>
</div>

    <!-- FORMULARIO PERSONA -->
<div id="formPersona" class="registro-form-panel">
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

        <button type="submit" class="btn btn-primary btn-block">Crear cuenta personal</button>
    </form>

</div>

    <a class="registro-back-link" href="<?php echo PUBLIC_PATH; ?>/login.php">Volver al login</a>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-registro-tipo]').forEach(function (button) {
        button.addEventListener('click', function () {
            var tipo = button.getAttribute('data-registro-tipo');
            var formularioActivo = document.getElementById(tipo === 'empresa' ? 'formEmpresa' : 'formPersona');

            document.querySelectorAll('.registro-form-panel').forEach(function (panel) {
                panel.classList.toggle('registro-form-visible', panel === formularioActivo);
            });

            document.querySelectorAll('[data-registro-tipo]').forEach(function (option) {
                option.classList.toggle('registro-tipo-activo', option === button);
            });

            formularioActivo.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});
</script>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>