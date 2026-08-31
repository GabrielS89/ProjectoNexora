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

// Variables para edición
$id_editar = '';
$modo_edicion = false;
$email_editar = '';
$nombre_editar = '';
$telefono_editar = '';
$rol_editar = '1';
$tipo_persona_editar = 'Persona';
$password_editar = '';
$error_message = '';

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

// Crear / Actualizar
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

    $campos_requeridos = [$email, $nombre, $telefono];
    if (empty($id)) {
        $campos_requeridos[] = $password;
    }
    if ($tipo_persona === 'Empresa') {
        $campos_requeridos = array_merge($campos_requeridos, [$nombre_empresa, $rut,$calle,$esquina,$numpuerta, $descripcion, $rep_nombre, $rep_cargo, $rep_email, $rep_telefono]);
    }

    $falta_campo = false;
    foreach ($campos_requeridos as $valor) {
        if (empty($valor)) {
            $falta_campo = true;
            break;
        }
    }

    if ($falta_campo) {
        $error_message = 'Todos los campos visibles deben completarse antes de guardar.';
    } else {
        if ($tipo_persona === 'Empresa') {
            $campos_validos = !empty($nombre_empresa) && !empty($rut) && !empty($calle) && !empty($esquina) && !empty($numpuerta) && !empty($descripcion) && !empty($rep_nombre) && !empty($rep_cargo) && !empty($rep_email) && !empty($rep_telefono);
        } else {
            $campos_validos = true;
        }

        if ($campos_validos) {
            if (!empty($id)) {
                // Actualizar usuario existente
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuario SET Email=?, Nombre=?, Telefono=?, Tipo_Persona=?, ID_Rol=?, Contrasena=? WHERE ID_Usuario=?");
                    $stmt->execute([$email, $nombre, $telefono, $tipo_persona, $rol, $hash, $id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE usuario SET Email=?, Nombre=?, Telefono=?, Tipo_Persona=?, ID_Rol=? WHERE ID_Usuario=?");
                    $stmt->execute([$email, $nombre, $telefono, $tipo_persona, $rol, $id]);
                }

                if ($tipo_persona === 'Empresa') {
                    $stmt = $pdo->prepare("DELETE FROM persona WHERE ID_Usuario=?");
                    $stmt->execute([$id]);

                    $stmtRep = $pdo->prepare("SELECT ID_Representante FROM empresa WHERE ID_USUARIO=?");
                    $stmtRep->execute([$id]);
                    $rep = $stmtRep->fetch();
                    if ($rep) {
                        $stmt = $pdo->prepare("UPDATE representante SET Nombre_Completo=?, Cargo=?, Email_Contacto=?, Telefono_Contacto=? WHERE ID_Representante=?");
                        $stmt->execute([$rep_nombre, $rep_cargo, $rep_email, $rep_telefono, $rep['ID_Representante']]);
                        $representante_id = $rep['ID_Representante'];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO representante (Nombre_Completo, Cargo, Email_Contacto, Telefono_Contacto) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$rep_nombre, $rep_cargo, $rep_email, $rep_telefono]);
                        $representante_id = $pdo->lastInsertId();
                    }

                    $stmtEmp = $pdo->prepare("SELECT ID_Empresa FROM empresa WHERE ID_USUARIO=?");
                    $stmtEmp->execute([$id]);
                    $empresa = $stmtEmp->fetch();
                    if ($empresa) {
                        $stmt = $pdo->prepare("UPDATE empresa SET ID_USUARIO=?, RUT=?, Nombre_Empresa=?, Calle=?, Esquina=?, Numero_Puerta=?, Descripcion=?, Categoria_Empresarial=?, ID_Representante=? WHERE ID_Empresa=?");
                        $stmt->execute([$id, $rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $descripcion, $representante_id, $empresa['ID_Empresa']]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO empresa (ID_USUARIO, RUT, Nombre_Empresa, Calle, Esquina, Numero_Puerta, Descripcion, Categoria_Empresarial, ID_Representante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$id, $rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $descripcion, $representante_id]);
                    }
                } else {
                    $stmtGetRep = $pdo->prepare("SELECT ID_Representante FROM empresa WHERE ID_USUARIO=?");
                    $stmtGetRep->execute([$id]);
                    $repToDelete = $stmtGetRep->fetch();
                    $stmt = $pdo->prepare("DELETE FROM empresa WHERE ID_USUARIO=?");
                    $stmt->execute([$id]);
                    if ($repToDelete && $repToDelete['ID_Representante']) {
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
            } else {
                if (empty($password)) {
                    $error_message = 'La contraseña es obligatoria para crear un usuario.';
                } else {
                    $passwordToHash = $password;
                    $hash = password_hash($passwordToHash, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO usuario (Email, Contrasena, Nombre, Telefono, Tipo_Persona, ID_Rol) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$email, $hash, $nombre, $telefono, $tipo_persona, $rol]);
                    $newUserId = $pdo->lastInsertId();

                    if ($tipo_persona === 'Empresa') {
                        $stmt = $pdo->prepare("INSERT INTO representante (Nombre_Completo, Cargo, Email_Contacto, Telefono_Contacto) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$rep_nombre, $rep_cargo, $rep_email, $rep_telefono]);
                        $representante_id = $pdo->lastInsertId();

                        $stmt = $pdo->prepare("INSERT INTO empresa (ID_USUARIO, RUT, Nombre_Empresa, Calle, Esquina, Numero_Puerta, Descripcion, Categoria_Empresarial, ID_Representante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$newUserId, $rut, $nombre_empresa, $calle, $esquina, $numpuerta, $descripcion, $descripcion, $representante_id]);
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO persona (ID_Usuario) VALUES (?)");
                        $stmt->execute([$newUserId]);
                    }
                }
            }
        }
    }

    if (!$is_partial) {
        header('Location: usuariospanel.php');
        exit;
    }
}

// Borrar / Editar
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? '';
    if ($_GET['action'] === 'delete' && !empty($id)) {
        $stmt = $pdo->prepare("DELETE FROM usuario WHERE ID_Usuario=?");
        $stmt->execute([$id]);
        if (!$is_partial) {
            header('Location: usuariospanel.php');
            exit;
        }
    }
    if ($_GET['action'] === 'edit' && !empty($id)) {
        $stmt = $pdo->prepare(
            "SELECT u.*, p.ID_Usuario AS persona_existente
            FROM usuario u
            LEFT JOIN persona p ON p.ID_Usuario = u.ID_Usuario
            WHERE u.ID_Usuario = ?"
        );
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
                $stmtEmpresa = $pdo->prepare("SELECT e.ID_Empresa, e.RUT, e.Nombre_Empresa, e.Calle, e.Esquina, e.Numero_Puerta, e.Descripcion, e.Categoria_Empresarial, r.Nombre_Completo, r.Cargo, r.Email_Contacto, r.Telefono_Contacto FROM empresa e LEFT JOIN representante r ON r.ID_Representante = e.ID_Representante WHERE e.ID_USUARIO = ?");
                $stmtEmpresa->execute([$id]);
                $empresaData = $stmtEmpresa->fetch();

                if ($empresaData) {
                    $nombre_empresa_editar = $empresaData['Nombre_Empresa'] ?? '';
                    $rut_editar = $empresaData['RUT'] ?? '';
                    $calle_editar = $empresaData['Calle'] ?? '';
                    $esquina_editar = $empresaData['Esquina'] ?? '';
                    $numpuerta_editar = $empresaData['Numero_Puerta'] ?? '';
                    $descripcion_editar = $empresaData['Descripcion'] ?? '';
                    $rep_nombre_editar = $empresaData['Nombre_Completo'] ?? '';
                    $rep_cargo_editar = $empresaData['Cargo'] ?? '';
                    $rep_email_editar = $empresaData['Email_Contacto'] ?? '';
                    $rep_telefono_editar = $empresaData['Telefono_Contacto'] ?? '';
                }
            }
        }
    }
}

// Roles y listado de usuarios
$stmtRoles = $pdo->query("SELECT ID_Rol, TipoRol FROM rol ORDER BY ID_Rol ASC");
$roles = $stmtRoles->fetchAll();
$stmt = $pdo->query("SELECT u.ID_Usuario, u.Email, u.Nombre, u.Telefono, u.Tipo_Persona, u.ID_Rol, r.TipoRol FROM usuario u LEFT JOIN rol r ON u.ID_Rol = r.ID_Rol ORDER BY u.ID_Usuario DESC");
$usuarios = $stmt->fetchAll();
?>

<?php if (!$is_partial): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Usuarios</title>
</head>
<body>
<?php endif; ?>
    <h2><?php echo $modo_edicion ? "Editar Usuario" : "Agregar Nuevo Usuario"; ?></h2>
    <?php if (!empty($error_message)): ?>
        <p style="color: red;"><strong><?php echo htmlspecialchars($error_message); ?></strong></p>
    <?php endif; ?>
    <form action="usuariospanel.php?partial=1" method="POST" id="usuarioForm">
        <input type="hidden" name="id" value="<?php echo $id_editar; ?>">
        <label>Email:</label><input type="email" name="email" value="<?php echo htmlspecialchars($email_editar); ?>" required><br>
        <label>Nombre:</label><input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre_editar); ?>" required><br>
        <label>Teléfono:</label><input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono_editar); ?>" required><br>
        <label>Tipo Persona:</label>
        <select name="tipo_persona" id="tipo_persona" onchange="mostrarCamposTipo()" required>
            <option value="">-- Selecciona --</option>
            <option value="Persona" <?php echo ($tipo_persona_editar === 'Persona') ? 'selected' : ''; ?>>Persona</option>
            <option value="Empresa" <?php echo ($tipo_persona_editar === 'Empresa') ? 'selected' : ''; ?>>Empresa</option>
        </select><br>
        <label>Rol:</label>
        <select name="id_rol" required>
            <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['ID_Rol']; ?>" <?= ($rol['ID_Rol'] == $rol_editar) ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($rol['TipoRol']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <div id="personaFields" style="display: none;">
            <h3>Datos Persona</h3>
            <p>Este usuario no requiere datos empresariales. Solo complete los datos básicos.</p>
        </div>

        <div id="empresaFields" style="display: none;">
            <h3>Datos Empresa</h3>
            <label>Nombre de la Empresa:</label><input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($nombre_empresa_editar); ?>"><br>
            <label>RUT:</label><input type="text" name="rut" value="<?php echo htmlspecialchars($rut_editar); ?>"><br>
            <label>Calle:</label><input type="text" name="calle" value="<?php echo htmlspecialchars($calle_editar); ?>"><br>
            <label>Esquina:</label><input type="text" name="esquina" value="<?php echo htmlspecialchars($esquina_editar); ?>"><br>
            <label>Numero de Puerta:</label><input type="text" name="numpuerta" value="<?php echo htmlspecialchars($numpuerta_editar); ?>"><br>
            <label>Descripción:</label><textarea name="descripcion"><?php echo htmlspecialchars($descripcion_editar); ?></textarea><br>
            <h4>Datos del Representante</h4>
            <label>Nombre Representante:</label><input type="text" name="rep_nombre" value="<?php echo htmlspecialchars($rep_nombre_editar); ?>"><br>
            <label>Cargo:</label><input type="text" name="rep_cargo" value="<?php echo htmlspecialchars($rep_cargo_editar); ?>"><br>
            <label>Email del Representante:</label><input type="email" name="rep_email" value="<?php echo htmlspecialchars($rep_email_editar); ?>"><br>
            <label>Teléfono del Representante:</label><input type="text" name="rep_telefono" value="<?php echo htmlspecialchars($rep_telefono_editar); ?>"><br>
        </div>

        <label>Contraseña:</label><input type="password" name="password" value=""><br>
        <small><?php echo $modo_edicion ? 'Dejar en blanco para mantener la contraseña actual.' : 'La contraseña es obligatoria al crear un usuario.'; ?></small><br>
        <button type="submit"><?php echo $modo_edicion ? "Actualizar" : "Guardar"; ?></button>
    </form>

    <h2>Lista de Usuarios</h2>
    <table border="1">
        <tr><th>ID</th><th>Email</th><th>Nombre</th><th>Teléfono</th><th>Tipo Persona</th><th>Rol</th><th>Acciones</th></tr>
        <?php foreach ($usuarios as $user): ?>
            <tr>
                <td><?= $user['ID_Usuario']; ?></td>
                <td><?= htmlspecialchars($user['Email']); ?></td>
                <td><?= htmlspecialchars($user['Nombre']); ?></td>
                <td><?= htmlspecialchars($user['Telefono']); ?></td>
                <td><?= htmlspecialchars($user['Tipo_Persona']); ?></td>
                <td><?= htmlspecialchars($user['TipoRol'] ?? 'N/A'); ?></td>
                <td>
                    <a class="partial-link" href="usuariospanel.php?action=edit&id=<?= $user['ID_Usuario']; ?>&partial=1">Editar</a>
                    <a class="partial-link" href="usuariospanel.php?action=delete&id=<?= $user['ID_Usuario']; ?>&partial=1" data-confirm="¿Seguro que deseas eliminar este usuario?">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function mostrarCamposTipo() {
            const tipo = document.getElementById('tipo_persona').value;
            const personaFields = document.getElementById('personaFields');
            const empresaFields = document.getElementById('empresaFields');
            const empresaInputs = empresaFields.querySelectorAll('input, textarea, select');

            if (tipo === 'Empresa') {
                empresaFields.style.display = 'block';
                personaFields.style.display = 'none';
                empresaInputs.forEach(el => {
                    el.disabled = false;
                    if (el.name) el.required = true;
                });
            } else {
                empresaFields.style.display = 'none';
                personaFields.style.display = 'block';
                empresaInputs.forEach(el => {
                    el.disabled = true;
                    el.required = false;
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            mostrarCamposTipo();
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo_persona');
            if (tipoSelect) {
                tipoSelect.addEventListener('change', mostrarCamposTipo);
            }
        });
    </script>

<?php if (!$is_partial): ?>
</body>
</html>
<?php endif; ?>