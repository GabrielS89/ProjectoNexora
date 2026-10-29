<?php

$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');

$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == 1;
if (!$is_partial) {
    require_once(INCLUDES_DIR . '/header.php');
}

$on_adminpanel = (strpos($_SERVER['REQUEST_URI'] ?? '', '/adminpanel.php') !== false) || (basename($_SERVER['PHP_SELF'] ?? '') === 'adminpanel.php');
$usuario_base_url = $on_adminpanel ? 'adminpanel.php?page=usuarios' : 'usuariospanel.php';
$usuario_form_action = $usuario_base_url . ($is_partial ? '&partial=1' : '?partial=1');

// Definir si el usuario tiene permisos de administración completa
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
// Si llega el rol 4, solo puede usar el panel de instalación; no el CRUD de usuarios.
if (isset($_SESSION['usuario_rol']) && (int)$_SESSION['usuario_rol'] === 4) {
    $es_admin = false;
}

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
    $categoria      = trim($_POST['categoria'] ?? '');
    $tamano         = trim($_POST['tamano'] ?? '');
    $rep_nombre     = trim($_POST['rep_nombre'] ?? '');
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
                        $stmt = $pdo->prepare("UPDATE empresa SET RUT=?, Nombre_Empresa=?, Calle=?, Esquina=?, Numero_Puerta=?, Descripcion=?, Categoria_Empresarial=?, Tamano=?, ID_Representante=? WHERE ID_Empresa=?");
                        $stmt->execute([$rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $categoria, $tamano, $representante_id, $empresa['ID_Empresa']]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO empresa (ID_Usuario, RUT, Nombre_Empresa, Calle, Esquina, Numero_Puerta, Descripcion, Categoria_Empresarial, Tamano, ID_Representante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$id, $rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $categoria, $tamano, $representante_id]);
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

                    $stmt = $pdo->prepare("INSERT INTO empresa (ID_Usuario, RUT, Nombre_Empresa, Calle, Esquina, Numero_Puerta, Descripcion, Categoria_Empresarial, Tamano, ID_Representante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
        header('Location: ' . $usuario_base_url);
        exit;
    }
}

// Roles y listado actualizado de usuarios
$stmtRoles = $pdo->query("SELECT ID_Rol, TipoRol FROM rol ORDER BY ID_Rol ASC");
$roles = $stmtRoles->fetchAll();
$stmt = $pdo->query("SELECT u.ID_Usuario, u.Email, u.Nombre, u.Telefono, u.Tipo_Persona, u.ID_Rol, r.TipoRol FROM usuario u LEFT JOIN rol r ON u.ID_Rol = r.ID_Rol ORDER BY u.ID_Usuario DESC");
$usuarios = $stmt->fetchAll();
?>

    <div class="encabezado-admin">
        <button type="button" class="btn btn-primary" onclick="abrirModalCrear()">Crear Nuevo Usuario</button>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alerta alerta--exito"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alerta alerta--error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <div class="tarjeta-estadistica tarjeta--con-padding">
        <div class="tarjeta-cabecera">
            <h2 class="tarjeta-titulo">Usuarios Registrados</h2>
        </div>

        <table class="tabla">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Tipo Persona</th>
                    <th>Rol</th>
                    <th class="texto-centro">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usr): ?>
                        <tr>
                            <td class="celda-id">#<?php echo $usr['ID_Usuario']; ?></td>
                            <td class="celda-fuerte"><?php echo htmlspecialchars($usr['Nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usr['Email']); ?></td>
                            <td><?php echo htmlspecialchars($usr['Telefono'] ?? '-'); ?></td>
                            <td>
                                <span class="distintivo-tipo"><?php echo ucfirst($usr['Tipo_Persona']); ?></span>
                            </td>
                            <td class="celda-fuerte">
                                <?php 
                                    if ($usr['ID_Rol'] == 3) echo '<span class="rol-superadmin">Super Admin</span>';
                                    elseif ($usr['ID_Rol'] == 2) echo '<span class="rol-admin">Admin</span>';
                                    elseif ($usr['ID_Rol'] == 4) echo '<span class="rol-admin">Instalación</span>';
                                    else echo 'Usuario';
                                ?>
                            </td>
                            <td class="texto-centro">
                                <div class="grupo-acciones">
                                    <button type="button" class="btn btn-outline btn--pequeno"
                                        onclick='abrirModalEditar(<?php echo json_encode($usr, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                        Editar
                                    </button>

                                    <?php if ((int)$usr['ID_Usuario'] !== (int)$_SESSION['user_id']): ?>
                                        <a href="../controllers/admin_controller.php?action=delete_user&id=<?php echo $usr['ID_Usuario']; ?>" 
                                            class="btn btn-danger btn--pequeno"
                                            onclick="return confirm('¿Estás seguro de eliminar este usuario (#<?php echo $usr['ID_Usuario']; ?>)?');">
                                            Eliminar
                                        </a>
                                    <?php else: ?>
                                        <span class="etiqueta-yo">(Tú)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="fila-vacia">No se encontraron usuarios en la base de datos.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="modalCrear" class="modal">
        <div class="modal-content modal-content--wide">
            <div class="modal-header">
                <h2 class="modal-title">Crear Nuevo Usuario</h2>
                <button type="button" class="modal-close" onclick="cerrarModalCrear()">&times;</button>
            </div>

            <form action="../controllers/admin_controller.php" method="POST" class="modal-form">
                <input type="hidden" name="action" value="create_user">

                <div class="form-grid">
                    <div class="form-field">
                        <label for="c_nombre">Nombre Completo:</label>
                        <input type="text" id="c_nombre" name="nombre" placeholder="Ej. Ana Gómez" required>
                    </div>

                    <div class="form-field">
                        <label for="c_email">Correo Electrónico:</label>
                        <input type="email" id="c_email" name="email" placeholder="ana@ejemplo.com" required>
                    </div>

                    <div class="form-field">
                        <label for="c_telefono">Teléfono:</label>
                        <input type="text" id="c_telefono" name="telefono" placeholder="+598 99 123 456">
                    </div>

                    <div class="form-field">
                        <label for="c_password">Contraseña:</label>
                        <input type="password" id="c_password" name="password" placeholder="••••••••" required>
                    </div>

                    <div class="form-field form-field--full">
                        <label for="c_tipo_persona">Tipo de Usuario:</label>
                        <select id="c_tipo_persona" name="tipo_persona" required onchange="toggleEmpresaPanel('c')">
                            <option value="Persona">Persona (Cliente Individual)</option>
                            <option value="Empresa">Empresa (Corporativo)</option>
                        </select>
                    </div>

                    <div class="form-field form-field--full">
                        <label for="c_id_rol">Rol de Acceso / Permisos:</label>
                        <select id="c_id_rol" name="id_rol" required>
                            <option value="1">Usuario Regular (Rol 1)</option>
                            <option value="2">Administrador (Rol 2)</option>
                            <option value="3">Super Administrador (Rol 3)</option>
                            <option value="4">Instalación (Rol 4)</option>
                        </select>
                    </div>
                </div>

                <div class="empresa-panel" id="c_empresa_panel" data-modal="c" hidden>
                    <div class="empresa-panel__header">
                        <span>Datos de la empresa</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="c_nombre_empresa">Nombre de la empresa:</label>
                            <input type="text" id="c_nombre_empresa" name="nombre_empresa" placeholder="Ej. Nexora S.A.">
                        </div>

                        <div class="form-field">
                            <label for="c_rut">RUT:</label>
                            <input type="text" id="c_rut" name="rut" placeholder="123456789012">
                        </div>

                        <div class="form-field">
                            <label for="c_calle">Calle:</label>
                            <input type="text" id="c_calle" name="calle" placeholder="Avenida de ejemplo">
                        </div>

                        <div class="form-field">
                            <label for="c_esquina">Esquina:</label>
                            <input type="text" id="c_esquina" name="esquina" placeholder="Av. Brasil">
                        </div>

                        <div class="form-field">
                            <label for="c_numpuerta">Número de puerta:</label>
                            <input type="text" id="c_numpuerta" name="numpuerta" placeholder="1234">
                        </div>

                        <div class="form-field">
                            <label for="c_categoria">Categoría empresarial:</label>
                            <select id="c_categoria" name="categoria">
                                <option value="">-- Selecciona un sector --</option>
                                <option value="tecnologia">Tecnología e Informática</option>
                                <option value="comercio">Comercio y Retail</option>
                                <option value="industria">Industria y Logística</option>
                                <option value="servicios">Servicios Profesionales</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="c_tamano">Tamaño de la empresa:</label>
                            <select id="c_tamano" name="tamano">
                                <option value="pequena">1 a 5 empleados (Pequeña)</option>
                                <option value="mediana">6 a 20 empleados (Mediana)</option>
                                <option value="grande">Más de 20 empleados (Grande)</option>
                            </select>
                        </div>

                        <div class="form-field form-field--full">
                            <label for="c_descripcion">Descripción:</label>
                            <textarea id="c_descripcion" name="descripcion" rows="3" placeholder="Describe la actividad comercial de la empresa..."></textarea>
                        </div>
                    </div>

                    <div class="empresa-panel__header">
                        <span>Representante legal</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="c_rep_nombre">Nombre completo:</label>
                            <input type="text" id="c_rep_nombre" name="rep_nombre" placeholder="Ej. María López">
                        </div>

                        <div class="form-field">
                            <label for="c_rep_cargo">Cargo:</label>
                            <input type="text" id="c_rep_cargo" name="rep_cargo" placeholder="Gerente General">
                        </div>

                        <div class="form-field">
                            <label for="c_rep_email">Email de contacto:</label>
                            <input type="email" id="c_rep_email" name="rep_email" placeholder="representante@empresa.com">
                        </div>

                        <div class="form-field">
                            <label for="c_rep_telefono">Teléfono del representante:</label>
                            <input type="text" id="c_rep_telefono" name="rep_telefono" placeholder="+598 94 000 000">
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    <button type="button" class="btn btn-outline" onclick="cerrarModalCrear()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditar" class="modal">
        <div class="modal-content modal-content--wide">
            <div class="modal-header">
                <h2 class="modal-title">Editar Usuario <span id="e_id_label"></span></h2>
                <button type="button" class="modal-close" onclick="cerrarModalEditar()">&times;</button>
            </div>

            <form action="../controllers/admin_controller.php" method="POST" class="modal-form">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" id="e_id_usuario" name="id_usuario">

                <div class="form-grid">
                    <div class="form-field">
                        <label for="e_nombre">Nombre Completo:</label>
                        <input type="text" id="e_nombre" name="nombre" required>
                    </div>

                    <div class="form-field">
                        <label for="e_email">Correo Electrónico:</label>
                        <input type="email" id="e_email" name="email" required>
                    </div>

                    <div class="form-field">
                        <label for="e_telefono">Teléfono:</label>
                        <input type="text" id="e_telefono" name="telefono">
                    </div>

                    <div class="form-field">
                        <label for="e_password">Nueva Contraseña (Opcional):</label>
                        <input type="password" id="e_password" name="password" placeholder="Ingresa nueva clave solo si deseas cambiarla">
                    </div>

                    <div class="form-field form-field--full">
                        <label for="e_tipo_persona">Tipo de Usuario:</label>
                        <select id="e_tipo_persona" name="tipo_persona" required onchange="toggleEmpresaPanel('e')">
                            <option value="Persona">Persona (Cliente Individual)</option>
                            <option value="Empresa">Empresa (Corporativo)</option>
                        </select>
                    </div>

                    <div class="form-field form-field--full">
                        <label for="e_id_rol">Rol de Acceso / Permisos:</label>
                        <select id="e_id_rol" name="id_rol" required>
                            <option value="1">Usuario Regular (Rol 1)</option>
                            <option value="2">Administrador (Rol 2)</option>
                            <option value="3">Super Administrador (Rol 3)</option>
                            <option value="4">Instalación (Rol 4)</option>
                        </select>
                    </div>
                </div>

                <div class="empresa-panel" id="e_empresa_panel" data-modal="e" hidden>
                    <div class="empresa-panel__header">
                        <span>Datos de la empresa</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="e_nombre_empresa">Nombre de la empresa:</label>
                            <input type="text" id="e_nombre_empresa" name="nombre_empresa" placeholder="Ej. Nexora S.A.">
                        </div>

                        <div class="form-field">
                            <label for="e_rut">RUT:</label>
                            <input type="text" id="e_rut" name="rut" placeholder="123456789012">
                        </div>

                        <div class="form-field">
                            <label for="e_calle">Calle:</label>
                            <input type="text" id="e_calle" name="calle" placeholder="Avenida de ejemplo">
                        </div>

                        <div class="form-field">
                            <label for="e_esquina">Esquina:</label>
                            <input type="text" id="e_esquina" name="esquina" placeholder="Av. Brasil">
                        </div>

                        <div class="form-field">
                            <label for="e_numpuerta">Número de puerta:</label>
                            <input type="text" id="e_numpuerta" name="numpuerta" placeholder="1234">
                        </div>

                        <div class="form-field">
                            <label for="e_categoria">Categoría empresarial:</label>
                            <select id="e_categoria" name="categoria">
                                <option value="">-- Selecciona un sector --</option>
                                <option value="tecnologia">Tecnología e Informática</option>
                                <option value="comercio">Comercio y Retail</option>
                                <option value="industria">Industria y Logística</option>
                                <option value="servicios">Servicios Profesionales</option>
                            </select>
                        </div>

                        <div class="form-field">
                            <label for="e_tamano">Tamaño de la empresa:</label>
                            <select id="e_tamano" name="tamano">
                                <option value="pequena">1 a 5 empleados (Pequeña)</option>
                                <option value="mediana">6 a 20 empleados (Mediana)</option>
                                <option value="grande">Más de 20 empleados (Grande)</option>
                            </select>
                        </div>

                        <div class="form-field form-field--full">
                            <label for="e_descripcion">Descripción:</label>
                            <textarea id="e_descripcion" name="descripcion" rows="3" placeholder="Describe la actividad comercial de la empresa..."></textarea>
                        </div>
                    </div>

                    <div class="empresa-panel__header">
                        <span>Representante legal</span>
                    </div>

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="e_rep_nombre">Nombre completo:</label>
                            <input type="text" id="e_rep_nombre" name="rep_nombre" placeholder="Ej. María López">
                        </div>

                        <div class="form-field">
                            <label for="e_rep_cargo">Cargo:</label>
                            <input type="text" id="e_rep_cargo" name="rep_cargo" placeholder="Gerente General">
                        </div>

                        <div class="form-field">
                            <label for="e_rep_email">Email de contacto:</label>
                            <input type="email" id="e_rep_email" name="rep_email" placeholder="representante@empresa.com">
                        </div>

                        <div class="form-field">
                            <label for="e_rep_telefono">Teléfono del representante:</label>
                            <input type="text" id="e_rep_telefono" name="rep_telefono" placeholder="+598 94 000 000">
                        </div>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
                    <button type="button" class="btn btn-outline" onclick="cerrarModalEditar()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal-content--wide {
            max-width: 880px;
            width: min(92vw, 880px);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(220px, 1fr));
            gap: 16px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-field--full {
            grid-column: 1 / -1;
        }

        .empresa-panel {
            margin-top: 24px;
            border: 1px solid rgba(52, 152, 219, 0.2);
            background: rgba(52, 152, 219, 0.04);
            border-radius: 16px;
            padding: 18px;
        }

        .empresa-panel__header {
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #1d4f91;
            margin-bottom: 16px;
        }

        .modal-form input,
        .modal-form select,
        .modal-form textarea {
            width: 100%;
            box-sizing: border-box;
        }

        @media (max-width: 720px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        function toggleEmpresaPanel(prefix) {
            const select = document.getElementById(prefix + '_tipo_persona');
            const panel = document.getElementById(prefix + '_empresa_panel');
            const isEmpresa = select && select.value === 'Empresa';

            if (panel) {
                panel.hidden = !isEmpresa;
            }

            const fields = panel ? panel.querySelectorAll('input, textarea, select') : [];
            fields.forEach((field) => {
                field.required = isEmpresa;
            });
        }

        function abrirModalCrear() {
            document.getElementById('modalCrear').style.display = 'flex';
            toggleEmpresaPanel('c');
        }
        function cerrarModalCrear() {
            document.getElementById('modalCrear').style.display = 'none';
            document.getElementById('modalCrear').querySelector('form').reset();
            toggleEmpresaPanel('c');
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

            document.getElementById('e_nombre_empresa').value = '';
            document.getElementById('e_rut').value = '';
            document.getElementById('e_calle').value = '';
            document.getElementById('e_esquina').value = '';
            document.getElementById('e_numpuerta').value = '';
            document.getElementById('e_categoria').value = '';
            document.getElementById('e_tamano').value = 'mediana';
            document.getElementById('e_descripcion').value = '';
            document.getElementById('e_rep_nombre').value = '';
            document.getElementById('e_rep_cargo').value = '';
            document.getElementById('e_rep_email').value = '';
            document.getElementById('e_rep_telefono').value = '';

            toggleEmpresaPanel('e');

            if ((user.Tipo_Persona || 'Persona') === 'Empresa') {
                fetch('../controllers/admin_controller.php?action=get_user_company&id=' + encodeURIComponent(user.ID_Usuario))
                    .then(response => response.json())
                    .then(data => {
                        if (!data) return;
                        document.getElementById('e_nombre_empresa').value = data.Nombre_Empresa || '';
                        document.getElementById('e_rut').value = data.RUT || '';
                        document.getElementById('e_calle').value = data.Calle || '';
                        document.getElementById('e_esquina').value = data.Esquina || '';
                        document.getElementById('e_numpuerta').value = data.Numero_Puerta || '';
                        document.getElementById('e_categoria').value = data.Categoria_Empresarial || '';
                        document.getElementById('e_tamano').value = data.Tamano || 'mediana';
                        document.getElementById('e_descripcion').value = data.Descripcion || '';
                        document.getElementById('e_rep_nombre').value = data.Rep_Nombre || '';
                        document.getElementById('e_rep_cargo').value = data.Rep_Cargo || '';
                        document.getElementById('e_rep_email').value = data.Rep_Email || '';
                        document.getElementById('e_rep_telefono').value = data.Rep_Telefono || '';
                    })
                    .catch(() => {});
            }

            document.getElementById('modalEditar').style.display = 'flex';
        }
        function cerrarModalEditar() {
            document.getElementById('modalEditar').style.display = 'none';
            document.getElementById('modalEditar').querySelector('form').reset();
            toggleEmpresaPanel('e');
        }

        window.addEventListener('DOMContentLoaded', function () {
            toggleEmpresaPanel('c');
            toggleEmpresaPanel('e');
        });

        window.onclick = function(event) {
            const modalC = document.getElementById('modalCrear');
            const modalE = document.getElementById('modalEditar');
            if (event.target === modalC) cerrarModalCrear();
            if (event.target === modalE) cerrarModalEditar();
        }
    </script>

<?php if (!$is_partial): ?>
<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
<?php endif; ?>