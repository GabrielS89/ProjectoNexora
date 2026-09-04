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
$descripcion_editar = '';
$precio_editar = '';
$stock_editar = '';
$categoria_editar = '';
$proveedor_editar = '';
$error_message = '';

// Crear / Actualizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = $_POST['precio'] ?? '';
    $stock = $_POST['stock'] ?? '';
    $categoria = trim($_POST['categoria'] ?? '');
    $proveedor = $_POST['id_proveedor'] ?? '';

    $campos_requeridos = [$nombre, $descripcion, $precio, $stock, $categoria, $proveedor];
    $falta_campo = false;
    foreach ($campos_requeridos as $valor) {
        if ($valor === '' || $valor === null) {
            $falta_campo = true;
            break;
        }
    }

    if ($falta_campo) {
        $error_message = 'Todos los campos deben completarse antes de guardar.';
    } else {
        try {
            if (!empty($id)) {
                $stmt = $pdo->prepare("UPDATE producto 
                    SET Nombre=?, Descripcion=?, Precio_Base=?, Stock_Actual=?, Categoria=?, ID_Proveedor=? 
                    WHERE ID_Producto=?");
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor, $id]);
                $id_editar = '';
                $modo_edicion = false;
                $nombre_editar = '';
                $descripcion_editar = '';
                $precio_editar = '';
                $stock_editar = '';
                $categoria_editar = '';
                $proveedor_editar = '';
            } else {
                $stmt = $pdo->prepare("INSERT INTO producto 
                    (Nombre, Descripcion, Precio_Base, Stock_Actual, Categoria, ID_Proveedor) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor]);
            }
        } catch (Exception $e) {
            $error_message = 'Error al guardar el producto: ' . $e->getMessage();
        }
        if (!$is_partial) {
            header("Location: adminpanelTienda.php");
            exit;
        }
    }
}

// Borrar / Editar
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? '';
    if ($_GET['action'] === 'delete' && !empty($id)) {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("DELETE FROM detalle_pedido WHERE ID_Producto = ?");
            $stmt->execute([$id]);
            $stmt = $pdo->prepare("DELETE FROM producto WHERE ID_Producto = ?");
            $stmt->execute([$id]);
            $pdo->commit();
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error_message = 'Error al eliminar el producto: ' . $e->getMessage();
        }
        if (!$is_partial) {
            header("Location: adminpanelTienda.php");
            exit;
        }
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

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--card-border); padding-bottom: 0.8rem;">
        <h2 style="margin: 0; font-size: 1.3rem;"><?php echo $modo_edicion ? "✏ Editar Artículo de Tienda" : "➕ Agregar Nuevo Artículo a la Tienda"; ?></h2>
        <?php if ($modo_edicion): ?>
            <a href="adminpanelTienda.php?partial=1" class="partial-link btn btn-outline btn-sm">Cancelar Edición</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="alert-box alert-danger">
            <span class="alert-tag">ERROR</span>
            <div><?php echo htmlspecialchars($error_message); ?></div>
        </div>
    <?php endif; ?>

    <form action="adminpanelTienda.php?partial=1" method="POST" style="margin-bottom: 2.5rem; background: rgba(0,0,0,0.2); padding: 1.5rem; border-radius: 10px; border: 1px solid var(--card-border);">
        <input type="hidden" name="id" value="<?php echo $id_editar; ?>">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
            <div>
                <label>Nombre del Producto / Artículo: <span style="color:#ef4444;">*</span></label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($nombre_editar); ?>" placeholder="Ej: Router Wi-Fi Mesh Pro" required>
            </div>
            <div>
                <label>Categoría: <span style="color:#ef4444;">*</span></label>
                <input type="text" name="categoria" value="<?php echo htmlspecialchars($categoria_editar); ?>" placeholder="Ej: Networking, Hardware, Servidores" required>
            </div>
            <div>
                <label>Precio Base (USD): <span style="color:#ef4444;">*</span></label>
                <input type="number" step="0.01" name="precio" value="<?php echo htmlspecialchars($precio_editar); ?>" placeholder="0.00" required>
            </div>
            <div>
                <label>Stock Disponible: <span style="color:#ef4444;">*</span></label>
                <input type="number" name="stock" value="<?php echo htmlspecialchars($stock_editar); ?>" placeholder="0" required>
            </div>
            <div style="grid-column: 1 / -1;">
                <label>Proveedor Responsable: <span style="color:#ef4444;">*</span></label>
                <select name="id_proveedor" required>
                    <option value="">-- Selecciona un Proveedor --</option>
                    <?php foreach ($proveedores as $prov): ?>
                        <option value="<?= $prov['ID_Proveedor']; ?>" <?= ($prov['ID_Proveedor']==$proveedor_editar)?'selected':''; ?>>
                            <?= htmlspecialchars($prov['Nombre_Empresa']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="grid-column: 1 / -1;">
                <label>Descripción del Producto:</label>
                <textarea name="descripcion" rows="3" placeholder="Detalles técnicos y especificaciones..." required><?php echo htmlspecialchars($descripcion_editar); ?></textarea>
            </div>
        </div>

        <div style="margin-top: 1rem; display: flex; gap: 0.8rem;">
            <button type="submit" class="btn btn-primary"><?php echo $modo_edicion ? "Actualizar Producto" : "Guardar Producto"; ?></button>
            <?php if ($modo_edicion): ?>
                <a href="adminpanelTienda.php?partial=1" class="partial-link btn btn-outline">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>

    <div style="margin-top: 2rem;">
        <h2 style="font-size: 1.3rem; margin-bottom: 1rem;">📦 Catálogo Actual de Productos</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $p): ?>
                            <tr>
                                <td><strong>#<?= $p['ID_Producto']; ?></strong></td>
                                <td><?= htmlspecialchars($p['Nombre']); ?></td>
                                <td><span class="producto-categoria-tag" style="margin: 0; font-size: 0.72rem;"><?= htmlspecialchars($p['Categoria']); ?></span></td>
                                <td style="font-weight: 700; color: #4ade80;">$<?= number_format($p['Precio_Base'], 2); ?> USD</td>
                                <td>
                                    <?php if ($p['Stock_Actual'] > 0): ?>
                                        <span style="color: var(--primary-green-hover); font-weight: 600;"><?= $p['Stock_Actual']; ?> unid.</span>
                                    <?php else: ?>
                                        <span style="color: #ef4444; font-weight: 600;">Sin stock</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['Nombre_Empresa']); ?></td>
                                <td>
                                    <a class="partial-link" href="adminpanelTienda.php?action=edit&id=<?= $p['ID_Producto']; ?>&partial=1">✏ Editar</a>
                                    <a class="partial-link" href="adminpanelTienda.php?action=delete&id=<?= $p['ID_Producto']; ?>&partial=1" data-confirm="¿Seguro que deseas eliminar este producto?">🗑 Borrar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No hay productos registrados en la tienda actualmente.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php if (!$is_partial): ?>
<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
<?php endif; ?>
