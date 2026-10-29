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
$nombre_editar = '';
$email_editar = '';
$telefono_editar = '';
$calle_editar = '';
$esquina_editar = '';
$numero_editar = '';
$url_editar = '';
$metodo_editar = '';
$error_message = '';

// Crear / Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nombre = trim($_POST['nombre_empresa'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $calle = trim($_POST['calle'] ?? '');
    $esquina = trim($_POST['esquina'] ?? '');
    $numero = trim($_POST['numero_puerta'] ?? '');
    $url = trim($_POST['url'] ?? '');
    $metodo = trim($_POST['metodo_pago'] ?? '');

    $campos_requeridos = [$nombre, $email, $telefono, $calle, $esquina, $numero, $url, $metodo];
    $falta_campo = false;
    foreach ($campos_requeridos as $valor) {
        if ($valor === '') {
            $falta_campo = true;
            break;
        }
    }

    if ($falta_campo) {
        $error_message = 'Todos los campos deben completarse antes de guardar.';
    } else {
        if (!empty($id)) {
            $stmt = $pdo->prepare("UPDATE proveedor
                SET Nombre_Empresa=?, Email_Contacto=?, Telefono=?, Calle=?, Esquina=?, Numero_Puerta=?, URL_Sitio_web=?, Metodo_Pago=? 
                WHERE ID_Proveedor=?");
            $stmt->execute([$nombre, $email, $telefono, $calle, $esquina, $numero, $url, $metodo, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO proveedor
                (Nombre_Empresa, Email_Contacto, Telefono, Calle, Esquina, Numero_Puerta, URL_Sitio_web, Metodo_Pago)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $email, $telefono, $calle, $esquina, $numero, $url, $metodo]);
        }
        if (!$is_partial) {
            header("Location: proveedorespanel.php");
            exit;
        }
    }
}

// Borrar / Editar
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? '';
    if ($_GET['action'] === 'delete' && !empty($id)) {
        $stmt = $pdo->prepare("DELETE FROM proveedor WHERE ID_Proveedor=?");
        $stmt->execute([$id]);
        if (!$is_partial) {
            header("Location: proveedorespanel.php");
            exit;
        }
    }
    if ($_GET['action'] === 'edit' && !empty($id)) {
        $stmt = $pdo->prepare("SELECT * FROM proveedor WHERE ID_Proveedor=?");
        $stmt->execute([$id]);
        $prov = $stmt->fetch();
        if ($prov) {
            $id_editar = $prov['ID_Proveedor'];
            $nombre_editar = $prov['Nombre_Empresa'];
            $email_editar = $prov['Email_Contacto'];
            $telefono_editar = $prov['Telefono'];
            $calle_editar = $prov['Calle'];
            $esquina_editar = $prov['Esquina'];
            $numero_editar = $prov['Numero_Puerta'];
            $url_editar = $prov['URL_Sitio_web'];
            $metodo_editar = $prov['Metodo_Pago'];
            $modo_edicion = true;
        }
    }
}

// Listado
$stmt = $pdo->query("SELECT * FROM proveedor ORDER BY ID_Proveedor DESC");
$proveedores = $stmt->fetchAll();
?>

<!-- CONTENIDO PRINCIPAL DE PROVEEDORES -->
<div class="main-content">
    <!-- ENCABEZADO CON TÍTULO Y ACCIONES -->
    <div class="encabezado-admin">
        <div>
            <h1 class="titulo-admin"><?php echo $modo_edicion ? "Editar Proveedor" : "Gestión de Proveedores"; ?></h1>
            <p class="texto-sutil"><?php echo $modo_edicion ? "Actualiza la información del proveedor." : "Agrega y administra proveedores certificados de la tienda."; ?></p>
        </div>
    </div>

    <!-- FORMULARIO DE PROVEEDORES -->
    <div class="provider-form-container">
        <h2 class="provider-form-title">
            <?php echo $modo_edicion ? "Editar Proveedor" : "Agregar Nuevo Proveedor"; ?>
        </h2>

        <?php if (!empty($error_message)): ?>
            <div class="alerta alerta--error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="adminpanel.php?page=proveedores&partial=1" method="POST" class="provider-form">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_editar); ?>">

            <div class="form-grid-2">
                <div>
                    <label>Nombre de Empresa:</label>
                    <input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($nombre_editar); ?>" required>
                </div>

                <div>
                    <label>Email de Contacto:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email_editar); ?>" required>
                </div>

                <div>
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono_editar); ?>" required>
                </div>

                <div>
                    <label>Método de Pago:</label>
                    <input type="text" name="metodo_pago" value="<?php echo htmlspecialchars($metodo_editar); ?>" placeholder="Ej: Transferencia, Tarjeta" required>
                </div>

                <div>
                    <label>Calle:</label>
                    <input type="text" name="calle" value="<?php echo htmlspecialchars($calle_editar); ?>" required>
                </div>

                <div>
                    <label>Esquina / Avenida:</label>
                    <input type="text" name="esquina" value="<?php echo htmlspecialchars($esquina_editar); ?>" required>
                </div>

                <div>
                    <label>Número de Puerta:</label>
                    <input type="text" name="numero_puerta" value="<?php echo htmlspecialchars($numero_editar); ?>" required>
                </div>

                <div>
                    <label>URL Sitio Web:</label>
                    <input type="url" name="url" value="<?php echo htmlspecialchars($url_editar); ?>" placeholder="https://ejemplo.com" required>
                </div>
            </div>

            <div class="grupo-acciones">
                <button type="submit" class="btn btn-primary">
                    <?php echo $modo_edicion ? "Actualizar Proveedor" : "Guardar Proveedor"; ?>
                </button>
                <?php if ($modo_edicion): ?>
                    <a href="adminpanel.php?page=proveedores&partial=1" class="btn btn-outline">Cancelar Edición</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- TABLA DE PROVEEDORES -->
    <div class="provider-table-container">
        <h2 class="subtitulo-estadistica">
            Lista de Proveedores Certificados
        </h2>

        <?php if (!empty($proveedores)): ?>
            <table class="provider-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Empresa</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Sitio Web</th>
                        <th>Método Pago</th>
                        <th class="texto-centro">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $prov): ?>
                        <tr>
                            <td class="provider-id">#<?php echo $prov['ID_Proveedor']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($prov['Nombre_Empresa']); ?></strong>
                            </td>
                            <td class="provider-contact"><?php echo htmlspecialchars($prov['Email_Contacto']); ?></td>
                            <td class="provider-contact"><?php echo htmlspecialchars($prov['Telefono']); ?></td>
                            <td class="provider-contact">
                                <?php echo htmlspecialchars($prov['Calle'] . " #" . $prov['Numero_Puerta'] . " Esq. " . $prov['Esquina']); ?>
                            </td>
                            <td class="provider-contact">
                                <a href="<?php echo htmlspecialchars($prov['URL_Sitio_web']); ?>" target="_blank" class="provider-web-link">
                                    Visitar
                                </a>
                            </td>
                            <td>
                                <span class="provider-payment-badge">
                                    <?php echo htmlspecialchars($prov['Metodo_Pago']); ?>
                                </span>
                            </td>
                            <td class="provider-actions">
                                <a href="adminpanel.php?page=proveedores&action=edit&id=<?php echo $prov['ID_Proveedor']; ?>&partial=1" class="btn btn-outline btn-sm">
                                    Editar
                                </a>
                                <a href="adminpanel.php?page=proveedores&action=delete&id=<?php echo $prov['ID_Proveedor']; ?>&partial=1" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('¿Estás seguro de eliminar a <?php echo htmlspecialchars($prov['Nombre_Empresa']); ?>?');">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="provider-empty">
                No hay proveedores registrados. ¡Agrega el primero arriba!
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if (!$is_partial): ?>
    <?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
<?php endif; ?>
