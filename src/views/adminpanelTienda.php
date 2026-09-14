<?php
$require_admin = true;
$page_title = 'Administración de Tienda - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Requerir conexión a la base de datos
require_once(__DIR__ . '/../../database/database.php');

$error_message = '';
$msg_message = '';

// PROCESAR CREAR O EDITAR PRODUCTO (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_product'])) {
    $id          = (int)($_POST['id_producto'] ?? 0);
    $nombre      = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $descripcion = htmlspecialchars(trim($_POST['descripcion'] ?? ''));
    $precio      = (float)($_POST['precio'] ?? 0);
    $stock       = (int)($_POST['stock'] ?? 0);
    $categoria   = htmlspecialchars(trim($_POST['categoria'] ?? 'General'));
    $proveedor   = (int)($_POST['id_proveedor'] ?? 1);

    if (empty($nombre) || empty($descripcion) || $precio <= 0) {
        $error_message = 'Por favor, completa todos los campos requeridos y asigna un precio válido.';
    } else {
        try {
            if ($id > 0) {
                // Actualizar producto existente
                $stmt = $pdo->prepare("UPDATE producto 
                    SET Nombre=?, Descripcion=?, Precio_Base=?, Stock_Actual=?, Categoria=?, ID_Proveedor=? 
                    WHERE ID_Producto=?");
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor, $id]);
                header("Location: adminpanelTienda.php?msg=producto_actualizado");
                exit;
            } else {
                // Crear nuevo producto
                $stmt = $pdo->prepare("INSERT INTO producto 
                    (Nombre, Descripcion, Precio_Base, Stock_Actual, Categoria, ID_Proveedor) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor]);
                header("Location: adminpanelTienda.php?msg=producto_creado");
                exit;
            }
        } catch (Exception $e) {
            $error_message = 'Error en la base de datos: ' . $e->getMessage();
        }
    }
}

// PROCESAR ELIMINAR PRODUCTO (GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM producto WHERE ID_Producto=?");
            $stmt->execute([$id]);
            header("Location: adminpanelTienda.php?msg=producto_eliminado");
            exit;
        } catch (Exception $e) {
            $error_message = 'No se pudo eliminar el producto. Podría estar asociado a pedidos existentes.';
        }
    }
}

// CONSULTAR LISTA DE PRODUCTOS
$productos = [];
try {
    $stmt = $pdo->query("
        SELECT p.ID_Producto, p.Nombre, p.Descripcion, p.Precio_Base, p.Stock_Actual, p.Categoria, p.ID_Proveedor, pr.Nombre_Empresa as ProveedorNombre
        FROM producto p
        LEFT JOIN proveedor pr ON p.ID_Proveedor = pr.ID_Proveedor
        ORDER BY p.ID_Producto DESC
    ");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $productos = [];
}

// CONSULTAR PROVEEDORES PARA EL SELECT DEL FORMULARIO
$proveedores = [];
try {
    $stmtProv = $pdo->query("SELECT ID_Proveedor, Nombre_Empresa FROM proveedor ORDER BY Nombre_Empresa ASC");
    $proveedores = $stmtProv->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $proveedores = [];
}

// Si la lista de proveedores está vacía, añadimos una por defecto
if (empty($proveedores)) {
    $proveedores = [
        ['ID_Proveedor' => 1, 'Nombre_Empresa' => 'TechSupply Uruguay']
    ];
}

// MÉTICASH
$totalProductos = count($productos);
$totalStock = array_sum(array_column($productos, 'Stock_Actual'));
$categoriasUnicas = count(array_unique(array_column($productos, 'Categoria')));
?>

<!-- CABECERA Y NAVEGACIÓN DE PESTAÑAS ADMIN -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 2.2rem; margin-bottom: 0.2rem;">Gestión de Productos (Tienda)</h1>
        <p style="color: var(--text-muted);">Agrega, edita y administra los artículos del catálogo público.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="btn btn-primary" onclick="abrirModalCrearProd()">+ Agregar Producto</button>
        <a href="<?php echo VIEWS_PATH; ?>/tienda.php" class="btn btn-outline" target="_blank">Ver Tienda Pública ↗</a>
        <a href="<?php echo PUBLIC_PATH; ?>/logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</div>

<!-- NAVEGACIÓN SUB-PESTAÑAS DE ADMINISTRACIÓN -->
<div style="background: var(--card-bg); border: 1px solid var(--card-border); padding: 8px; border-radius: 12px; display: flex; gap: 8px; margin-bottom: 2rem; box-shadow: var(--shadow-sm);">
    <a href="adminpanel.php" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; color: var(--text-muted); transition: 0.3s;">
        👥 Gestión de Usuarios
    </a>
    <a href="adminpanelTienda.php" style="padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; background: var(--primary-green); color: white; box-shadow: var(--shadow-sm);">
        🛍️ Gestión de Productos (Tienda)
    </a>
</div>

<!-- MUESTRA DE ALERTAS Y NOTIFICACIONES -->
<?php if (isset($_GET['msg'])): ?>
    <?php 
        $msgTexto = 'Operación realizada correctamente.';
        if ($_GET['msg'] === 'producto_creado') $msgTexto = '¡Producto agregado al catálogo exitosamente!';
        if ($_GET['msg'] === 'producto_actualizado') $msgTexto = '¡Datos del producto actualizados correctamente!';
        if ($_GET['msg'] === 'producto_eliminado') $msgTexto = '¡Producto eliminado del catálogo!';
    ?>
    <div class="alert" style="background: rgba(13, 125, 83, 0.12); color: var(--primary-green); border: 1.5px solid rgba(13, 125, 83, 0.3); text-align: center; margin-bottom: 2rem;">
        <?php echo $msgTexto; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert" style="background: rgba(220, 53, 69, 0.12); color: #d32f2f; border: 1.5px solid rgba(220, 53, 69, 0.3); text-align: center; margin-bottom: 2rem;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<!-- TARJETAS ESTADÍSTICAS TIENDA -->
<div class="features-grid" style="margin-top: 0; margin-bottom: 2.5rem;">
    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Total de Productos</span>
        <h2 style="font-size: 2.2rem; color: var(--primary-green); margin-top: 0.3rem;">
            <?php echo $totalProductos; ?>
        </h2>
    </div>

    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Categorías Activas</span>
        <h2 style="font-size: 2.2rem; color: var(--primary-green); margin-top: 0.3rem;">
            <?php echo $categoriasUnicas; ?>
        </h2>
    </div>

    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Unidades en Stock</span>
        <h2 style="font-size: 2.2rem; color: var(--primary-green); margin-top: 0.3rem;">
            <?php echo $totalStock; ?>
        </h2>
    </div>
</div>

<!-- TABLA DE PRODUCTOS DE LA TIENDA -->
<div class="feature-card" style="background: var(--card-bg); padding: 1.8rem; overflow-x: auto;">
    <h2 style="margin-bottom: 1.2rem; font-size: 1.4rem; color: var(--primary-green);">Catálogo de Productos</h2>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
        <thead>
            <tr style="border-bottom: 2px solid var(--card-border); color: var(--text-heading);">
                <th style="padding: 12px 10px;">ID</th>
                <th style="padding: 12px 10px;">Producto</th>
                <th style="padding: 12px 10px;">Categoría</th>
                <th style="padding: 12px 10px;">Precio USD</th>
                <th style="padding: 12px 10px;">Stock</th>
                <th style="padding: 12px 10px;">Proveedor</th>
                <th style="padding: 12px 10px; text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $prod): ?>
                    <tr style="border-bottom: 1px solid rgba(13, 125, 83, 0.1);">
                        <td style="padding: 12px 10px; font-weight: bold; color: var(--primary-green);">#<?php echo $prod['ID_Producto']; ?></td>
                        <td style="padding: 12px 10px;">
                            <strong style="color: var(--text-heading); font-size: 1rem;"><?php echo htmlspecialchars($prod['Nombre']); ?></strong>
                            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 2px; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?php echo htmlspecialchars($prod['Descripcion']); ?>
                            </p>
                        </td>
                        <td style="padding: 12px 10px;">
                            <span style="background: var(--light-green-tint); color: var(--primary-green); padding: 4px 12px; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">
                                <?php echo htmlspecialchars($prod['Categoria'] ?? 'General'); ?>
                            </span>
                        </td>
                        <td style="padding: 12px 10px; font-weight: 800; color: var(--primary-green); font-size: 1.05rem;">
                            $<?php echo number_format((float)$prod['Precio_Base'], 2); ?>
                        </td>
                        <td style="padding: 12px 10px; font-weight: 600;">
                            <?php echo (int)$prod['Stock_Actual']; ?> u.
                        </td>
                        <td style="padding: 12px 10px; color: var(--text-muted); font-size: 0.9rem;">
                            <?php echo htmlspecialchars($prod['ProveedorNombre'] ?? 'TechSupply Uruguay'); ?>
                        </td>
                        <td style="padding: 12px 10px; text-align: center;">
                            <div style="display: flex; justify-content: center; gap: 8px;">
                                <button type="button" class="btn btn-outline" style="padding: 5px 12px; font-size: 0.82rem;"
                                    onclick='abrirModalEditarProd(<?php echo json_encode($prod, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    Editar
                                </button>
                                <a href="adminpanelTienda.php?action=delete&id=<?php echo $prod['ID_Producto']; ?>" 
                                   class="btn btn-danger" style="padding: 5px 12px; font-size: 0.82rem;"
                                   onclick="return confirm('¿Estás seguro de eliminar el producto <?php echo htmlspecialchars($prod['Nombre']); ?>?');">
                                    Eliminar
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="padding: 20px; text-align: center; color: var(--text-muted);">No hay productos registrados en el catálogo de la tienda.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- MODAL 1: CREAR NUEVO PRODUCTO -->
<!-- ========================================== -->
<div id="modalCrearProd" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); border: 1px solid var(--card-border); padding: 2.2rem; border-radius: 16px; width: 90%; max-width: 540px; box-shadow: var(--shadow-lg); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary-green); font-size: 1.5rem; margin: 0;">Agregar Producto a la Tienda</h2>
            <button type="button" onclick="cerrarModalCrearProd()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="adminpanelTienda.php" method="POST" style="margin: 0; padding: 0; background: transparent; box-shadow: none; border: none;">
            <input type="hidden" name="action_product" value="1">
            <input type="hidden" name="id_producto" value="0">

            <label for="p_nombre">Nombre del Producto:</label>
            <input type="text" id="p_nombre" name="nombre" placeholder="Ej. Servidor Rack 2U Intel Xeon" required>

            <label for="p_descripcion">Descripción Completa:</label>
            <textarea id="p_descripcion" name="descripcion" rows="3" placeholder="Detalles de hardware, especificaciones y garantía..." required></textarea>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label for="p_precio">Precio Base (USD):</label>
                    <input type="number" step="0.01" id="p_precio" name="precio" placeholder="250.00" required>
                </div>
                <div>
                    <label for="p_stock">Stock Inicial:</label>
                    <input type="number" id="p_stock" name="stock" placeholder="10" required>
                </div>
            </div>

            <label for="p_categoria">Categoría:</label>
            <input type="text" id="p_categoria" name="categoria" placeholder="Ej. Redes, Hardware, Licencias" required>

            <label for="p_proveedor">Proveedor Certificado:</label>
            <select id="p_proveedor" name="id_proveedor" required>
                <?php foreach ($proveedores as $prov): ?>
                    <option value="<?php echo $prov['ID_Proveedor']; ?>">
                        <?php echo htmlspecialchars($prov['Nombre_Empresa']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div style="display: flex; gap: 10px; margin-top: 1.2rem;">
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalCrearProd()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: EDITAR PRODUCTO EXISTENTE -->
<!-- ========================================== -->
<div id="modalEditarProd" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 2000; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div style="background: var(--bg-surface); border: 1px solid var(--card-border); padding: 2.2rem; border-radius: 16px; width: 90%; max-width: 540px; box-shadow: var(--shadow-lg); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="color: var(--primary-green); font-size: 1.5rem; margin: 0;">Editar Producto <span id="ep_id_label"></span></h2>
            <button type="button" onclick="cerrarModalEditarProd()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>

        <form action="adminpanelTienda.php" method="POST" style="margin: 0; padding: 0; background: transparent; box-shadow: none; border: none;">
            <input type="hidden" name="action_product" value="1">
            <input type="hidden" id="ep_id_producto" name="id_producto">

            <label for="ep_nombre">Nombre del Producto:</label>
            <input type="text" id="ep_nombre" name="nombre" required>

            <label for="ep_descripcion">Descripción Completa:</label>
            <textarea id="ep_descripcion" name="descripcion" rows="3" required></textarea>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label for="ep_precio">Precio Base (USD):</label>
                    <input type="number" step="0.01" id="ep_precio" name="precio" required>
                </div>
                <div>
                    <label for="ep_stock">Stock Actual:</label>
                    <input type="number" id="ep_stock" name="stock" required>
                </div>
            </div>

            <label for="ep_categoria">Categoría:</label>
            <input type="text" id="ep_categoria" name="categoria" required>

            <label for="ep_proveedor">Proveedor Certificado:</label>
            <select id="ep_proveedor" name="id_proveedor" required>
                <?php foreach ($proveedores as $prov): ?>
                    <option value="<?php echo $prov['ID_Proveedor']; ?>">
                        <?php echo htmlspecialchars($prov['Nombre_Empresa']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div style="display: flex; gap: 10px; margin-top: 1.2rem;">
                <button type="submit" class="btn btn-primary">Actualizar Cambios</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalEditarProd()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- LÓGICA DE INTERACCIÓN JAVASCRIPT -->
<script>
function abrirModalCrearProd() {
    document.getElementById('modalCrearProd').style.display = 'flex';
}
function cerrarModalCrearProd() {
    document.getElementById('modalCrearProd').style.display = 'none';
}

function abrirModalEditarProd(prod) {
    document.getElementById('ep_id_producto').value = prod.ID_Producto;
    document.getElementById('ep_id_label').textContent = '#' + prod.ID_Producto;
    document.getElementById('ep_nombre').value = prod.Nombre || '';
    document.getElementById('ep_descripcion').value = prod.Descripcion || '';
    document.getElementById('ep_precio').value = prod.Precio_Base || 0;
    document.getElementById('ep_stock').value = prod.Stock_Actual || 0;
    document.getElementById('ep_categoria').value = prod.Categoria || 'General';
    if (prod.ID_Proveedor) {
        document.getElementById('ep_proveedor').value = prod.ID_Proveedor;
    }

    document.getElementById('modalEditarProd').style.display = 'flex';
}
function cerrarModalEditarProd() {
    document.getElementById('modalEditarProd').style.display = 'none';
}

// Cerrar modales al hacer clic fuera del contenido
window.onclick = function(event) {
    const modalC = document.getElementById('modalCrearProd');
    const modalE = document.getElementById('modalEditarProd');
    if (event.target === modalC) cerrarModalCrearProd();
    if (event.target === modalE) cerrarModalEditarProd();
}
</script>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
