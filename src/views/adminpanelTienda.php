<?php
$require_admin = true;
$page_title = 'Administración de Tienda - Nexora';
require_once(__DIR__ . '/../../includes/config.php');

$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == 1;
$admin_tienda_url = 'adminpanel.php?page=tienda&partial=1';
$admin_tienda_listing = $is_partial ? $admin_tienda_url : 'adminpanelTienda.php';
if (!$is_partial) {
    require_once(INCLUDES_DIR . '/header.php');
}

// Requerir conexión a la base de datos
require_once(__DIR__ . '/../../database/database.php');

$error_message = '';
$msg_message = '';

function redirectToTiendaPanel($is_partial, $extra = '') {
    $target = $is_partial ? 'adminpanel.php?page=tienda&partial=1' : 'adminpanelTienda.php';
    if ($extra !== '') {
        $target .= (strpos($target, '?') === false ? '?' : '&') . $extra;
    }
    if ($is_partial) {
        echo '<script>window.location.href = "' . $target . '";</script>';
        exit;
    }
    header('Location: ' . $target);
    exit;
}

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
                redirectToTiendaPanel($is_partial, 'msg=producto_actualizado');
            } else {
                // Crear nuevo producto
                $stmt = $pdo->prepare("INSERT INTO producto 
                    (Nombre, Descripcion, Precio_Base, Stock_Actual, Categoria, ID_Proveedor) 
                    VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$nombre, $descripcion, $precio, $stock, $categoria, $proveedor]);
                redirectToTiendaPanel($is_partial, 'msg=producto_creado');
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
            redirectToTiendaPanel($is_partial, 'msg=producto_eliminado');
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
<div class="encabezado-admin">
    <div>
        <h1 class="titulo-admin">Gestión de Productos (Tienda)</h1>
        <p class="texto-sutil">Agrega, edita y administra los artículos del catálogo público.</p>
    </div>
    <div class="acciones-admin">
        <button type="button" class="btn btn-primary" onclick="abrirModalCrearProd()">Agregar Producto</button>
        <a href="<?php echo VIEWS_PATH; ?>/tienda.php" class="btn btn-outline" target="_blank">Ver Tienda</a>
    </div>
</div>

<!-- MUESTRA DE ALERTAS Y NOTIFICACIONES -->
<?php if (isset($_GET['msg'])): ?>
    <?php 
        $msgTexto = 'Operación realizada correctamente.';
        if ($_GET['msg'] === 'producto_creado') $msgTexto = '¡Producto agregado al catálogo exitosamente!';
        if ($_GET['msg'] === 'producto_actualizado') $msgTexto = '¡Datos del producto actualizados correctamente!';
        if ($_GET['msg'] === 'producto_eliminado') $msgTexto = '¡Producto eliminado del catálogo!';
    ?>
    <div class="alerta alerta--exito">
        <?php echo $msgTexto; ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alerta alerta--error">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<!-- TARJETAS ESTADÍSTICAS TIENDA -->
<div class="grid-estadisticas">
    <div class="tarjeta-estadistica">
        <span class="texto-sutil small-text">Total de Productos</span>
        <h2 class="valor-estadistica"><?php echo $totalProductos; ?></h2>
    </div>

    <div class="tarjeta-estadistica">
        <span class="texto-sutil small-text">Categorías Activas</span>
        <h2 class="valor-estadistica"><?php echo $categoriasUnicas; ?></h2>
    </div>

    <div class="tarjeta-estadistica">
        <span class="texto-sutil small-text">Unidades en Stock</span>
        <h2 class="valor-estadistica"><?php echo $totalStock; ?></h2>
    </div>
</div>

<!-- TABLA DE PRODUCTOS DE LA TIENDA -->
<div class="tarjeta-estadistica tarjeta--con-padding">
    <h2 class="tarjeta-titulo">Catálogo de Productos</h2>

    <table class="tabla">
        <thead>
            <tr>
                <th>ID</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Precio USD</th>
                <th>Stock</th>
                <th>Proveedor</th>
                <th class="texto-centro">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($productos)): ?>
                <?php foreach ($productos as $prod): ?>
                    <tr>
                        <td class="celda-id">#<?php echo $prod['ID_Producto']; ?></td>
                        <td>
                            <strong class="celda-fuerte"><?php echo htmlspecialchars($prod['Nombre']); ?></strong>
                            <p class="prod-desc"><?php echo htmlspecialchars($prod['Descripcion']); ?></p>
                        </td>
                        <td>
                            <span class="distintivo-tipo"><?php echo htmlspecialchars($prod['Categoria'] ?? 'General'); ?></span>
                        </td>
                        <td class="celda-fuerte">$<?php echo number_format((float)$prod['Precio_Base'], 2); ?></td>
                        <td class="celda-fuerte"><?php echo (int)$prod['Stock_Actual']; ?> u.</td>
                        <td class="texto-sutil"><?php echo htmlspecialchars($prod['ProveedorNombre'] ?? 'TechSupply Uruguay'); ?></td>
                        <td class="texto-centro">
                            <div class="grupo-acciones">
                                <button type="button" class="btn btn-outline btn--pequeno" onclick='abrirModalEditarProd(<?php echo json_encode($prod, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>Editar</button>
                                <a href="<?php echo $admin_tienda_url . ($is_partial ? '&' : '?') . 'action=delete&id=' . $prod['ID_Producto']; ?>" class="btn btn-danger btn--pequeno" onclick="return confirm('¿Estás seguro de eliminar el producto <?php echo htmlspecialchars($prod['Nombre']); ?>?');">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="fila-vacia">No hay productos registrados en el catálogo de la tienda.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ========================================== -->
<!-- MODAL 1: CREAR NUEVO PRODUCTO -->
<!-- ========================================== -->
<div id="modalCrearProd" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Agregar Producto a la Tienda</h2>
            <button type="button" class="modal-close" onclick="cerrarModalCrearProd()">&times;</button>
        </div>

        <form action="<?php echo $admin_tienda_listing; ?>" method="POST" class="modal-form">
            <input type="hidden" name="action_product" value="1">
            <input type="hidden" name="id_producto" value="0">

            <label for="p_nombre">Nombre del Producto:</label>
            <input type="text" id="p_nombre" name="nombre" placeholder="Ej. Servidor Rack 2U Intel Xeon" required>

            <label for="p_descripcion">Descripción Completa:</label>
            <textarea id="p_descripcion" name="descripcion" rows="3" placeholder="Detalles de hardware, especificaciones y garantía..." required></textarea>

            <div>
                <label for="p_precio">Precio Base (USD):</label>
                <input type="number" step="0.01" id="p_precio" name="precio" placeholder="250.00" required>
            </div>
            <div>
                <label for="p_stock">Stock Inicial:</label>
                <input type="number" id="p_stock" name="stock" placeholder="10" required>
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

            <div class="modal-actions">
                <button type="submit" class="btn btn-primary">Guardar Producto</button>
                <button type="button" class="btn btn-outline" onclick="cerrarModalCrearProd()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: EDITAR PRODUCTO EXISTENTE -->
<!-- ========================================== -->
<div id="modalEditarProd" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Editar Producto <span id="ep_id_label"></span></h2>
            <button type="button" class="modal-close" onclick="cerrarModalEditarProd()">&times;</button>
        </div>

        <form action="<?php echo $admin_tienda_listing; ?>" method="POST" class="modal-form">
            <input type="hidden" name="action_product" value="1">
            <input type="hidden" id="ep_id_producto" name="id_producto">

            <label for="ep_nombre">Nombre del Producto:</label>
            <input type="text" id="ep_nombre" name="nombre" required>

            <label for="ep_descripcion">Descripción Completa:</label>
            <textarea id="ep_descripcion" name="descripcion" rows="3" required></textarea>

            <div>
                <label for="ep_precio">Precio Base (USD):</label>
                <input type="number" step="0.01" id="ep_precio" name="precio" required>
            </div>
            <div>
                <label for="ep_stock">Stock Actual:</label>
                <input type="number" id="ep_stock" name="stock" required>
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

            <div class="modal-actions">
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

<?php if (!$is_partial): ?>
    <?php require_once(INCLUDES_DIR . '/footer.php'); ?>
<?php endif; ?>
