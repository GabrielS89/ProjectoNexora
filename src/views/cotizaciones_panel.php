<?php
$require_admin = true;
$page_title    = 'Panel de Cotizaciones - Nexora';
require_once(__DIR__ . '/../../includes/config.php');

$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == 1;
$admin_cotizaciones_url = VIEWS_PATH . '/adminpanel.php?page=cotizaciones&partial=1';
$standalone_cotizaciones_url = 'cotizaciones_panel.php';
if (!$is_partial) {
    require_once(INCLUDES_DIR . '/header.php');
}

require_once(__DIR__ . '/../../database/database.php');
require_once(MODELS_PATH . '/plan.php');

// ── Feedback de sesión ────────────────────────────────────────────────────────
$success_msg = $_SESSION['cot_success'] ?? null;
$error_msg   = $_SESSION['cot_error']   ?? null;
unset($_SESSION['cot_success'], $_SESSION['cot_error']);

// ── Sub-vista activa ──────────────────────────────────────────────────────────
$vista  = $_GET['vista'] ?? 'lista';
$sol_id = (int)($_GET['sol_id'] ?? 0);

// Verificar si la tabla ya tiene columnas nuevas de instalación
$estado_instalacion_exists = false;
$monto_instalacion_exists = false;
try {
    $colCheck = $pdo->query("SHOW COLUMNS FROM solicitud_cotizacion LIKE 'Estado_Instalacion'");
    $estado_instalacion_exists = (bool)$colCheck->fetch();

    $montoCheck = $pdo->query("SHOW COLUMNS FROM solicitud_cotizacion LIKE 'Monto_Instalacion'");
    $monto_instalacion_exists = (bool)$montoCheck->fetch();
} catch (Exception $e) {
    $estado_instalacion_exists = false;
    $monto_instalacion_exists = false;
}

// ── Cargar solicitud seleccionada para el formulario de aprobación ─────────────
$solicitud_sel = null;
if ($vista === 'crear' && $sol_id > 0) {
    $stmtSol = $pdo->prepare("
        SELECT sc.*,
                u.Nombre AS Nombre_Usuario, u.Email, u.Telefono,
                e.Nombre_Empresa AS Razon_Social, e.RUT
        FROM solicitud_cotizacion sc
        JOIN usuario u ON u.ID_Usuario = sc.ID_Usuario
        LEFT JOIN empresa e ON e.ID_Usuario = sc.ID_Usuario
        WHERE sc.ID_Solicitud = ? AND sc.Estado = 'Pendiente'
    ");
    $stmtSol->execute([$sol_id]);
    $solicitud_sel = $stmtSol->fetch(PDO::FETCH_ASSOC);

    if (!$solicitud_sel) {
        $_SESSION['cot_error'] = 'Solicitud no encontrada o ya procesada.';
        header('Location: ' . ($is_partial ? $admin_cotizaciones_url : $standalone_cotizaciones_url));
        exit();
    }
}

// ── Listado de solicitudes ────────────────────────────────────────────────────
$filtro_estado  = $_GET['estado'] ?? 'Pendiente';
$estados_validos = ['Pendiente', 'Aprobada', 'Rechazada', 'Instalada', 'todas'];
if (!in_array($filtro_estado, $estados_validos)) $filtro_estado = 'Pendiente';

$PRECIOS = ['pc' => 800, 'impresoras' => 400, 'puntos_red' => 150, 'servidores' => 1200, 'wifi' => 250];

if ($filtro_estado === 'Instalada') {
    $where = "WHERE sc.Estado_Instalacion = 'Instalada'";
} else {
    $where = $filtro_estado !== 'todas' ? "WHERE sc.Estado = " . $pdo->quote($filtro_estado) : '';
}

$solicitudes = $pdo->query("
    SELECT sc.*,
            u.Nombre AS Nombre_Usuario, u.Email,
            e.Nombre_Empresa AS Razon_Social, e.RUT
    FROM solicitud_cotizacion sc
    JOIN usuario u ON u.ID_Usuario = sc.ID_Usuario
    LEFT JOIN empresa e ON e.ID_Usuario = sc.ID_Usuario
    $where
    ORDER BY sc.Fecha_Solicitud DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($success_msg)): ?>
    <div class="alerta alerta--exito">
        <?php echo $success_msg; ?>
    </div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alerta alerta--error">
        <?php echo htmlspecialchars($error_msg); ?>
    </div>
<?php endif; ?>

<?php if ($vista === 'crear' && $solicitud_sel):
    $s = $solicitud_sel;
    $sub_pc   = $s['Cant_PC']         * $PRECIOS['pc'];
    $sub_imp  = $s['Cant_Impresoras']  * $PRECIOS['impresoras'];
    $sub_red  = $s['Cant_Puntos_Red']  * $PRECIOS['puntos_red'];
    $sub_srv  = $s['Cant_Servidores']  * $PRECIOS['servidores'];
    $sub_wifi = $s['Cant_Wifi']        * $PRECIOS['wifi'];
    $total_estimado_cliente = $sub_pc + $sub_imp + $sub_red + $sub_srv + $sub_wifi;
    $estado_instalacion_actual = $s['Estado_Instalacion'] ?? 'Pendiente';
    $monto_instalacion_actual = isset($s['Monto_Instalacion']) ? (float)$s['Monto_Instalacion'] : 0.00;
?>
    <!-- ══════ VISTA: CREAR PLAN CUSTOM ══════ -->
    <div class="cot-header">
        <div>
            <h2>Aprobar Solicitud de Infraestructura</h2>
            <p class="cot-subtitle">
                Solicitud #<?php echo $s['ID_Solicitud']; ?> —
                <?php echo htmlspecialchars($s['Razon_Social'] ?? $s['Nombre_Usuario']); ?>
            </p>
        </div>
        <a class="btn btn-outline btn-sm partial-link"
            href="<?php echo $is_partial ? $admin_cotizaciones_url : $standalone_cotizaciones_url; ?>">
            ← Volver a la lista
        </a>
    </div>

    <!-- Datos de la empresa -->
    <div class="info-empresa-card">
        <div><span class="campo">Empresa:</span><br>
            <span class="valor"><?php echo htmlspecialchars($s['Razon_Social'] ?? '—'); ?></span></div>
        <div><span class="campo">RUT:</span><br>
            <span class="valor"><?php echo htmlspecialchars($s['RUT'] ?? '—'); ?></span></div>
        <div><span class="campo">Email:</span><br>
            <span class="valor"><?php echo htmlspecialchars($s['Email']); ?></span></div>
        <div><span class="campo">Teléfono:</span><br>
            <span class="valor"><?php echo htmlspecialchars($s['Telefono'] ?? '—'); ?></span></div>
        <div><span class="campo">Fecha solicitud:</span><br>
            <span class="valor"><?php echo date('d/m/Y H:i', strtotime($s['Fecha_Solicitud'])); ?></span></div>
    </div>

    <div class="info-empresa-card">
        <div><span class="campo">Estado instalación:</span><br>
            <span class="valor"><?php echo htmlspecialchars($estado_instalacion_actual); ?></span></div>
        <div><span class="campo">Monto estimado de instalación:</span><br>
            <span class="valor"><?php echo $monto_instalacion_actual > 0 ? '$' . number_format($monto_instalacion_actual, 2) : 'No registrado'; ?></span></div>
    </div>

    <!-- Lo que pidió el cliente -->
    <p class="sec-title">Solicitud del cliente</p>
    <p class="cot-texto-descripcion">
        <strong>Descripción:</strong>
        <?php echo htmlspecialchars($s['Descripcion']); ?>
    </p>

    <div class="desglose-grid">
        <?php
        $componentes = [
            ['PC / Estaciones',       $s['Cant_PC'],         $sub_pc,   $PRECIOS['pc']],
            ['Impresoras de Red',    $s['Cant_Impresoras'], $sub_imp,  $PRECIOS['impresoras']],
            ['Puntos de Red',        $s['Cant_Puntos_Red'], $sub_red,  $PRECIOS['puntos_red']],
            ['Servidores / Rack',     $s['Cant_Servidores'], $sub_srv,  $PRECIOS['servidores']],
            ['Wi-Fi Mesh Empresarial', $s['Cant_Wifi'],       $sub_wifi, $PRECIOS['wifi']],
        ];
        foreach ($componentes as [$label, $cant, $sub, $precio_u]):
        ?>
        <div class="desglose-item">
            <span class="d-label"><?php echo $label; ?></span>
            <span class="d-value"><?php echo $cant; ?> unid.</span>
            <span class="d-sub">
                × $<?php echo number_format($precio_u, 2); ?> = <strong>$<?php echo number_format($sub, 2); ?> USD</strong>
            </span>
        </div>
        <?php endforeach; ?>
        <div class="desglose-item desglose-item-total">
            <span class="d-label">Presupuesto estimado cliente</span>
            <span class="d-value d-value-total">$<?php echo number_format($total_estimado_cliente, 2); ?> USD</span>
        </div>
    </div>

    <?php if (!empty($s['Notas_Extra'])): ?>
    <p class="sec-title">Notas del cliente</p>
    <p class="cot-texto-notas"><?php echo htmlspecialchars($s['Notas_Extra']); ?></p>
    <?php endif; ?>

    <div class="info-empresa-card">
        <div><span class="campo">Tipo de ordenadores:</span><br><span class="valor"><?php echo htmlspecialchars($s['Tipo_Ordenador'] ?? 'Oficina'); ?></span></div>
        <div><span class="campo">Gama:</span><br><span class="valor"><?php echo htmlspecialchars($s['Gama_Equipo'] ?? 'Media'); ?></span></div>
    </div>

    <p class="sec-title">Decisión administrativa</p>
    <form class="form-custom-plan" action="<?php echo BASE_URL; ?>/src/controllers/gestionar_plan_custom.php?partial=<?php echo $is_partial ? '1' : '0'; ?>" method="POST">
        <input type="hidden" name="action" value="aprobar_cotizacion">
        <input type="hidden" name="sol_id" value="<?php echo $s['ID_Solicitud']; ?>">
        <input type="hidden" name="empresa_usuario_id" value="<?php echo $s['ID_Usuario']; ?>">

        <div class="form-group">
            <label for="comentario_admin">Comentario de aprobación (opcional)</label>
            <textarea id="comentario_admin" name="comentario_admin" rows="3" placeholder="Ej: Aprobado para compra de equipos y posterior instalación en sede principal..."></textarea>
        </div>

        <div class="plan-form-actions">
            <a href="<?php echo $is_partial ? $admin_cotizaciones_url : $standalone_cotizaciones_url; ?>" class="btn btn-outline partial-link">Cancelar</a>
            <button type="submit" class="btn btn-primary" <?php echo ($estado_instalacion_actual !== 'Aprobada' || $monto_instalacion_actual <= 0) ? 'disabled' : ''; ?>>Aprobar solicitud e ir a instalación</button>
        </div>
        <?php if ($estado_instalacion_actual !== 'Aprobada' || $monto_instalacion_actual <= 0): ?>
            <p class="cot-subtitle" style="color: #b91c1c; margin-top: 12px;">
                Para aprobar esta solicitud, la instalación debe estar en estado <strong>Aprobada</strong> y contar con un monto estimado válido.
            </p>
        <?php endif; ?>
    </form>

<?php else: ?>
    <!-- ══════ VISTA: LISTA DE SOLICITUDES ══════ -->
    <div class="cot-header">
        <h2>Solicitudes de Cotización</h2>
    </div>

    <?php $base_url = $is_partial ? $admin_cotizaciones_url : $standalone_cotizaciones_url; ?>
    <div class="cot-filter-bar">
        <a href="<?php echo $base_url . ($is_partial ? '&' : '?') . 'estado=Pendiente'; ?>" class="partial-link <?php echo $filtro_estado === 'Pendiente' ? 'active' : ''; ?>">Pendientes</a>
        <a href="<?php echo $base_url . ($is_partial ? '&' : '?') . 'estado=Aprobada'; ?>" class="partial-link <?php echo $filtro_estado === 'Aprobada' ? 'active' : ''; ?>">Aprobadas</a>
        <a href="<?php echo $base_url . ($is_partial ? '&' : '?') . 'estado=Rechazada'; ?>" class="partial-link <?php echo $filtro_estado === 'Rechazada' ? 'active' : ''; ?>">Rechazadas</a>
        <a href="<?php echo $base_url . ($is_partial ? '&' : '?') . 'estado=Instalada'; ?>" class="partial-link <?php echo $filtro_estado === 'Instalada' ? 'active' : ''; ?>">Instaladas</a>
        <a href="<?php echo $base_url . ($is_partial ? '&' : '?') . 'estado=todas'; ?>" class="partial-link <?php echo $filtro_estado === 'todas' ? 'active' : ''; ?>">Todas</a>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="empty-state">
            <p>No hay solicitudes<?php echo $filtro_estado !== 'todas' ? ' ' . strtolower($filtro_estado) . 's' : ''; ?>.</p>
            <p>Las solicitudes enviadas por las empresas aparecerán aquí.</p>
        </div>
    <?php else: ?>
        <table class="cot-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Empresa</th>
                    <th>RUT</th>
                    <th>Descripción</th>
                    <th>Componentes</th>
                    <th>Est. Cliente</th>
                    <th>Estado</th>
                    <th>Instalación</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($solicitudes as $sol):
                $total_cli = ($sol['Cant_PC'] * $PRECIOS['pc']) + ($sol['Cant_Impresoras'] * $PRECIOS['impresoras']) + ($sol['Cant_Puntos_Red'] * $PRECIOS['puntos_red']) + ($sol['Cant_Servidores'] * $PRECIOS['servidores']) + ($sol['Cant_Wifi'] * $PRECIOS['wifi']);
                $resumen = [];
                if ($sol['Cant_PC']) $resumen[] = $sol['Cant_PC'] . ' PC';
                if ($sol['Cant_Impresoras']) $resumen[] = $sol['Cant_Impresoras'] . ' Imp.';
                if ($sol['Cant_Puntos_Red']) $resumen[] = $sol['Cant_Puntos_Red'] . ' Red';
                if ($sol['Cant_Servidores']) $resumen[] = $sol['Cant_Servidores'] . ' Srv.';
                if ($sol['Cant_Wifi']) $resumen[] = $sol['Cant_Wifi'] . ' WiFi';
                $badge_class = match($sol['Estado']) {
                    'Pendiente' => 'badge-pendiente',
                    'Aprobada' => 'badge-aprobada',
                    'Rechazada' => 'badge-rechazada',
                    default => ''
                };
                $estado_instalacion = $sol['Estado_Instalacion'] ?? 'Pendiente';
                $monto_instalacion = isset($sol['Monto_Instalacion']) ? (float)$sol['Monto_Instalacion'] : 0.00;
            ?>
                <tr>
                    <td><strong>#<?php echo $sol['ID_Solicitud']; ?></strong></td>
                    <td>
                        <?php echo date('d/m/Y', strtotime($sol['Fecha_Solicitud'])); ?><br>
                        <span class="cot-hora"><?php echo date('H:i', strtotime($sol['Fecha_Solicitud'])); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($sol['Razon_Social'] ?? $sol['Nombre_Usuario']); ?></td>
                    <td><?php echo htmlspecialchars($sol['RUT'] ?? '—'); ?></td>
                    <td class="descripcion-celda"><?php echo htmlspecialchars(mb_strimwidth($sol['Descripcion'], 0, 60, '…')); ?></td>
                    <td class="componentes-celda"><?php echo empty($resumen) ? '—' : implode(', ', $resumen); ?></td>
                    <td class="total-celda">$<?php echo number_format($total_cli, 2); ?></td>
                    <td><span class="badge-estado <?php echo $badge_class; ?>"><?php echo $sol['Estado']; ?></span></td>
                    <td><span class="badge-estado badge-pendiente"><?php echo htmlspecialchars($estado_instalacion); ?></span></td>
                    <td><?php echo $monto_instalacion > 0 ? '$' . number_format($monto_instalacion, 2) : '—'; ?></td>
                    <td>
                        <?php if ($sol['Estado'] === 'Pendiente'): ?>
                            <div class="grupo-acciones">
                                <a class="btn btn-primary btn-sm partial-link" href="<?php echo $base_url . ($is_partial ? '&' : '?') . 'vista=crear&sol_id=' . $sol['ID_Solicitud']; ?>" <?php echo ($estado_instalacion !== 'Aprobada' || $monto_instalacion <= 0) ? 'onclick="return false;"' : ''; ?>>Aprobar</a>
                                <a class="btn btn-danger btn-sm partial-link" href="<?php echo BASE_URL; ?>/src/controllers/gestionar_plan_custom.php?action=rechazar&sol_id=<?php echo $sol['ID_Solicitud']; ?>&partial=<?php echo $is_partial ? '1' : '0'; ?>" data-confirm="¿Rechazar la solicitud #<?php echo $sol['ID_Solicitud']; ?>?">Rechazar</a>
                            </div>
                        <?php elseif ($sol['Estado'] === 'Aprobada'): ?>
                            <span class="estado-aprobada">En instalación</span>
                        <?php else: ?>
                            <span class="estado-rechazada">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php if (!$is_partial): require_once(INCLUDES_DIR . '/footer.php'); endif; ?>
