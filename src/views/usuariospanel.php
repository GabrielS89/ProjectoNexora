<?php

$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');

$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == 1;
if (!$is_partial) {
    require_once(INCLUDES_DIR . '/header.php');
}

// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
require_once(__DIR__ . '/../../database/database.php');

// Variables para edición y feedback
$id_editar = '';
$modo_edicion = false;
$email_editar = '';
$nombre_editar = '';
$telefono_editar = '';
$rol_editar = '1';
$tipo_persona_editar = 'Persona';
$password_editar = '';
$error_message = '';
$success_message = '';

// Campos Empresa
$nombre_empresa_editar = '';
$rut_editar = '';
$calle_editar = '';
$esquina_editar = '';
$numpuerta_editar = '';
$descripcion_editar = '';
$rep_nombre_editar = '';
$rep_cargo_editar = '';
$rep_email_editar = '';
$rep_telefono_editar = '';

// ── PROCESAR ACCIONES GET (Borrar / Cargar para Editar) ──────────────────────
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? '';
    
    // ELIMINAR USUARIO EN CASCADA
    if ($_GET['action'] === 'delete' && !empty($id)) {
        if (isset($_SESSION['user_id']) && (int)$id === (int)$_SESSION['user_id']) {
            $error_message = 'No puedes eliminar tu propia cuenta de usuario en sesión activa.';
        } else {
            try {
                $pdo->beginTransaction();

                // 1. Eliminar persona
                $stmt = $pdo->prepare("DELETE FROM persona WHERE ID_Usuario = ?");
                $stmt->execute([$id]);

                // 2. Obtener representantes de empresas asociadas y eliminarlos
                $stmtRep = $pdo->prepare("SELECT ID_Representante FROM empresa WHERE ID_Usuario = ?");
                $stmtRep->execute([$id]);
                $repIds = $stmtRep->fetchAll(PDO::FETCH_COLUMN);

                $stmt = $pdo->prepare("DELETE FROM empresa WHERE ID_Usuario = ?");
                $stmt->execute([$id]);

                if (!empty($repIds)) {
                    $cleanRepIds = array_filter($repIds);
                    if (!empty($cleanRepIds)) {
                        $inQuery = implode(',', array_fill(0, count($cleanRepIds), '?'));
                        $stmt = $pdo->prepare("DELETE FROM representante WHERE ID_Representante IN ($inQuery)");
                        $stmt->execute(array_values($cleanRepIds));
                    }
                }

                // 3. Eliminar usuario_plan
                $stmt = $pdo->prepare("DELETE FROM usuario_plan WHERE ID_Usuario = ?");
                $stmt->execute([$id]);

                // 4. Eliminar solicitudes de cotización
                $stmt = $pdo->prepare("DELETE FROM solicitud_cotizacion WHERE ID_Usuario = ?");
                $stmt->execute([$id]);

                // 5. Eliminar cotizaciones
                $stmt = $pdo->prepare("DELETE FROM cotizacion WHERE ID_Usuario = ?");
                $stmt->execute([$id]);

                // 6. Eliminar pedidos y detalles si existen
                $stmtPed = $pdo->prepare("SELECT ID_Pedido FROM pedido WHERE ID_Usuario = ?");
                $stmtPed->execute([$id]);
                $pedIds = $stmtPed->fetchAll(PDO::FETCH_COLUMN);
                if (!empty($pedIds)) {
                    $inPed = implode(',', array_fill(0, count($pedIds), '?'));
                    $stmt = $pdo->prepare("DELETE FROM detalle_pedido WHERE ID_Pedido IN ($inPed)");
                    $stmt->execute($pedIds);
                    $stmt = $pdo->prepare("DELETE FROM pedido WHERE ID_Usuario = ?");
                    $stmt->execute([$id]);
                }

                // 7. Eliminar usuario principal
                $stmt = $pdo->prepare("DELETE FROM usuario WHERE ID_Usuario = ?");
                $stmt->execute([$id]);

                $pdo->commit();
                $success_message = "Usuario #$id eliminado correctamente del sistema.";
            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error_message = 'Error al eliminar usuario: ' . $e->getMessage();
            }
        }
    }

    // CARGAR DATOS PARA EDICIÓN
    if ($_GET['action'] === 'edit' && !empty($id)) {
        $stmt = $pdo->prepare("SELECT * FROM usuario WHERE ID_Usuario = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if ($user) {
            $id_editar = $user['ID_Usuario'];
            $email_editar = $user['Email'];
            $nombre_editar = $user['Nombre'];
            $telefono_editar = $user['Telefono'];
            $rol_editar = $user['ID_Rol'];
            $tipo_persona_editar = $user['Tipo_Persona'] ?? 'Persona';
            $modo_edicion = true;

            if ($tipo_persona_editar === 'Empresa') {
                $stmtEmpresa = $pdo->prepare("
                    SELECT e.*, r.Nombre_Completo AS Rep_Nombre, r.Cargo AS Rep_Cargo, 
                           r.Email_Contacto AS Rep_Email, r.Telefono_Contacto AS Rep_Telefono 
                    FROM empresa e 
                    LEFT JOIN representante r ON r.ID_Representante = e.ID_Representante 
                    WHERE e.ID_Usuario = ?
                ");
                $stmtEmpresa->execute([$id]);
                $empresaData = $stmtEmpresa->fetch();

                if ($empresaData) {
                    $nombre_empresa_editar = $empresaData['Nombre_Empresa'] ?? '';
                    $rut_editar = $empresaData['RUT'] ?? '';
                    $calle_editar = $empresaData['Calle'] ?? '';
                    $esquina_editar = $empresaData['Esquina'] ?? '';
                    $numpuerta_editar = $empresaData['Numero_Puerta'] ?? '';
                    $descripcion_editar = $empresaData['Descripcion'] ?? '';
                    $rep_nombre_editar = $empresaData['Rep_Nombre'] ?? '';
                    $rep_cargo_editar = $empresaData['Rep_Cargo'] ?? '';
                    $rep_email_editar = $empresaData['Rep_Email'] ?? '';
                    $rep_telefono_editar = $empresaData['Rep_Telefono'] ?? '';
                }
            }
        }
    }
}

// ── PROCESAR FORMULARIO POST (Crear / Actualizar) ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $rol = $_POST['id_rol'] ?? '1';
    $tipo_persona = $_POST['tipo_persona'] ?? 'Persona';
    $password = $_POST['password'] ?? '';

    // Campos empresa
    $nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
    $rut = trim($_POST['rut'] ?? '');
    $calle = trim($_POST['calle'] ?? '');
    $esquina = trim($_POST['esquina'] ?? '');
    $numpuerta = trim($_POST['numpuerta'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $rep_nombre = trim($_POST['rep_nombre'] ?? '');
    $rep_cargo = trim($_POST['rep_cargo'] ?? '');
    $rep_email = trim($_POST['rep_email'] ?? '');
    $rep_telefono = trim($_POST['rep_telefono'] ?? '');

    if (empty($email) || empty($nombre)) {
        $error_message = 'El correo electrónico y el nombre son campos obligatorios.';
    } elseif (empty($id) && empty($password)) {
        $error_message = 'La contraseña es obligatoria al registrar un nuevo usuario.';
    } else {
        try {
            $pdo->beginTransaction();

            if (!empty($id)) {
                // ACTUALIZAR USUARIO EXISTENTE
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuario SET Email=?, Nombre=?, Telefono=?, Tipo_Persona=?, ID_Rol=?, Contrasena=? WHERE ID_Usuario=?");
                    $stmt->execute([$email, $nombre, $telefono, $tipo_persona, $rol, $hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuario SET Email=?, Nombre=?, Telefono=?, Tipo_Persona=?, ID_Rol=? WHERE ID_Usuario=?");
                    $stmt->execute([$email, $nombre, $telefono, $tipo_persona, $rol, $id]);
                }

                if ($tipo_persona === 'Empresa') {
                    // Limpiar registro en persona
                    $stmt = $pdo->prepare("DELETE FROM persona WHERE ID_Usuario=?");
                    $stmt->execute([$id]);

                    // Actualizar o crear representante
                    $stmtRep = $pdo->prepare("SELECT ID_Representante FROM empresa WHERE ID_Usuario=?");
                    $stmtRep->execute([$id]);
                    $rep = $stmtRep->fetch();
                    
                    if ($rep && !empty($rep['ID_Representante'])) {
                        $stmt = $pdo->prepare("UPDATE representante SET Nombre_Completo=?, Cargo=?, Email_Contacto=?, Telefono_Contacto=? WHERE ID_Representante=?");
                        $stmt->execute([$rep_nombre, $rep_cargo, $rep_email, $rep_telefono, $rep['ID_Representante']]);
                        $representante_id = $rep['ID_Representante'];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO representante (Nombre_Completo, Cargo, Email_Contacto, Telefono_Contacto) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$rep_nombre, $rep_cargo, $rep_email, $rep_telefono]);
                        $representante_id = $pdo->lastInsertId();
                    }

                    // Actualizar o crear empresa
                    $stmtEmp = $pdo->prepare("SELECT ID_Empresa FROM empresa WHERE ID_Usuario=?");
                    $stmtEmp->execute([$id]);
                    $empresa = $stmtEmp->fetch();
                    if ($empresa) {
                        $stmt = $pdo->prepare("UPDATE empresa SET RUT=?, Nombre_Empresa=?, Calle=?, Esquina=?, Numero_Puerta=?, Descripcion=?, Categoria_Empresarial=?, Tamano='Mediana', ID_Representante=? WHERE ID_Empresa=?");
                        $stmt->execute([$rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $descripcion, $representante_id, $empresa['ID_Empresa']]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO empresa (ID_Usuario, RUT, Nombre_Empresa, Calle, Esquina, Numero_Puerta, Descripcion, Categoria_Empresarial, Tamano, ID_Representante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Mediana', ?)");
                        $stmt->execute([$id, $rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $descripcion, $representante_id]);
                    }
                } else {
                    // Limpiar empresa y representante
                    $stmtGetRep = $pdo->prepare("SELECT ID_Representante FROM empresa WHERE ID_Usuario=?");
                    $stmtGetRep->execute([$id]);
                    $repToDelete = $stmtGetRep->fetch();
                    
                    $stmt = $pdo->prepare("DELETE FROM empresa WHERE ID_Usuario=?");
                    $stmt->execute([$id]);
                    
                    if ($repToDelete && !empty($repToDelete['ID_Representante'])) {
                        $stmt = $pdo->prepare("DELETE FROM representante WHERE ID_Representante=?");
                        $stmt->execute([$repToDelete['ID_Representante']]);
                    }

                    $stmt = $pdo->prepare("SELECT ID_Usuario FROM persona WHERE ID_Usuario=?");
                    $stmt->execute([$id]);
                    if (!$stmt->fetch()) {
                        $stmt = $pdo->prepare("INSERT INTO persona (ID_Usuario) VALUES (?)");
                        $stmt->execute([$id]);
                    }
                }

                $pdo->commit();
                $success_message = "Usuario #$id ($nombre) actualizado correctamente.";
                
                // Reiniciar modo edición tras guardar
                $id_editar = '';
                $modo_edicion = false;
                $email_editar = '';
                $nombre_editar = '';
                $telefono_editar = '';
                $rol_editar = '1';
                $tipo_persona_editar = 'Persona';
            } else {
                // CREAR NUEVO USUARIO
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO usuario (Email, Contrasena, Nombre, Telefono, Tipo_Persona, ID_Rol) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$email, $hash, $nombre, $telefono, $tipo_persona, $rol]);
                $newUserId = $pdo->lastInsertId();

                if ($tipo_persona === 'Empresa') {
                    $stmt = $pdo->prepare("INSERT INTO representante (Nombre_Completo, Cargo, Email_Contacto, Telefono_Contacto) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$rep_nombre, $rep_cargo, $rep_email, $rep_telefono]);
                    $representante_id = $pdo->lastInsertId();

                    $stmt = $pdo->prepare("INSERT INTO empresa (ID_Usuario, RUT, Nombre_Empresa, Calle, Esquina, Numero_Puerta, Descripcion, Categoria_Empresarial, Tamano, ID_Representante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Mediana', ?)");
                    $stmt->execute([$newUserId, $rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $descripcion, $representante_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO persona (ID_Usuario) VALUES (?)");
                    $stmt->execute([$newUserId]);
                }

                $pdo->commit();
                $success_message = "Nuevo usuario #$newUserId ($nombre) registrado exitosamente.";
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error_message = 'Error en la base de datos: ' . $e->getMessage();
        }
    }

    if (!$is_partial) {
        header('Location: usuariospanel.php');
        exit;
    }
}

// Roles y listado actualizado de usuarios
$stmtRoles = $pdo->query("SELECT ID_Rol, TipoRol FROM rol ORDER BY ID_Rol ASC");
$roles = $stmtRoles->fetchAll();
$stmt = $pdo->query("SELECT u.ID_Usuario, u.Email, u.Nombre, u.Telefono, u.Tipo_Persona, u.ID_Rol, r.TipoRol FROM usuario u LEFT JOIN rol r ON u.ID_Rol = r.ID_Rol ORDER BY u.ID_Usuario DESC");
$usuarios = $stmt->fetchAll();
?>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--card-border); padding-bottom: 0.8rem;">
        <h2 style="margin: 0; font-size: 1.3rem;"><?php echo $modo_edicion ? "✏ Editar Usuario / Empresa" : "➕ Registrar Nuevo Usuario"; ?></h2>
        <?php if ($modo_edicion): ?>
            <a href="usuariospanel.php?partial=1" class="partial-link btn btn-outline btn-sm">Cancelar Edición</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert-box alert-success">
            <span class="alert-tag">ÉXITO</span>
            <div><?php echo htmlspecialchars($success_message); ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert-box alert-danger">
            <span class="alert-tag">ERROR</span>
            <div><?php echo htmlspecialchars($error_message); ?></div>
        </div>
    <?php endif; ?>

    <form action="usuariospanel.php?partial=1" method="POST" id="usuarioForm" style="margin-bottom: 2.5rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 10px; border: 1px solid var(--card-border);">
        <input type="hidden" name="id" value="<?php echo $id_editar; ?>">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <div>
                <label>Correo Electrónico: <span style="color:#ef4444;">*</span></label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email_editar); ?>" placeholder="usuario@email.com" required>
            </div>
            <div>
                <label>Nombre Completo / Razón: <span style="color:#ef4444;">*</span></label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre_editar); ?>" placeholder="Juan Pérez o Empresa S.A." required>
            </div>
            <div>
                <label>Teléfono de Contacto: <span style="color:#ef4444;">*</span></label>
                <input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono_editar); ?>" placeholder="099 123 456" required>
            </div>
            <div>
                <label>Tipo de Cuenta: <span style="color:#ef4444;">*</span></label>
                <select name="tipo_persona" id="tipo_persona" onchange="mostrarCamposTipo()" required>
                    <option value="Persona" <?php echo ($tipo_persona_editar === 'Persona') ? 'selected' : ''; ?>>Persona Particular</option>
                    <option value="Empresa" <?php echo ($tipo_persona_editar === 'Empresa') ? 'selected' : ''; ?>>Empresa Corporativa</option>
                </select>
            </div>
            <div>
                <label>Rol en el Sistema: <span style="color:#ef4444;">*</span></label>
                <select name="id_rol" required>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['ID_Rol']; ?>" <?= ($rol['ID_Rol'] == $rol_editar) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($rol['TipoRol']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label>Contraseña: <?php echo $modo_edicion ? '(Opcional)' : '<span style="color:#ef4444;">*</span>'; ?></label>
                <input type="password" name="password" value="" placeholder="<?php echo $modo_edicion ? 'Dejar en blanco para no cambiar' : 'Nueva contraseña'; ?>">
            </div>
        </div>

        <!-- Campos específicos para Empresa -->
        <div id="empresaFields" style="display: <?php echo ($tipo_persona_editar === 'Empresa') ? 'block' : 'none'; ?>; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px dashed var(--card-border);">
            <h4 style="color: var(--primary-green-hover); margin-bottom: 1rem;">🏢 Datos Fiscales y Domicilio de la Empresa</h4>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label>Nombre Comercial / Fantasía:</label>
                    <input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($nombre_empresa_editar); ?>">
                </div>
                <div>
                    <label>RUT / Identificación Fiscal:</label>
                    <input type="text" name="rut" value="<?php echo htmlspecialchars($rut_editar); ?>">
                </div>
                <div>
                    <label>Calle:</label>
                    <input type="text" name="calle" value="<?php echo htmlspecialchars($calle_editar); ?>">
                </div>
                <div>
                    <label>Esquina:</label>
                    <input type="text" name="esquina" value="<?php echo htmlspecialchars($esquina_editar); ?>">
                </div>
                <div>
                    <label>Número de Puerta:</label>
                    <input type="text" name="numpuerta" value="<?php echo htmlspecialchars($numpuerta_editar); ?>">
                </div>
            </div>

            <div style="margin-top: 0.8rem;">
                <label>Descripción de la Actividad Empresarial:</label>
                <textarea name="descripcion" rows="2"><?php echo htmlspecialchars($descripcion_editar); ?></textarea>
            </div>

            <h4 style="color: var(--primary-green-hover); margin-top: 1.2rem; margin-bottom: 0.8rem;">👤 Datos del Representante Legal</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                <div>
                    <label>Nombre Representante:</label>
                    <input type="text" name="rep_nombre" value="<?php echo htmlspecialchars($rep_nombre_editar); ?>">
                </div>
                <div>
                    <label>Cargo del Representante:</label>
                    <input type="text" name="rep_cargo" value="<?php echo htmlspecialchars($rep_cargo_editar); ?>">
                </div>
                <div>
                    <label>Email de Contacto:</label>
                    <input type="email" name="rep_email" value="<?php echo htmlspecialchars($rep_email_editar); ?>">
                </div>
                <div>
                    <label>Teléfono Directo:</label>
                    <input type="text" name="rep_telefono" value="<?php echo htmlspecialchars($rep_telefono_editar); ?>">
                </div>
            </div>
        </div>

        <div style="margin-top: 1.5rem; display: flex; gap: 0.8rem;">
            <button type="submit" class="btn btn-primary"><?php echo $modo_edicion ? "Actualizar Usuario" : "Guardar Usuario"; ?></button>
            <?php if ($modo_edicion): ?>
                <a href="usuariospanel.php?partial=1" class="partial-link btn btn-outline">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>

    <div style="margin-top: 2rem;">
        <h2 style="font-size: 1.3rem; margin-bottom: 1rem;">👥 Usuarios Registrados en el Sistema</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Tipo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $user): ?>
                            <tr>
                                <td><strong>#<?= $user['ID_Usuario']; ?></strong></td>
                                <td><?= htmlspecialchars($user['Email']); ?></td>
                                <td><?= htmlspecialchars($user['Nombre']); ?></td>
                                <td><?= htmlspecialchars($user['Telefono'] ?: 'N/A'); ?></td>
                                <td>
                                    <span style="font-size: 0.8rem; font-weight: 600; color: <?= ($user['Tipo_Persona'] === 'Empresa') ? '#38bdf8' : 'var(--text-muted)'; ?>;">
                                        <?= ($user['Tipo_Persona'] === 'Empresa') ? '🏢 Empresa' : '👤 Persona'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($user['TipoRol'] === 'Super_Admin' || $user['TipoRol'] === 'Admin'): ?>
                                        <span class="admin-badge-role" style="font-size: 0.72rem; padding: 2px 8px;"><?= htmlspecialchars($user['TipoRol']); ?></span>
                                    <?php else: ?>
                                        <span style="font-size: 0.82rem; color: var(--text-muted);"><?= htmlspecialchars($user['TipoRol'] ?? 'Usuario'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="partial-link" href="usuariospanel.php?action=edit&id=<?= $user['ID_Usuario']; ?>&partial=1">✏ Editar</a>
                                    <a class="partial-link" href="usuariospanel.php?action=delete&id=<?= $user['ID_Usuario']; ?>&partial=1" data-confirm="¿Seguro que deseas eliminar este usuario?">🗑 Borrar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No hay usuarios registrados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function mostrarCamposTipo() {
            const select = document.getElementById('tipo_persona');
            if (!select) return;
            const empresaFields = document.getElementById('empresaFields');
            if (!empresaFields) return;

            if (select.value === 'Empresa') {
                empresaFields.style.display = 'block';
            } else {
                empresaFields.style.display = 'none';
            }
        }
        mostrarCamposTipo();
    </script>

<?php if (!$is_partial): ?>
<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
<?php endif; ?>