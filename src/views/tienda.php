<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);

// Conexión a la base de datos
require_once(__DIR__ . '/../../database/database.php');

// Consulta de productos con JOIN a proveedores
$stmt = $pdo->query("
    SELECT p.ID_Producto, p.Nombre, p.Descripcion, p.Precio_Base, p.Stock_Actual, pr.Nombre_Empresa
    FROM producto p
    LEFT JOIN proveedor pr ON p.ID_Proveedor = pr.ID_Proveedor
    ORDER BY p.ID_Producto DESC
");
$productos = $stmt->fetchAll();
?>

<head></head>

<body>

<section class="contaierTienda">

<div>
    <h1>Bienvenido a la Tienda</h1>
    <p>Explora nuestros productos y ofertas exclusivas.</p>
</div>

<section class="productos">
    <h2>Productos Destacados</h2>
    <div class="producto-lista">
        <?php if (count($productos) > 0): ?>
            <?php foreach ($productos as $p): ?>
                <div class="producto-item">
                    <h3><?= htmlspecialchars($p['Nombre']); ?></h3>
                    <p><?= htmlspecialchars($p['Descripcion']); ?></p>
                    <p><strong>Precio:</strong> $<?= number_format($p['Precio_Base'], 2); ?></p>
                    <p><strong>Stock:</strong> <?= $p['Stock_Actual']; ?></p>
                    <p><strong>Proveedor:</strong> <?= htmlspecialchars($p['Nombre_Empresa']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay productos disponibles en este momento.</p>
        <?php endif; ?>
    </div>
</section>

</body>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
