<?php
$require_admin = true;
$page_title    = 'Panel de Cotizaciones - Nexora';
require_once(__DIR__ . '/../../includes/config.php');

$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == 1;
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
$vista       = $_GET['vista']  ?? 'lista';
$sol_id      = (int)($_GET['sol_id'] ?? 0);

// ── Cargar solicitud seleccionada para el formulario "Crear Plan" ─────────────
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
        header('Location: cotizaciones_panel.php?partial=' . ($is_partial ? '1' : '0'));
        exit();
    }
}

// ── Listado de solicitudes ────────────────────────────────────────────────────
$filtro_estado  = $_GET['estado'] ?? 'Pendiente';
$estados_validos = ['Pendiente', 'Aprobada', 'Rechazada', 'todas'];
if (!in_array($filtro_estado, $estados_validos)) $filtro_estado = 'Pendiente';

// Calcular total estimado en PHP con los precios base
$PRECIOS = ['pc' => 800, 'impresoras' => 400, 'puntos_red' => 150, 'servidores' => 1200, 'wifi' => 250];

$where   = $filtro_estado !== 'todas' ? "WHERE sc.Estado = " . $pdo->quote($filtro_estado) : '';
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

<style>
.cot-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.5rem; }
.cot-filter-bar { display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:1.5rem; }
.cot-filter-bar a {
    padding:0.4rem 1rem; border-radius:20px; font-size:0.85rem; font-weight:600;
    text-decoration:none; border:1px solid var(--card-border); color:var(--text-muted); transition:all 0.2s;
}
.cot-filter-bar a.active, .cot-filter-bar a:hover {
    border-color:var(--primary-green); color:#fff; background:rgba(17,138,94,0.2);
}
.cot-table { width:100%; border-collapse:collapse; font-size:0.9rem; }
.cot-table th {
    background:rgba(17,138,94,0.15); color:var(--primary-green-hover);
    padding:0.75rem 1rem; text-align:left; font-size:0.82rem;
    text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--card-border);
}
.cot-table td { padding:0.75rem 1rem; border-bottom:1px solid rgba(255,255,255,0.05); vertical-align:top; color:var(--text-main); }
.cot-table tr:hover td { background:rgba(17,138,94,0.05); }
.badge-estado { padding:0.2rem 0.65rem; border-radius:12px; font-size:0.78rem; font-weight:700; }
.badge-pendiente { background:rgba(234,179,8,0.18);  color:#facc15; border:1px solid rgba(234,179,8,0.4); }
.badge-aprobada  { background:rgba(17,138,94,0.18);  color:#4ade80; border:1px solid rgba(17,138,94,0.4); }
.badge-rechazada { background:rgba(220,53,69,0.18);  color:#f87171; border:1px solid rgba(220,53,69,0.4); }

/* Desglose de componentes en la solicitud */
.desglose-grid {
    display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr));
    gap:0.6rem; margin-bottom:1.5rem;
}
.desglose-item {
    background:rgba(13,25,19,0.7); border:1px solid var(--card-border);
    border-radius:8px; padding:0.65rem 1rem;
}
.desglose-item .d-label { font-size:0.78rem; color:var(--text-muted); display:block; margin-bottom:0.2rem; }
.desglose-item .d-value { font-size:1rem; font-weight:700; color:var(--text-main); }
.desglose-item .d-sub   { font-size:0.78rem; color:var(--primary-green-hover); margin-top:0.1rem; }

.info-empresa-card {
    background:rgba(13,25,19,0.6); border:1px solid var(--card-border);
    border-left:3px solid var(--primary-green); border-radius:8px;
    padding:0.9rem 1.2rem; margin-bottom:1.5rem; font-size:0.88rem;
    display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:0.5rem 1.5rem;
}
.info-empresa-card .campo { color:var(--text-muted); }
.info-empresa-card .valor { color:var(--text-main); font-weight:600; }

.form-custom-plan {
    background:var(--card-bg); border:1px solid var(--card-border);
    border-radius:12px; padding:2rem; max-width:720px; margin:0;
}
.form-group { display:flex; flex-direction:column; gap:0.3rem; margin-bottom:1rem; }
.form-group label { font-size:0.88rem; font-weight:600; color:var(--text-main); }
.form-group input,
.form-group textarea {
    background:rgba(0,0,0,0.5); border:1px solid var(--card-border);
    border-radius:6px; color:var(--text-main); padding:0.6rem 0.85rem;
    font-family:inherit; font-size:0.92rem; transition:border-color 0.25s;
    width:100%; box-sizing:border-box;
}
.form-group input:focus, .form-group textarea:focus {
    outline:none; border-color:var(--primary-green); box-shadow:0 0 8px rgba(17,138,94,0.35);
}
.comp-grid-empleado {
    display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr));
    gap:0.75rem; margin-bottom:1.25rem;
}
.comp-row {
    background:rgba(13,25,19,0.7); border:1px solid var(--card-border);
    border-radius:8px; padding:0.75rem 1rem;
    display:flex; flex-direction:column; gap:0.3rem;
}
.comp-label  { font-size:0.82rem; font-weight:600; color:var(--text-main); }
.comp-precio { font-size:0.75rem; color:var(--primary-green-hover); }
.comp-input-wrap { display:flex; align-items:center; gap:0.4rem; margin-top:0.2rem; }
.comp-input-wrap input[type="number"] { width:70px; padding:0.4rem 0.5rem; text-align:center; margin-bottom:0; }
.total-preview {
    background:linear-gradient(135deg, rgba(17,138,94,0.2), rgba(7,11,9,0.8));
    border:1px solid var(--primary-green-hover); border-radius:10px;
    padding:1rem 1.4rem; display:flex; justify-content:space-between;
    align-items:center; margin-bottom:1.5rem;
}
.total-preview .lbl { font-size:0.95rem; font-weight:600; }
.total-preview .amt { font-size:1.5rem; font-weight:800; color:#20e39b; }
.plan-form-actions { display:flex; gap:0.75rem; justify-content:flex-end; margin-top:0.5rem; }
.empty-state { text-align:center; padding:3rem 1rem; color:var(--text-muted); }
.empty-state p { margin-top:0.5rem; font-size:0.92rem; }
.sec-title {
    font-size:0.82rem; font-weight:700; text-transform:uppercase;
    letter-spacing:0.5px; color:var(--primary-green-hover); margin-bottom:0.65rem; margin-top:0.25rem;
}
</style>

<?php if (!empty($success_msg)): ?>
    <div class="alert-box alert-success" style="margin-bottom:1.5rem;">
        <span class="alert-tag">OK</span><div><?php echo $success_msg; ?></div>
    </div>
<?php endif; ?>
<?php if (!empty($error_msg)): ?>
    <div class="alert-box alert-danger" style="margin-bottom:1.5rem;">
        <span class="alert-tag">ERROR</span><div><?php echo htmlspecialchars($error_msg); ?></div>
    </div>
<?php endif; ?>

<?php if ($vista === 'crear' && $solicitud_sel):
    // Calcular totales parciales del cliente para mostrar
    $s = $solicitud_sel;
    $sub_pc   = $s['Cant_PC']         * $PRECIOS['pc'];
    $sub_imp  = $s['Cant_Impresoras']  * $PRECIOS['impresoras'];
    $sub_red  = $s['Cant_Puntos_Red']  * $PRECIOS['puntos_red'];
    $sub_srv  = $s['Cant_Servidores']  * $PRECIOS['servidores'];
    $sub_wifi = $s['Cant_Wifi']        * $PRECIOS['wifi'];
    $total_estimado_cliente = $sub_pc + $sub_imp + $sub_red + $sub_srv + $sub_wifi;
?>
    <!-- ══════ VISTA: CREAR PLAN CUSTOM ══════ -->
    <div class="cot-header">
        <div>
            <h2>Crear Plan Custom</h2>
            <p style="color:var(--text-muted);font-size:0.9rem;margin-top:0.25rem;">
                Solicitud #<?php echo $s['ID_Solicitud']; ?> —
                <?php echo htmlspecialchars($s['Razon_Social'] ?? $s['Nombre_Usuario']); ?>
            </p>
        </div>
        <a class="btn btn-outline btn-sm partial-link"
           href="cotizaciones_panel.php?partial=<?php echo $is_partial ? '1' : '0'; ?>">
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

    <!-- Lo que pidió el cliente -->
    <p class="sec-title">Solicitud del cliente</p>
    <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:0.75rem;">
        <strong style="color:var(--text-main);">Descripción:</strong>
        <?php echo htmlspecialchars($s['Descripcion']); ?>
    </p>

    <div class="desglose-grid">
        <?php
        $componentes = [
            ['PC / Estaciones',       $s['Cant_PC'],         $sub_pc,   $PRECIOS['pc']],
            ['Impresoras de Red',      $s['Cant_Impresoras'], $sub_imp,  $PRECIOS['impresoras']],
            ['Puntos de Red',          $s['Cant_Puntos_Red'], $sub_red,  $PRECIOS['puntos_red']],
            ['Servidores / Rack',      $s['Cant_Servidores'], $sub_srv,  $PRECIOS['servidores']],
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
        <div class="desglose-item" style="border-color:var(--primary-green-hover);">
            <span class="d-label">Presupuesto estimado cliente</span>
            <span class="d-value" style="color:#20e39b;">$<?php echo number_format($total_estimado_cliente, 2); ?> USD</span>
        </div>
    </div>

    <?php if (!empty($s['Notas_Extra'])): ?>
    <p class="sec-title">Notas del cliente</p>
    <p style="font-size:0.88rem;color:var(--text-muted);margin-bottom:1.5rem;white-space:pre-wrap;"><?php echo htmlspecialchars($s['Notas_Extra']); ?></p>
    <?php endif; ?>

    <!-- Formulario empleado -->
    <p class="sec-title">Definir plan y precio final</p>
    <form class="form-custom-plan"
          action="<?php echo BASE_URL; ?>/src/controllers/gestionar_plan_custom.php?partial=<?php echo $is_partial ? '1' : '0'; ?>"
          method="POST">
        <input type="hidden" name="action"           value="crear_plan_custom">
        <input type="hidden" name="sol_id"           value="<?php echo $s['ID_Solicitud']; ?>">
        <input type="hidden" name="empresa_usuario_id" value="<?php echo $s['ID_Usuario']; ?>">

        <div class="form-group">
            <label for="nombre_plan">Nombre del Plan <span style="color:#ef4444">*</span></label>
            <input type="text" id="nombre_plan" name="nombre_plan" required
                   value="Plan Custom — <?php echo htmlspecialchars($s['Razon_Social'] ?? $s['Nombre_Usuario']); ?>">
        </div>
        <div class="form-group">
            <label for="descripcion_plan">Descripción del Plan <span style="color:#ef4444">*</span></label>
            <textarea id="descripcion_plan" name="descripcion_plan" rows="3" required
                      placeholder="Alcance, plazos, condiciones especiales..."></textarea>
        </div>

        <!-- Componentes: el empleado ajusta cantidades y precios finales -->
        <p class="sec-title" style="margin-top:0.25rem;">Componentes y precios finales acordados</p>
        <div class="comp-grid-empleado">

            <div class="comp-row">
                <span class="comp-label">PC / Estaciones</span>
                <span class="comp-precio">Cantidad × Precio unitario USD</span>
                <div class="comp-input-wrap">
                    <input type="number" name="cant_pc" id="cant_pc" min="0" max="999"
                           value="<?php echo $s['Cant_PC']; ?>" oninput="recalcTotal()">
                    <span style="font-size:0.8rem;color:var(--text-muted)">×</span>
                    <input type="number" name="precio_pc" id="precio_pc" min="0" step="0.01"
                           value="800.00" oninput="recalcTotal()" style="width:85px;">
                </div>
            </div>

            <div class="comp-row">
                <span class="comp-label">Impresoras de Red</span>
                <span class="comp-precio">Cantidad × Precio unitario USD</span>
                <div class="comp-input-wrap">
                    <input type="number" name="cant_impresoras" id="cant_impresoras" min="0" max="999"
                           value="<?php echo $s['Cant_Impresoras']; ?>" oninput="recalcTotal()">
                    <span style="font-size:0.8rem;color:var(--text-muted)">×</span>
                    <input type="number" name="precio_impresoras" id="precio_impresoras" min="0" step="0.01"
                           value="400.00" oninput="recalcTotal()" style="width:85px;">
                </div>
            </div>

            <div class="comp-row">
                <span class="comp-label">Puntos de Red</span>
                <span class="comp-precio">Cantidad × Precio unitario USD</span>
                <div class="comp-input-wrap">
                    <input type="number" name="cant_puntos_red" id="cant_puntos_red" min="0" max="999"
                           value="<?php echo $s['Cant_Puntos_Red']; ?>" oninput="recalcTotal()">
                    <span style="font-size:0.8rem;color:var(--text-muted)">×</span>
                    <input type="number" name="precio_puntos_red" id="precio_puntos_red" min="0" step="0.01"
                           value="150.00" oninput="recalcTotal()" style="width:85px;">
                </div>
            </div>

            <div class="comp-row">
                <span class="comp-label">Servidores / Rack</span>
                <span class="comp-precio">Cantidad × Precio unitario USD</span>
                <div class="comp-input-wrap">
                    <input type="number" name="cant_servidores" id="cant_servidores" min="0" max="999"
                           value="<?php echo $s['Cant_Servidores']; ?>" oninput="recalcTotal()">
                    <span style="font-size:0.8rem;color:var(--text-muted)">×</span>
                    <input type="number" name="precio_servidores" id="precio_servidores" min="0" step="0.01"
                           value="1200.00" oninput="recalcTotal()" style="width:85px;">
                </div>
            </div>

            <div class="comp-row">
                <span class="comp-label">Wi-Fi Mesh</span>
                <span class="comp-precio">Cantidad × Precio unitario USD</span>
                <div class="comp-input-wrap">
                    <input type="number" name="cant_wifi" id="cant_wifi" min="0" max="999"
                           value="<?php echo $s['Cant_Wifi']; ?>" oninput="recalcTotal()">
                    <span style="font-size:0.8rem;color:var(--text-muted)">×</span>
                    <input type="number" name="precio_wifi" id="precio_wifi" min="0" step="0.01"
                           value="250.00" oninput="recalcTotal()" style="width:85px;">
                </div>
            </div>

        </div>

        <div class="total-preview">
            <span class="lbl">Total Final del Plan:</span>
            <span class="amt" id="total_display">$0.00 USD</span>
            <input type="hidden" name="total_final" id="total_final" value="0">
        </div>

        <div class="form-group">
            <label for="cant_estaciones">Estaciones totales (perfil del plan)</label>
            <input type="number" id="cant_estaciones" name="cant_estaciones"
                   min="0" value="<?php echo $s['Cant_PC']; ?>" placeholder="Ej: 6">
        </div>

        <div class="form-group">
            <label for="notas_empleado">Notas internas del empleado</label>
            <textarea id="notas_empleado" name="notas_empleado" rows="3"
                      placeholder="Condiciones de pago, fechas de instalación, aclaraciones..."></textarea>
        </div>

        <div class="plan-form-actions">
            <a href="cotizaciones_panel.php?partial=<?php echo $is_partial ? '1' : '0'; ?>"
               class="btn btn-outline partial-link">Cancelar</a>
            <button type="submit" class="btn btn-primary">Crear Plan y Asignar a Empresa</button>
        </div>
    </form>

    <script>
    function recalcTotal() {
        const get = id => parseFloat(document.getElementById(id).value) || 0;
        const total = (get('cant_pc')         * get('precio_pc'))         +
                      (get('cant_impresoras')  * get('precio_impresoras')) +
                      (get('cant_puntos_red')  * get('precio_puntos_red')) +
                      (get('cant_servidores')  * get('precio_servidores')) +
                      (get('cant_wifi')        * get('precio_wifi'));
        document.getElementById('total_display').innerText =
            '$' + total.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}) + ' USD';
        document.getElementById('total_final').value = total.toFixed(2);
    }
    recalcTotal();
    </script>

<?php else: ?>
    <!-- ══════ VISTA: LISTA DE SOLICITUDES ══════ -->
    <div class="cot-header">
        <h2>Solicitudes de Cotización</h2>
    </div>

    <?php $base_url = 'cotizaciones_panel.php?partial=' . ($is_partial ? '1' : '0'); ?>
    <div class="cot-filter-bar">
        <a href="<?php echo $base_url; ?>&estado=Pendiente"
           class="partial-link <?php echo $filtro_estado === 'Pendiente' ? 'active' : ''; ?>">Pendientes</a>
        <a href="<?php echo $base_url; ?>&estado=Aprobada"
           class="partial-link <?php echo $filtro_estado === 'Aprobada'  ? 'active' : ''; ?>">Aprobadas</a>
        <a href="<?php echo $base_url; ?>&estado=Rechazada"
           class="partial-link <?php echo $filtro_estado === 'Rechazada' ? 'active' : ''; ?>">Rechazadas</a>
        <a href="<?php echo $base_url; ?>&estado=todas"
           class="partial-link <?php echo $filtro_estado === 'todas'     ? 'active' : ''; ?>">Todas</a>
    </div>

    <?php if (empty($solicitudes)): ?>
        <div class="empty-state">
            <p style="font-size:1.1rem;font-weight:600;">No hay solicitudes<?php echo $filtro_estado !== 'todas' ? ' ' . strtolower($filtro_estado) . 's' : ''; ?>.</p>
            <p>Las solicitudes enviadas por las empresas desde el cotizador aparecerán aquí.</p>
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
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($solicitudes as $sol):
                $total_cli =
                    ($sol['Cant_PC']         * $PRECIOS['pc'])         +
                    ($sol['Cant_Impresoras']  * $PRECIOS['impresoras']) +
                    ($sol['Cant_Puntos_Red']  * $PRECIOS['puntos_red']) +
                    ($sol['Cant_Servidores']  * $PRECIOS['servidores']) +
                    ($sol['Cant_Wifi']        * $PRECIOS['wifi']);

                $resumen = [];
                if ($sol['Cant_PC'])         $resumen[] = $sol['Cant_PC']         . ' PC';
                if ($sol['Cant_Impresoras'])  $resumen[] = $sol['Cant_Impresoras']  . ' Imp.';
                if ($sol['Cant_Puntos_Red'])  $resumen[] = $sol['Cant_Puntos_Red']  . ' Red';
                if ($sol['Cant_Servidores'])  $resumen[] = $sol['Cant_Servidores']  . ' Srv.';
                if ($sol['Cant_Wifi'])        $resumen[] = $sol['Cant_Wifi']        . ' WiFi';
                $badge_class = match($sol['Estado']) {
                    'Pendiente' => 'badge-pendiente',
                    'Aprobada'  => 'badge-aprobada',
                    'Rechazada' => 'badge-rechazada',
                    default     => ''
                };
            ?>
                <tr>
                    <td><strong>#<?php echo $sol['ID_Solicitud']; ?></strong></td>
                    <td>
                        <?php echo date('d/m/Y', strtotime($sol['Fecha_Solicitud'])); ?><br>
                        <span style="font-size:0.78rem;color:var(--text-muted);"><?php echo date('H:i', strtotime($sol['Fecha_Solicitud'])); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($sol['Razon_Social'] ?? $sol['Nombre_Usuario']); ?></td>
                    <td><?php echo htmlspecialchars($sol['RUT'] ?? '—'); ?></td>
                    <td style="max-width:180px;font-size:0.84rem;color:var(--text-muted);">
                        <?php echo htmlspecialchars(mb_strimwidth($sol['Descripcion'], 0, 60, '…')); ?>
                    </td>
                    <td style="font-size:0.82rem;color:var(--text-muted);">
                        <?php echo empty($resumen) ? '—' : implode(', ', $resumen); ?>
                    </td>
                    <td style="font-weight:700;color:var(--primary-green-hover);font-size:0.85rem;">
                        $<?php echo number_format($total_cli, 2); ?>
                    </td>
                    <td><span class="badge-estado <?php echo $badge_class; ?>"><?php echo $sol['Estado']; ?></span></td>
                    <td>
                        <?php if ($sol['Estado'] === 'Pendiente'): ?>
                            <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                                <a class="btn btn-primary btn-sm partial-link"
                                   href="cotizaciones_panel.php?vista=crear&sol_id=<?php echo $sol['ID_Solicitud']; ?>&partial=<?php echo $is_partial ? '1' : '0'; ?>">
                                    Crear Plan
                                </a>
                                <a class="btn btn-danger btn-sm partial-link"
                                   href="<?php echo BASE_URL; ?>/src/controllers/gestionar_plan_custom.php?action=rechazar&sol_id=<?php echo $sol['ID_Solicitud']; ?>&partial=<?php echo $is_partial ? '1' : '0'; ?>"
                                   data-confirm="¿Rechazar la solicitud #<?php echo $sol['ID_Solicitud']; ?>?">
                                    Rechazar
                                </a>
                            </div>
                        <?php elseif ($sol['Estado'] === 'Aprobada'): ?>
                            <span style="font-size:0.8rem;color:#4ade80;">✓ Plan asignado</span>
                        <?php else: ?>
                            <span style="font-size:0.8rem;color:var(--text-muted);">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php if (!$is_partial): require_once(INCLUDES_DIR . '/footer.php'); endif; ?>
