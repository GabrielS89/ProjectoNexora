<?php
$page_title = 'Tienda de Productos - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Obtener productos en tiempo real desde la base de datos MySQL
$productos = [];
try {
    if (!isset($pdo)) {
        require_once(BASE_URL . '/database/database.php');
    }
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT p.*, pr.Nombre_Empresa as ProveedorNombre
                            FROM producto p
                            LEFT JOIN proveedor pr ON p.ID_Proveedor = pr.ID_Proveedor
                            ORDER BY p.ID_Producto DESC");
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $productos = [];
}
?>

<div class="tienda-header">
    <h1 class="tienda-title">Tienda de Equipamiento & Software</h1>
    <p class="tienda-subtitle">Explora componentes de red, servidores y hardware certificado para potenciar la infraestructura de tu empresa.</p>
</div>

<!-- GRILLA DE PRODUCTOS -->
<div class="planes-grid tienda-grid">
    <?php foreach ($productos as $prod): ?>
        <div class="plan-card tienda-card">
            <div class="tienda-card-header">
                <span class="product-tag">
                    <?php echo htmlspecialchars($prod['Categoria'] ?? 'General'); ?>
                </span>
                <span class="product-stock">
                    Stock: <strong><?php echo (int)($prod['Stock_Actual'] ?? 0); ?></strong> u.
                </span>
            </div>

            <h3 class="product-title">
                <?php echo htmlspecialchars($prod['Nombre']); ?>
            </h3>

            <p class="product-desc">
                <?php echo htmlspecialchars($prod['Descripcion']); ?>
            </p>

            <div class="product-footer">
                <div>
                    <span class="product-price-label">Precio USD</span>
                    <strong class="product-price-amount">
                        $<?php echo number_format((float)$prod['Precio_Base'], 2); ?>
                    </strong>
                </div>

                <?php if ($isLoggedIn): ?>
                    <button type="button" class="btn btn-primary" onclick="alert('¡Gracias por tu interés! El producto fue añadido a tu pedido.');">
                        Comprar
                    </button>
                <?php else: ?>
                    <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-primary btn-sm-login">
                        Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
