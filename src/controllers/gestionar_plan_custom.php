<?php
/**
 * Controller: gestionar_plan_custom.php
 * Crea planes custom a partir de solicitudes y rechaza solicitudes.
 * Solo accesible para admins (Rol 2 o 3).
 */
require_once(__DIR__ . '/../../includes/config.php');
require_once(MODELS_PATH . '/plan.php');

if (!isUserLoggedIn() || !isset($_SESSION['usuario_rol']) || !in_array($_SESSION['usuario_rol'], [2, 3])) {
    header('Location: ' . PUBLIC_PATH . '/login.php');
    exit();
}

$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == '1';
$panel_url  = VIEWS_PATH . '/cotizaciones_panel.php' . ($is_partial ? '?partial=1' : '');

// ── GET: rechazar solicitud ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'rechazar') {
    $sol_id = (int)($_GET['sol_id'] ?? 0);

    if ($sol_id <= 0) {
        $_SESSION['cot_error'] = 'ID de solicitud no válido.';
        header('Location: ' . $panel_url);
        exit();
    }

    try {
        $stmtGet = $pdo->prepare("
            SELECT ID_Usuario FROM solicitud_cotizacion
            WHERE ID_Solicitud = ? AND Estado = 'Pendiente'
        ");
        $stmtGet->execute([$sol_id]);
        $sol = $stmtGet->fetch(PDO::FETCH_ASSOC);

        if (!$sol) {
            $_SESSION['cot_error'] = 'Solicitud no encontrada o ya procesada.';
            header('Location: ' . $panel_url);
            exit();
        }

        $pdo->beginTransaction();

        // Marcar solicitud como Rechazada
        $pdo->prepare("UPDATE solicitud_cotizacion SET Estado = 'Rechazada' WHERE ID_Solicitud = ?")
            ->execute([$sol_id]);

        $pdo->commit();
        $_SESSION['cot_success'] = 'Solicitud #' . $sol_id . ' rechazada correctamente.';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['cot_error'] = 'Error al rechazar: ' . $e->getMessage();
    }

    header('Location: ' . $panel_url);
    exit();
}

// ── POST: crear plan custom y asignarlo a la empresa ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'crear_plan_custom') {

    $sol_id             = (int)($_POST['sol_id']             ?? 0);
    $empresa_usuario_id = (int)($_POST['empresa_usuario_id'] ?? 0);
    $nombre_plan        = trim($_POST['nombre_plan']         ?? '');
    $descripcion_plan   = trim($_POST['descripcion_plan']    ?? '');
    $cant_estaciones    = max(0, (int)($_POST['cant_estaciones'] ?? 0));
    $notas_empleado     = trim($_POST['notas_empleado']      ?? '') ?: null;

    // Cantidades finales (el empleado puede ajustarlas)
    $cant_pc          = max(0, (int)($_POST['cant_pc']          ?? 0));
    $cant_impresoras  = max(0, (int)($_POST['cant_impresoras']  ?? 0));
    $cant_puntos_red  = max(0, (int)($_POST['cant_puntos_red']  ?? 0));
    $cant_servidores  = max(0, (int)($_POST['cant_servidores']  ?? 0));
    $cant_wifi        = max(0, (int)($_POST['cant_wifi']        ?? 0));

    // Precios unitarios finales acordados por el empleado
    $precio_pc         = max(0, (float)($_POST['precio_pc']         ?? 800));
    $precio_impresoras = max(0, (float)($_POST['precio_impresoras'] ?? 400));
    $precio_puntos_red = max(0, (float)($_POST['precio_puntos_red'] ?? 150));
    $precio_servidores = max(0, (float)($_POST['precio_servidores'] ?? 1200));
    $precio_wifi       = max(0, (float)($_POST['precio_wifi']       ?? 250));

    // Totales por componente (precio_* en plan_custom_detalle guarda el SUBTOTAL)
    $sub_pc         = $cant_pc         * $precio_pc;
    $sub_impresoras = $cant_impresoras * $precio_impresoras;
    $sub_puntos_red = $cant_puntos_red * $precio_puntos_red;
    $sub_servidores = $cant_servidores * $precio_servidores;
    $sub_wifi       = $cant_wifi       * $precio_wifi;
    $total_final    = $sub_pc + $sub_impresoras + $sub_puntos_red + $sub_servidores + $sub_wifi;

    // Validaciones
    if (empty($nombre_plan) || empty($descripcion_plan)) {
        $_SESSION['cot_error'] = 'El nombre y la descripción del plan son obligatorios.';
        header('Location: ' . VIEWS_PATH . '/cotizaciones_panel.php?vista=crear&sol_id=' . $sol_id . ($is_partial ? '&partial=1' : ''));
        exit();
    }
    if ($sol_id <= 0 || $empresa_usuario_id <= 0) {
        $_SESSION['cot_error'] = 'Datos de solicitud o empresa no válidos.';
        header('Location: ' . $panel_url);
        exit();
    }

    $creado_por = (int)$_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // 1. Insertar el nuevo plan en plan_oficina (AUTO_INCREMENT >= 5)
        $stmtPlan = $pdo->prepare("
            INSERT INTO plan_oficina (Nombre_Plan, Descripcion, Cant_Estaciones)
            VALUES (?, ?, ?)
        ");
        $stmtPlan->execute([$nombre_plan, $descripcion_plan, $cant_estaciones]);
        $nuevo_plan_id = (int)$pdo->lastInsertId();

        if ($nuevo_plan_id < 5) {
            throw new Exception(
                "El ID generado ($nuevo_plan_id) entra en conflicto con planes reservados. " .
                "Ejecuta migration_plan_custom.sql para fijar AUTO_INCREMENT = 5."
            );
        }

        // 2. Insertar el detalle en plan_custom_detalle
        $pdo->prepare("
            INSERT INTO plan_custom_detalle
                (ID_Plan, ID_Solicitud,
                 Cant_PC, Cant_Impresoras, Cant_Puntos_Red, Cant_Servidores, Cant_Wifi,
                 Precio_PC, Precio_Impresoras, Precio_Puntos_Red, Precio_Servidores, Precio_Wifi,
                 Total_Final, Notas_Empleado, Creado_Por)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ")->execute([
            $nuevo_plan_id, $sol_id,
            $cant_pc, $cant_impresoras, $cant_puntos_red, $cant_servidores, $cant_wifi,
            $sub_pc, $sub_impresoras, $sub_puntos_red, $sub_servidores, $sub_wifi,
            $total_final, $notas_empleado, $creado_por,
        ]);

        // 3. Asignar el plan a usuario_plan y empresa
        $stmtExist = $pdo->prepare("SELECT ID_Usuario FROM usuario_plan WHERE ID_Usuario = ?");
        $stmtExist->execute([$empresa_usuario_id]);
        if ($stmtExist->fetch()) {
            $pdo->prepare("UPDATE usuario_plan SET ID_Plan = ?, Fecha_Contratacion = NOW() WHERE ID_Usuario = ?")
                ->execute([$nuevo_plan_id, $empresa_usuario_id]);
        } else {
            $pdo->prepare("INSERT INTO usuario_plan (ID_Usuario, ID_Plan, Fecha_Contratacion) VALUES (?, ?, NOW())")
                ->execute([$empresa_usuario_id, $nuevo_plan_id]);
        }
        $pdo->prepare("UPDATE empresa SET ID_Plan = ? WHERE ID_Usuario = ?")
            ->execute([$nuevo_plan_id, $empresa_usuario_id]);

        // 4. Marcar la solicitud como Aprobada
        $pdo->prepare("UPDATE solicitud_cotizacion SET Estado = 'Aprobada' WHERE ID_Solicitud = ?")
            ->execute([$sol_id]);

        $pdo->commit();

        $_SESSION['cot_success'] =
            'Plan <strong>' . htmlspecialchars($nombre_plan) . '</strong> ' .
            '(ID #' . $nuevo_plan_id . ') creado y asignado. ' .
            'Total: <strong>$' . number_format($total_final, 2) . ' USD</strong>.';
        header('Location: ' . $panel_url);
        exit();

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['cot_error'] = 'Error al crear el plan: ' . $e->getMessage();
        header('Location: ' . VIEWS_PATH . '/cotizaciones_panel.php?vista=crear&sol_id=' . $sol_id . ($is_partial ? '&partial=1' : ''));
        exit();
    }
}

// Fallback
$_SESSION['cot_error'] = 'Acción no reconocida.';
header('Location: ' . $panel_url);
exit();
