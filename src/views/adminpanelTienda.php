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
$descripcion_editar = '';
$precio_editar = '';
$stock_editar = '';
$categoria_editar = '';
$proveedor_editar = '';

// Crear / Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $categoria = $_POST['categoria'] ?? '';
    $proveedor = $_POST['id_proveedor'] ?? '';

    if (!empty($nombre)) {
        if (!empty($id)) {
            $stmt = $pdo->prepare("UPDATE producto 
                SET Nombre=?, Descripcion=?, Precio_Base=?, Stock_Actual=?, Categoria=?, ID_Proveedor=? 
                WHERE ID_Producto=?");
            $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO producto 
                (Nombre, Descripcion, Precio_Base, Stock_Actual, Categoria, ID_Proveedor) 
                VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor]);
        }
    }
    header("Location: adminpanelTienda.php");
    exit;
}

// Borrar / Editar
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? '';
    if ($_GET['action'] === 'delete' && !empty($id)) {
        $stmt = $pdo->prepare("DELETE FROM producto WHERE ID_Producto=?");
        $stmt->execute([$id]);
        header("Location: adminpanelTienda.php");
        exit;
    }
    if ($_GET['action'] === 'edit' && !empty($id)) {
        $stmt = $pdo->prepare("SELECT * FROM producto WHERE ID_Producto=?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch();
        if ($producto) {
            $id_editar = $producto['ID_Producto'];
            $nombre_editar = $producto['Nombre'];
            $descripcion_editar = $producto['Descripcion'];
            $precio_editar = $producto['Precio_Base'];
            $stock_editar = $producto['Stock_Actual'];
            $categoria_editar = $producto['Categoria'];
            $proveedor_editar = $producto['ID_Proveedor'];
            $modo_edicion = true;
        }
    }
}

// Listado con JOIN
$stmt = $pdo->query("
    SELECT p.ID_Producto, p.Nombre, p.Descripcion, p.Precio_Base, p.Stock_Actual, p.Categoria, pr.Nombre_Empresa
    FROM producto p
    LEFT JOIN proveedor pr ON p.ID_Proveedor = pr.ID_Proveedor
    ORDER BY p.ID_Producto DESC
");
$productos = $stmt->fetchAll();

// Proveedores para el select
$stmtProv = $pdo->query("SELECT ID_Proveedor, Nombre_Empresa FROM proveedor ORDER BY Nombre_Empresa ASC");
$proveedores = $stmtProv->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Productos</title>
</head>
<body>
    <h2><?php echo $modo_edicion ? "Editar Producto" : "Agregar Nuevo Producto"; ?></h2>
    <form action="adminpanelTienda.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id_editar; ?>">
        <label>Nombre:</label><input type="text" name="nombre" value="<?php echo $nombre_editar; ?>" required><br>
        <label>Descripción:</label><textarea name="descripcion"><?php echo $descripcion_editar; ?></textarea><br>
        <label>Precio Base:</label><input type="number" step="0.01" name="precio" value="<?php echo $precio_editar; ?>"><br>
        <label>Stock:</label><input type="number" name="stock" value="<?php echo $stock_editar; ?>"><br>
        <label>Categoría:</label><input type="text" name="categoria" value="<?php echo $categoria_editar; ?>"><br>
        <label>Proveedor:</label>
        <select name="id_proveedor">
            <option value="">-- Selecciona --</option>
            <?php foreach ($proveedores as $prov): ?>
                <option value="<?= $prov['ID_Proveedor']; ?>" <?= ($prov['ID_Proveedor']==$proveedor_editar)?'selected':''; ?>>
                    <?= htmlspecialchars($prov['Nombre_Empresa']); ?>
                </option>
            <?php endforeach; ?>
        </select><br>
        <button type="submit"><?php echo $modo_edicion ? "Actualizar" : "Guardar"; ?></button>
    </form>

    <h2>Lista de Productos</h2>
    <table border="1">
        <tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Stock</th><th>Categoría</th><th>Proveedor</th><th>Acciones</th></tr>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= $p['ID_Producto']; ?></td>
                <td><?= htmlspecialchars($p['Nombre']); ?></td>
                <td><?= htmlspecialchars($p['Descripcion']); ?></td>
                <td><?= $p['Precio_Base']; ?></td>
                <td><?= $p['Stock_Actual']; ?></td>
                <td><?= htmlspecialchars($p['Categoria']); ?></td>
                <td><?= htmlspecialchars($p['Nombre_Empresa']); ?></td>
                <td>
                    <a href="adminpanelTienda.php?action=edit&id=<?= $p['ID_Producto']; ?>">Editar</a>
                    <a href="adminpanelTienda.php?action=delete&id=<?= $p['ID_Producto']; ?>" onclick="return confirm('¿Seguro?');">Borrar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
