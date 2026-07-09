<?php

$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
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

// Crear / Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre_empresa'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $calle = $_POST['calle'] ?? '';
    $esquina = $_POST['esquina'] ?? '';
    $numero = $_POST['numero_puerta'] ?? '';
    $url = $_POST['url'] ?? '';
    $metodo = $_POST['metodo_pago'] ?? '';

    if (!empty($nombre)) {
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
    }
    header("Location: proveedorespanel.php");
    exit;
}

// Borrar / Editar
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? '';
    if ($_GET['action'] === 'delete' && !empty($id)) {
        $stmt = $pdo->prepare("DELETE FROM proveedor WHERE ID_Proveedor=?");
        $stmt->execute([$id]);
        header("Location: proveedorespanel.php");
        exit;
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Proveedores</title>
</head>
<body>
    <h2><?php echo $modo_edicion ? "Editar Proveedor" : "Agregar Nuevo Proveedor"; ?></h2>
    <form action="proveedorespanel.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id_editar; ?>">
        <label>Nombre Empresa:</label><input type="text" name="nombre_empresa" value="<?php echo $nombre_editar; ?>" required><br>
        <label>Email:</label><input type="email" name="email" value="<?php echo $email_editar; ?>"><br>
        <label>Teléfono:</label><input type="text" name="telefono" value="<?php echo $telefono_editar; ?>"><br>
        <label>Calle:</label><input type="text" name="calle" value="<?php echo $calle_editar; ?>"><br>
        <label>Esquina:</label><input type="text" name="esquina" value="<?php echo $esquina_editar; ?>"><br>
        <label>Número de Puerta:</label><input type="text" name="numero_puerta" value="<?php echo $numero_editar; ?>"><br>
        <label>URL Sitio Web:</label><input type="text" name="url" value="<?php echo $url_editar; ?>"><br>
        <label>Método de Pago:</label><input type="text" name="metodo_pago" value="<?php echo $metodo_editar; ?>"><br>
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
                    <a href="proveedorespanel.php?action=edit&id=<?= $prov['ID_Proveedor']; ?>">Editar</a>
                    <a href="proveedorespanel.php?action=delete&id=<?= $prov['ID_Proveedor']; ?>" onclick="return confirm('¿Seguro?');">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
