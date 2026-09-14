<?php
$require_admin = true;
$page_title = 'Panel de Administración - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Consultar lista de usuarios si PDO está disponible
$usuarios = [];
try {
    $dbPath = __DIR__ . '/../../database/database.php';
    if (file_exists($dbPath)) {
        require_once($dbPath);
        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT u.ID_Usuario, u.Email, u.Nombre, u.Telefono, u.Tipo_Persona, u.ID_Rol, r.TipoRol 
                                 FROM usuario u 
                                 LEFT JOIN rol r ON u.ID_Rol = r.ID_Rol 
                                 ORDER BY u.ID_Usuario ASC");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (Exception $e) {
    $usuarios = [];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 2.2rem; margin-bottom: 0.2rem;">Panel de Administración</h1>
        <p style="color: var(--text-muted);">Gestión de cuentas de usuario, roles y permisos de acceso (CRUD).</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="btn btn-primary" onclick="abrirModalCrear()">+ Crear Nuevo Usuario</button>
        <a href="<?php echo VIEWS_PATH; ?>/dashboard.php" class="btn btn-outline">Ir a mi Panel</a>
        <a href="<?php echo PUBLIC_PATH; ?>/logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</div>

<!-- NAVEGACIÓN SUB-PESTAÑAS DE ADMINISTRACIÓN -->
<div style="background: var(--card-bg); border: 1px solid var(--card-border); padding: 8px; border-radius: 12px; display: flex; gap: 8px; margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
    <a href="adminpanel.php" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; background: var(--primary-green); color: white; box-shadow: var(--shadow-sm);">
        👥 Gestión de Usuarios
    </a>
    <a href="adminpanelTienda.php" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; color: var(--text-muted); transition: 0.3s;">
        🛍️ Gestión de Productos (Tienda)
    </a>
</div>

<!-- ALERTAS DE NOTIFICACIÓN -->
<?php if (isset($_GET['msg'])): ?>
    <?php 
        $msgTexto = 'Operación realizada con éxito.';
        if ($_GET['msg'] === 'usuario_creado') $msgTexto = '¡Usuario creado correctamente!';
        if ($_GET['msg'] === 'usuario_actualizado') $msgTexto = '¡Datos de usuario actualizados correctamente!';
        if ($_GET['msg'] === 'usuario_eliminado') $msgTexto = '¡Usuario eliminado con éxito!';
    ?>
    <div class="alert" style="background: rgba(13, 125, 83, 0.12); color: var(--primary-green); border: 1.5px solid rgba(13, 125, 83, 0.3); text-align: center; margin-bottom: 2rem;">
        <?php echo $msgTexto; ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
    <?php 
        $errTexto = 'Ocurrió un error al procesar la solicitud.';
        if ($_GET['error'] === 'campos_vacios') $errTexto = 'Por favor, completa los campos requeridos.';
        if ($_GET['error'] === 'auto_eliminacion') $errTexto = 'No puedes eliminar tu propia cuenta mientras estás logueado.';
        if ($_GET['error'] === 'error_creacion') $errTexto = 'Error al crear el usuario. Verifique si el email ya existe.';
        if ($_GET['error'] === 'error_actualizacion') $errTexto = 'Error al actualizar el usuario.';
        if ($_GET['error'] === 'error_eliminacion') $errTexto = 'Error al eliminar el usuario.';
    ?>
    <div class="alert" style="background: rgba(220, 53, 69, 0.12); color: #d32f2f; border: 1.5px solid rgba(220, 53, 69, 0.3); text-align: center; margin-bottom: 2rem;">
        <?php echo $errTexto; ?>
    </div>
<?php endif; ?>

<!-- TARJETAS ESTADÍSTICAS DEL SISTEMA -->
<div class="features-grid" style="margin-top: 0; margin-bottom: 2.5rem;">
    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Total Registrados</span>
        <h2 style="font-size: 2.2rem; color: var(--primary-green); margin-top: 0.3rem;">
            <?php echo count($usuarios); ?>
        </h2>
    </div>

    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Nivel de Acceso</span>
        <h2 style="font-size: 1.5rem; color: var(--primary-green); margin-top: 0.5rem;">
            <?php echo ($_SESSION['usuario_rol'] == 3) ? 'Super Administrador' : 'Administrador'; ?>
        </h2>
    </div>

    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Usuario Activo</span>
        <p style="font-size: 1.05rem; font-weight: 600; color: var(--text-heading); margin-top: 0.5rem; word-break: break-all;">
            <?php echo htmlspecialchars($_SESSION['email']); ?>
        </p>
    </div>
</div>

<!-- TABLA DE USUARIOS DEL SISTEMA -->
<div class="feature-card" style="background: var(--card-bg); padding: 1.8rem; overflow-x: auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
        <h2 style="font-size: 1.4rem; color: var(--primary-green); margin: 0;">Usuarios Registrados</h2>
    </div>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
        <thead>
            <tr style="border-bottom: 2px solid var(--card-border); color: var(--text-heading);">
                <th style="padding: 12px 10px;">ID</th>
                <th style="padding: 12px 10px;">Nombre</th>
                <th style="padding: 12px 10px;">Email</th>
                <th style="padding: 12px 10px;">Teléfono</th>
                <th style="padding: 12px 10px;">Tipo Persona</th>
                <th style="padding: 12px 10px;">Rol</th>
                <th style="padding: 12px 10px; text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usr): ?>
                    <tr style="border-bottom: 1px solid rgba(13, 125, 83, 0.1);">
                        <td style="padding: 12px 10px; font-weight: bold; color: var(--primary-green);">#<?php echo $usr['ID_Usuario']; ?></td>
                        <td style="padding: 12px 10px; font-weight: 600;"><?php echo htmlspecialchars($usr['Nombre']); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($usr['Email']); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($usr['Telefono'] ?? '-'); ?></td>
                        <td style="padding: 12px 10px;">
                            <span style="background: var(--light-green-tint); color: var(--primary-green); padding: 4px 10px; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">
                                <?php echo ucfirst($usr['Tipo_Persona']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px 10px; font-weight: 600;">
                            <?php 
                                if ($usr['ID_Rol'] == 3) echo '<span style="color: #d32f2f;">Super Admin</span>';
                                elseif ($usr['ID_Rol'] == 2) echo '<span style="color: var(--primary-green);">Admin</span>';
                                else echo 'Usuario';
                            ?>
                        </td>
                        <td style="padding: 12px 10px; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button type="button" class="btn btn-outline" style="padding: 5px 12px; font-size: 0.82rem;"
                                    onclick='abrirModalEditar(<?php echo json_encode($usr, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    Editar
                                </button>

                                <?php if ((int)$usr['ID_Usuario'] !== (int)$_SESSION['user_id']): ?>
                                    <a href="../controllers/admin_controller.php?action=delete_user&id=<?php echo $usr['ID_Usuario']; ?>" 
                                       class="btn btn-danger" style="padding: 5px 12px; font-size: 0.82rem;"
                                       onclick="return confirm('¿Estás seguro de eliminar este usuario (#<?php echo $usr['ID_Usuario']; ?>)?');">
                                        Eliminar
                                    </a>
                                <?php else: ?>
                                    <span style="font-size: 0.78rem; color: var(--text-muted); padding: 5px;">(Tú)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="padding: 20px; text-align: center; color: var(--text-muted);">No se encontraron usuarios en la base de datos o ejecuta <a href="../database/instalar_bd.php">instalar_bd.php</a>.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- MODAL 1: CREAR NUEVO USUARIO -->
<!-- ========================================== -->
<div id="modalCrear" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); border: 1px solid var(--card-border); padding: 2.2rem; border-radius: 16px; width: 90%; max-width: 520px; box-shadow: var(--shadow-lg); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary-green); font-size: 1.5rem; margin: 0;">Crear Nuevo Usuario</h2>
            <button type="button" onclick="cerrarModalCrear()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="../controllers/admin_controller.php" method="POST" style="margin: 0; padding: 0; background: transparent; box-shadow: none; border: none;">
            <input type="hidden" name="action" value="create_user">

            <label for="c_nombre">Nombre Completo:</label>
            <input type="text" id="c_nombre" name="nombre" placeholder="Ej. Ana Gómez" required>

            <label for="c_email">Correo Electrónico:</label>
            <input type="email" id="c_email" name="email" placeholder="ana@ejemplo.com" required>

            <label for="c_telefono">Teléfono:</label>
            <input type="text" id="c_telefono" name="telefono" placeholder="+598 99 123 456">

            <label for="c_password">Contraseña:</label>
            <input type="password" id="c_password" name="password" placeholder="••••••••" required>

            <label for="c_tipo_persona">Tipo de Usuario:</label>
            <select id="c_tipo_persona" name="tipo_persona" required>
                <option value="Persona">Persona (Cliente Individual)</option>
                <option value="Empresa">Empresa (Corporativo)</option>
            </select>

            <label for="c_id_rol">Rol de Acceso / Permisos:</label>
            <select id="c_id_rol" name="id_rol" required>
                <option value="1">Usuario Regular (Rol 1)</option>
                <option value="2">Administrador (Rol 2)</option>
                <option value="3">Super Administrador (Rol 3)</option>
            </select>

            <div style="display: flex; gap: 10px; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalCrear()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: EDITAR USUARIO EXISTENTE -->
<!-- ========================================== -->
<div id="modalEditar" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); border: 1px solid var(--card-border); padding: 2.2rem; border-radius: 16px; width: 90%; max-width: 520px; box-shadow: var(--shadow-lg); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary-green); font-size: 1.5rem; margin: 0;">Editar Usuario <span id="e_id_label"></span></h2>
            <button type="button" onclick="cerrarModalEditar()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="../controllers/admin_controller.php" method="POST" style="margin: 0; padding: 0; background: transparent; box-shadow: none; border: none;">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" id="e_id_usuario" name="id_usuario">

            <label for="e_nombre">Nombre Completo:</label>
            <input type="text" id="e_nombre" name="nombre" required>

            <label for="e_email">Correo Electrónico:</label>
            <input type="email" id="e_email" name="email" required>

            <label for="e_telefono">Teléfono:</label>
            <input type="text" id="e_telefono" name="telefono">

            <label for="e_password">Nueva Contraseña (Opcional):</label>
            <small style="margin-bottom: 0.5rem;">Déjalo en blanco si no deseas cambiar la clave del usuario.</small>
            <input type="password" id="e_password" name="password" placeholder="Ingresa nueva clave solo si deseas cambiarla">

            <label for="e_tipo_persona">Tipo de Usuario:</label>
            <select id="e_tipo_persona" name="tipo_persona" required>
                <option value="Persona">Persona (Cliente Individual)</option>
                <option value="Empresa">Empresa (Corporativo)</option>
            </select>

            <label for="e_id_rol">Rol de Acceso / Permisos:</label>
            <select id="e_id_rol" name="id_rol" required>
                <option value="1">Usuario Regular (Rol 1)</option>
                <option value="2">Administrador (Rol 2)</option>
                <option value="3">Super Administrador (Rol 3)</option>
            </select>

            <div style="display: flex; gap: 10px; margin-top: 1rem;">
                <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalEditar()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- LÓGICA DE INTERACCIÓN JAVASCRIPT -->
<script>
function abrirModalCrear() {
    document.getElementById('modalCrear').style.display = 'flex';
}
function cerrarModalCrear() {
    document.getElementById('modalCrear').style.display = 'none';
}

function abrirModalEditar(user) {
    document.getElementById('e_id_usuario').value = user.ID_Usuario;
    document.getElementById('e_id_label').textContent = '#' + user.ID_Usuario;
    document.getElementById('e_nombre').value = user.Nombre || '';
    document.getElementById('e_email').value = user.Email || '';
    document.getElementById('e_telefono').value = user.Telefono || '';
    document.getElementById('e_password').value = '';
    document.getElementById('e_tipo_persona').value = user.Tipo_Persona || 'Persona';
    document.getElementById('e_id_rol').value = user.ID_Rol || 1;

    document.getElementById('modalEditar').style.display = 'flex';
}
function cerrarModalEditar() {
    document.getElementById('modalEditar').style.display = 'none';
}

// Cerrar modales al hacer clic fuera del contenido
window.onclick = function(event) {
    const modalC = document.getElementById('modalCrear');
    const modalE = document.getElementById('modalEditar');
    if (event.target === modalC) cerrarModalCrear();
    if (event.target === modalE) cerrarModalEditar();
}
</script>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
