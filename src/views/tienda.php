<?php
$page_title = 'Tienda de Productos - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Obtener productos en tiempo real desde la base de datos MySQL
$productos = [];
try {
    if (!isset($pdo)) {
        require_once(BASE_DIR . '/database/database.php');
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

<div style="text-align: center; margin-bottom: 3rem;">
    <h1 style="font-size: 2.4rem; margin-bottom: 0.5rem;">Tienda de Equipamiento & Software</h1>
    <p style="color: var(--text-muted); max-width: 700px; margin: 0 auto;">Explora componentes de red, servidores y hardware certificado para potenciar la infraestructura de tu empresa.</p>
</div>

<!-- GRILLA DE PRODUCTOS -->
<div class="planes-grid" style="gap: 2rem; margin-bottom: 4rem;">
    <?php foreach ($productos as $prod): ?>
        <div class="plan-card" style="background: var(--card-bg); text-align: left; display: flex; flex-direction: column;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <span style="background: var(--light-green-tint); color: var(--primary-green); padding: 4px 12px; border-radius: 20px; font-size: 0.82rem; font-weight: 700; text-transform: uppercase;">
                    <?php echo htmlspecialchars($prod['Categoria'] ?? 'General'); ?>
                </span>
                <span style="color: var(--text-muted); font-size: 0.85rem;">
                    Stock: <strong><?php echo (int)($prod['Stock_Actual'] ?? 0); ?></strong> u.
                </span>
            </div>

            <h3 style="font-size: 1.4rem; margin-bottom: 0.6rem; color: var(--text-heading);">
                <?php echo htmlspecialchars($prod['Nombre']); ?>
            </h3>

            <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5; flex-grow: 1; margin-bottom: 1.5rem;">
                <?php echo htmlspecialchars($prod['Descripcion']); ?>
            </p>

            <div style="border-top: 1px solid var(--card-border); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                <div>
                    <span style="font-size: 0.8rem; color: var(--text-muted); display: block;">Precio USD</span>
                    <strong style="font-size: 1.6rem; color: var(--primary-green); font-weight: 800;">
                        $<?php echo number_format((float)$prod['Precio_Base'], 2); ?>
                    </strong>
                </div>

                <?php if ($isLoggedIn): ?>
                    <button type="button" class="btn btn-primary" onclick="alert('¡Gracias por tu interés! El producto fue añadido a tu pedido.');">
                        Comprar
                    </button>
                <?php else: ?>
                    <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.6rem 1rem;">
                        Iniciar Sesión
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
