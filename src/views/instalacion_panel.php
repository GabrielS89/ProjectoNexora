<?php
$require_admin = true;
$page_title = 'Departamento de Instalación - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(__DIR__ . '/../../database/database.php');

$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == 1;
$base_url = VIEWS_PATH . '/adminpanel.php?page=instalacion&partial=1';

$vista = $_GET['vista'] ?? 'pendientes';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'actualizar_estado_instalacion') {
    $sol_id = (int)($_POST['sol_id'] ?? 0);
    $estado = $_POST['estado_instalacion'] ?? 'Pendiente';
    $monto = isset($_POST['monto_instalacion']) ? (float)str_replace(',', '.', trim($_POST['monto_instalacion'])) : 0.0;
    $valid_states = ['Pendiente', 'Aprobada', 'Rechazada', 'Instalada'];

    if ($sol_id > 0 && in_array($estado, $valid_states, true)) {
        $stmt = $pdo->prepare("UPDATE solicitud_cotizacion SET Estado_Instalacion = ?, Monto_Instalacion = ? WHERE ID_Solicitud = ?");
        $stmt->execute([$estado, $monto, $sol_id]);
        $_SESSION['cot_success'] = 'Estado de instalación y monto actualizado correctamente.';
    } else {
        $_SESSION['cot_error'] = 'No se pudo actualizar el estado de instalación.';
    }

    header('Location: ' . $base_url . '&vista=' . urlencode($vista));
    exit();
}

$success_msg = $_SESSION['cot_success'] ?? null;
$error_msg = $_SESSION['cot_error'] ?? null;
unset($_SESSION['cot_success'], $_SESSION['cot_error']);

$solicitudes = [];
try {
    $where = "WHERE sc.Estado IN ('Aprobada', 'Pendiente')";
    if ($vista === 'instaladas') {
        $where = "WHERE sc.Estado_Instalacion = 'Instalada'";
    }

    $stmt = $pdo->prepare("SELECT sc.*, u.Nombre AS Nombre_Usuario, u.Email,
        e.Nombre_Empresa AS Razon_Social, e.RUT
        FROM solicitud_cotizacion sc
        JOIN usuario u ON u.ID_Usuario = sc.ID_Usuario
        LEFT JOIN empresa e ON e.ID_Usuario = sc.ID_Usuario
        $where
        ORDER BY sc.Fecha_Solicitud DESC");
    $stmt->execute();
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $solicitudes = [];
}
?>

<?php if (!empty($success_msg)): ?>
    <div class="alerta alerta--exito"><?php echo htmlspecialchars($success_msg); ?></div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alerta alerta--error"><?php echo htmlspecialchars($error_msg); ?></div>
<?php endif; ?>

<div class="cot-header">
    <h2>Solicitudes de Instalación</h2>
</div>

<div class="cot-filter-bar">
    <a href="<?php echo $base_url . '&vista=pendientes'; ?>" class="partial-link <?php echo $vista === 'pendientes' ? 'active' : ''; ?>">Pendientes</a>
    <a href="<?php echo $base_url . '&vista=instaladas'; ?>" class="partial-link <?php echo $vista === 'instaladas' ? 'active' : ''; ?>">Instaladas</a>
</div>

<?php if (empty($solicitudes)): ?>
    <div class="empty-state">
        <p><?php echo $vista === 'instaladas' ? 'No hay solicitudes instaladas.' : 'No hay solicitudes aprobadas pendientes de instalación.'; ?></p>
    </div>
<?php else: ?>
    <table class="cot-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Empresa</th>
                <th>Tipo</th>
                <th>Gama</th>
                <th>Descripción</th>
                <th>Estado</th>
                <th>Monto</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($solicitudes as $sol): ?>
                <?php $estado_instalacion_actual = $sol['Estado_Instalacion'] ?? 'Pendiente'; ?>
                <tr>
                    <td><strong>#<?php echo $sol['ID_Solicitud']; ?></strong></td>
                    <td>
                        <?php echo htmlspecialchars($sol['Razon_Social'] ?? $sol['Nombre_Usuario']); ?><br>
                        <small><?php echo htmlspecialchars($sol['Email']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($sol['Tipo_Ordenador'] ?? 'Oficina'); ?></td>
                    <td><?php echo htmlspecialchars($sol['Gama_Equipo'] ?? 'Media'); ?></td>
                    <td><?php echo htmlspecialchars(mb_strimwidth($sol['Descripcion'], 0, 80, '…')); ?></td>
                    <td>
                        <span class="badge-estado badge-aprobada"><?php echo htmlspecialchars($estado_instalacion_actual); ?></span>
                    </td>
                    <td>
                        <?php echo isset($sol['Monto_Instalacion']) && (float)$sol['Monto_Instalacion'] > 0 ? '$' . number_format((float)$sol['Monto_Instalacion'], 2) : '—'; ?>
                    </td>
                    <td>
                        <?php if ($vista === 'instaladas' || $estado_instalacion_actual === 'Instalada'): ?>
                            <div class="estado-lectura">Solo lectura</div>
                        <?php else: ?>
                            <form action="<?php echo BASE_URL; ?>/src/views/adminpanel.php?page=instalacion&partial=1&vista=<?php echo urlencode($vista); ?>" method="POST" class="inline-form">
                                <input type="hidden" name="action" value="actualizar_estado_instalacion">
                                <input type="hidden" name="sol_id" value="<?php echo $sol['ID_Solicitud']; ?>">
                                <select name="estado_instalacion" class="custom-select small-select">
                                    <option value="Pendiente" <?php echo (($sol['Estado_Instalacion'] ?? 'Pendiente') === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                    <option value="Aprobada" <?php echo (($sol['Estado_Instalacion'] ?? 'Pendiente') === 'Aprobada') ? 'selected' : ''; ?>>Aprobada</option>
                                    <option value="Rechazada" <?php echo (($sol['Estado_Instalacion'] ?? 'Pendiente') === 'Rechazada') ? 'selected' : ''; ?>>Rechazada</option>
                                    <option value="Instalada" <?php echo (($sol['Estado_Instalacion'] ?? 'Pendiente') === 'Instalada') ? 'selected' : ''; ?>>Instalada</option>
                                </select>
                                <input type="number" step="0.01" min="0" name="monto_instalacion" value="<?php echo isset($sol['Monto_Instalacion']) ? (float)$sol['Monto_Instalacion'] : 0; ?>" placeholder="Monto" class="custom-input small-input" style="width: 110px; margin-top: 6px;">
                                <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
