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

<?php if (!$is_partial): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Proveedores</title>
</head>
<body>
<?php endif; ?>
    <h2><?php echo $modo_edicion ? "Editar Proveedor" : "Agregar Nuevo Proveedor"; ?></h2>
    <?php if (!empty($error_message)): ?>
        <p class="text-danger"><strong><?php echo htmlspecialchars($error_message); ?></strong></p>
    <?php endif; ?>
    <form action="proveedorespanel.php?partial=1" method="POST">
        <input type="hidden" name="id" value="<?php echo $id_editar; ?>">
        <label>Nombre Empresa:</label><input type="text" name="nombre_empresa" value="<?php echo htmlspecialchars($nombre_editar); ?>" required><br>
        <label>Email:</label><input type="email" name="email" value="<?php echo htmlspecialchars($email_editar); ?>" required><br>
        <label>Teléfono:</label><input type="text" name="telefono" value="<?php echo htmlspecialchars($telefono_editar); ?>" required><br>
        <label>Calle:</label><input type="text" name="calle" value="<?php echo htmlspecialchars($calle_editar); ?>" required><br>
        <label>Esquina:</label><input type="text" name="esquina" value="<?php echo htmlspecialchars($esquina_editar); ?>" required><br>
        <label>Número de Puerta:</label><input type="text" name="numero_puerta" value="<?php echo htmlspecialchars($numero_editar); ?>" required><br>
        <label>URL Sitio Web:</label><input type="text" name="url" value="<?php echo htmlspecialchars($url_editar); ?>" required><br>
        <label>Método de Pago:</label><input type="text" name="metodo_pago" value="<?php echo htmlspecialchars($metodo_editar); ?>" required><br>
        <button type="submit"><?php echo $modo_edicion ? "Actualizar" : "Guardar"; ?></button>
    </form>

    <h2>Lista de Proveedores</h2>
    <table border="1">
        <tr><th>ID</th><th>Empresa</th><th>Email</th><th>Teléfono</th><th>Dirección</th><th>Web</th><th>Método Pago</th><th>Acciones</th></tr>
        <?php foreach ($proveedores as $prov): ?>
            <tr>
                <td><?= $prov['ID_Proveedor']; ?></td>
                <td><?= htmlspecialchars($prov['Nombre_Empresa']); ?></td>
                <td><?= htmlspecialchars($prov['Email_Contacto']); ?></td>
                <td><?= htmlspecialchars($prov['Telefono']); ?></td>
                <td><?= htmlspecialchars($prov['Calle']." ".$prov['Numero_Puerta']." Esq. ".$prov['Esquina']); ?></td>
                <td><?= htmlspecialchars($prov['URL_Sitio_web']); ?></td>
                <td><?= htmlspecialchars($prov['Metodo_Pago']); ?></td>
                <td>
                    <a class="partial-link" href="proveedorespanel.php?action=edit&id=<?= $prov['ID_Proveedor']; ?>&partial=1">Editar</a>
                    <a class="partial-link" href="proveedorespanel.php?action=delete&id=<?= $prov['ID_Proveedor']; ?>&partial=1" data-confirm="¿Seguro?">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php if (!$is_partial): ?>
</body>
</html>
<?php endif; ?>
