<?php
$page_title = 'Tienda de Equipamiento - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);

// Consulta de productos con JOIN a proveedores
try {
    $stmt = $pdo->query("
        SELECT p.ID_Producto, p.Nombre, p.Descripcion, p.Precio_Base, p.Stock_Actual, p.Categoria, pr.Nombre_Empresa
        FROM producto p
        LEFT JOIN proveedor pr ON p.ID_Proveedor = pr.ID_Proveedor
        ORDER BY p.ID_Producto DESC
    ");
    $productos = $stmt->fetchAll();
} catch (Exception $e) {
    $productos = [];
}
?>

<div class="containerTienda">
    <div class="hero-section" style="padding-bottom: 1.5rem;">
        <h1>Tienda de <span>Equipamiento</span></h1>
        <p class="hero-subtitle">Componentes de hardware, networking y suministros tecnológicos para tu empresa.</p>
        
        <?php if ($es_admin): ?>
            <div style="margin-top: 1rem;">
                <a href="<?php echo VIEWS_PATH; ?>/adminpanel.php" class="btn btn-outline btn-sm">
                    ⚙ Gestionar Productos (Panel Admin)
                </a>
            </div>
        <?php endif; ?>
    </div>

    <section class="productos-section">
        <?php if (!empty($productos)): ?>
            <div class="productos-grid">
                <?php foreach ($productos as $p): ?>
                    <div class="producto-card">
                        <div class="producto-header">
                            <?php if (!empty($p['Categoria'])): ?>
                                <span class="producto-categoria-tag"><?= htmlspecialchars($p['Categoria']); ?></span>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($p['Nombre']); ?></h3>
                        </div>

                        <p class="producto-descripcion"><?= htmlspecialchars($p['Descripcion'] ?: 'Sin descripción detallada disponible.'); ?></p>

                        <div>
                            <div class="producto-meta">
                                <span class="producto-precio">$<?= number_format($p['Precio_Base'], 2); ?> <small style="font-size:0.75rem; font-weight:normal; color:var(--text-muted);">USD</small></span>
                                
                                <?php if ($p['Stock_Actual'] > 5): ?>
                                    <span class="producto-stock in-stock">● Stock: <?= (int)$p['Stock_Actual']; ?> unid.</span>
                                <?php elseif ($p['Stock_Actual'] > 0): ?>
                                    <span class="producto-stock in-stock" style="color:#f59e0b;">● Últimas <?= (int)$p['Stock_Actual']; ?> unid.</span>
                                <?php else: ?>
                                    <span class="producto-stock out-of-stock">● Sin Stock</span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($p['Nombre_Empresa'])): ?>
                                <div class="producto-proveedor">
                                    Proveedor: <strong><?= htmlspecialchars($p['Nombre_Empresa']); ?></strong>
                                </div>
                            <?php endif; ?>

                            <div class="producto-actions">
                                <?php if (isUserLoggedIn()): ?>
                                    <a href="<?php echo PUBLIC_PATH; ?>/contacto.php?asunto=Consulta+por+producto:+<?= urlencode($p['Nombre']); ?>" class="btn btn-primary btn-block btn-sm">
                                        Consultar / Adquirir
                                    </a>
                                <?php else: ?>
                                    <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-outline btn-block btn-sm">
                                        Iniciar Sesión para Comprar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert-box alert-success" style="justify-content: center; text-align: center; margin-top: 2rem;">
                <div>No hay productos disponibles en este momento. Vuelve a consultar pronto.</div>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once(INCLUDES_DIR . '/footer.php'); ?>
