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

// Productos estáticos de fallback si la BD aún no tiene registros
if (empty($productos)) {
    $productos = [
        [
            'ID_Producto' => 1,
            'Nombre' => 'Switch Gigabit 24 Puertos',
            'Categoria' => 'Redes',
            'Precio_Base' => '250.00',
            'Stock_Actual' => 15,
            'Descripcion' => 'Switch gestionable de alta velocidad ideal para redes corporativas de alto rendimiento.',
            'ProveedorNombre' => 'TechSupply Uruguay'
        ],
        [
            'ID_Producto' => 2,
            'Nombre' => 'Access Point Wi-Fi 6 Enterprise',
            'Categoria' => 'Redes',
            'Precio_Base' => '120.00',
            'Stock_Actual' => 30,
            'Descripcion' => 'Punto de acceso dual-band de última generación para cobertura total sin interrupciones.',
            'ProveedorNombre' => 'TechSupply Uruguay'
        ],
        [
            'ID_Producto' => 3,
            'Nombre' => 'Servidor Rack 1U Xeon',
            'Categoria' => 'Hardware',
            'Precio_Base' => '1850.00',
            'Stock_Actual' => 5,
            'Descripcion' => 'Servidor potente Intel Xeon con 32GB RAM y almacenamiento SSD NVMe ultrarrápido.',
            'ProveedorNombre' => 'TechSupply Uruguay'
        ]
    ];
}
?>

<div class="hero-section">
    <h1>Tienda de Equipamiento &amp; Software</h1>
    <p class="hero-subtitle">Explora componentes de red, servidores y hardware certificado para potenciar la infraestructura de tu empresa.</p>
</div>

<div class="planes-grid">
    <?php foreach ($productos as $prod): ?>
        <article class="plan-card">
            <div class="plan-card-header">
                <span class="distintivo-tipo"><?php echo htmlspecialchars($prod['Categoria'] ?? 'General'); ?></span>
                <span class="texto-sutil">
                    Stock: <strong><?php echo (int)($prod['Stock_Actual'] ?? 0); ?></strong> u.
                </span>
            </div>

            <h3><?php echo htmlspecialchars($prod['Nombre']); ?></h3>

            <p class="texto-sutil"><?php echo htmlspecialchars($prod['Descripcion']); ?></p>

            <div class="plan-price-tag">
                <span class="price-desc">Precio USD</span>
                <strong>$<?php echo number_format((float)$prod['Precio_Base'], 2); ?></strong>
            </div>

            <div class="plan-card-actions">
                <?php if (isUserLoggedIn()): ?>
                    <button type="button" class="btn btn-primary" onclick="alert('¡Gracias por tu interés! El producto fue añadido a tu pedido.');">
                        Comprar
                    </button>
                <?php else: ?>
                    <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-primary">
                        Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
