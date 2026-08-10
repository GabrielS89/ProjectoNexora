<?php
$require_admin = true;
$page_title = 'Panel de Administración - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');

// Consultar lista de usuarios si PDO está disponible
$usuarios = [];
try {
    if (file_exists(BASE_DIR . '/database/database.php')) {
        require_once(BASE_DIR . '/database/database.php');
        if (isset($pdo)) {
            $stmt = $pdo->query("SELECT u.ID_Usuario, u.Email, u.Nombre, u.Telefono, u.Tipo_Persona, r.TipoRol FROM usuario u LEFT JOIN rol r ON u.ID_Rol = r.ID_Rol ORDER BY u.ID_Usuario ASC");
            $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (Exception $e) {
    $usuarios = [];
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 2.2rem; margin-bottom: 0.2rem;">Panel de Administración</h1>
        <p style="color: var(--text-muted);">Gestión de cuentas de usuario, roles y permisos de acceso.</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="<?php echo VIEWS_PATH; ?>/dashboard.php" class="btn btn-outline">Ir a mi Panel</a>
        <a href="<?php echo PUBLIC_PATH; ?>/logout.php" class="btn btn-danger">Cerrar Sesión</a>
    </div>
</div>

<!-- TARJETAS ESTADÍSTICAS DEL SISTEMA -->
<div class="features-grid" style="margin-top: 0; margin-bottom: 2.5rem;">
    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Total Registrados</span>
        <h2 style="font-size: 2.2rem; color: var(--primary-green); margin-top: 0.3rem;">
            <?php echo count($usuarios); ?>
        </h2>
    </div>

    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Nivel de Acceso</span>
        <h2 style="font-size: 1.5rem; color: var(--primary-green); margin-top: 0.5rem;">
            <?php echo ($_SESSION['usuario_rol'] == 3) ? 'Super Administrador' : 'Administrador'; ?>
        </h2>
    </div>

    <div class="feature-card" style="background: var(--card-bg);">
        <span style="color: var(--text-muted); font-size: 0.9rem;">Usuario Activo</span>
        <p style="font-size: 1.05rem; font-weight: 600; color: var(--text-heading); margin-top: 0.5rem; word-break: break-all;">
            <?php echo htmlspecialchars($_SESSION['email']); ?>
        </p>
    </div>
</div>

<!-- TABLA DE USUARIOS DEL SISTEMA -->
<div class="feature-card" style="background: var(--card-bg); padding: 1.8rem; overflow-x: auto;">
    <h2 style="margin-bottom: 1.2rem; font-size: 1.4rem; color: var(--primary-green);">Usuarios Registrados</h2>

    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
        <thead>
            <tr style="border-bottom: 2px solid var(--card-border); color: var(--text-heading);">
                <th style="padding: 12px 10px;">ID</th>
                <th style="padding: 12px 10px;">Nombre</th>
                <th style="padding: 12px 10px;">Email</th>
                <th style="padding: 12px 10px;">Teléfono</th>
                <th style="padding: 12px 10px;">Tipo Persona</th>
                <th style="padding: 12px 10px;">Rol</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($usuarios)): ?>
                <?php foreach ($usuarios as $usr): ?>
                    <tr style="border-bottom: 1px solid rgba(13, 125, 83, 0.1);">
                        <td style="padding: 12px 10px; font-weight: bold; color: var(--primary-green);">#<?php echo $usr['ID_Usuario']; ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($usr['Nombre']); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($usr['Email']); ?></td>
                        <td style="padding: 12px 10px;"><?php echo htmlspecialchars($usr['Telefono'] ?? '-'); ?></td>
                        <td style="padding: 12px 10px;">
                            <span style="background: var(--light-green-tint); color: var(--primary-green); padding: 3px 10px; border-radius: 12px; font-size: 0.82rem; font-weight: 600;">
                                <?php echo ucfirst($usr['Tipo_Persona']); ?>
                            </span>
                        </td>
                        <td style="padding: 12px 10px; font-weight: 600; color: var(--text-heading);">
                            <?php echo htmlspecialchars($usr['TipoRol'] ?? 'Usuario'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="padding: 20px; text-align: center; color: var(--text-muted);">No se encontraron usuarios en la base de datos o ejecuta <a href="../database/instalar_bd.php">instalar_bd.php</a>.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>