<?php
/**
 * Controller: gestionar_plan_custom.php
 * Gestiona el flujo de aprobación y rechazo de solicitudes de infraestructura.
 * Solo accesible para admins (Rol 2 o 3).
 */
require_once(__DIR__ . '/../../includes/config.php');
require_once(MODELS_PATH . '/plan.php');

if (!isUserLoggedIn() || !isset($_SESSION['usuario_rol']) || !in_array($_SESSION['usuario_rol'], [2, 3])) {
    header('Location: ' . PUBLIC_PATH . '/login.php');
    exit();
}

$planModel = new Plan($pdo);
$planModel->ensureSolicitudCotizacionColumns();

// ── Configuración de redirección ──────────────────────────────────────────────
$is_partial = isset($_REQUEST['partial']) && $_REQUEST['partial'] == '1';
$panel_url  = $is_partial
    ? VIEWS_PATH . '/adminpanel.php?page=cotizaciones&partial=1'
    : VIEWS_PATH . '/cotizaciones_panel.php';
$create_url = $panel_url . ($is_partial ? '&vista=crear&sol_id=' : '?vista=crear&sol_id=');

// ── GET: rechazar solicitud ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'rechazar') {
    $sol_id = (int)($_GET['sol_id'] ?? 0);

    if ($sol_id <= 0) {
        $_SESSION['cot_error'] = 'ID de solicitud no válido.';
        header('Location: ' . $panel_url);
        exit();
    }

    try {
        $stmtGet = $pdo->prepare("SELECT ID_Usuario FROM solicitud_cotizacion WHERE ID_Solicitud = ? AND Estado = 'Pendiente'");
        $stmtGet->execute([$sol_id]);
        $sol = $stmtGet->fetch(PDO::FETCH_ASSOC);

        if (!$sol) {
            $_SESSION['cot_error'] = 'Solicitud no encontrada o ya procesada.';
            header('Location: ' . $panel_url);
            exit();
        }

        $pdo->beginTransaction();
        $pdo->prepare("UPDATE solicitud_cotizacion SET Estado = 'Rechazada' WHERE ID_Solicitud = ?")->execute([$sol_id]);
        $pdo->commit();

        $_SESSION['cot_success'] = 'Solicitud #' . $sol_id . ' rechazada correctamente.';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['cot_error'] = 'Error al rechazar: ' . $e->getMessage();
    }

    header('Location: ' . $panel_url);
    exit();
}

// ── POST: aprobar solicitud y moverla al departamento de instalación ─────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'aprobar_cotizacion') {
    $sol_id = (int)($_POST['sol_id'] ?? 0);

    if ($sol_id <= 0) {
        $_SESSION['cot_error'] = 'Datos de solicitud no válidos.';
        header('Location: ' . $panel_url);
        exit();
    }

    try {
        $pdo->beginTransaction();

        $stmtCheck = $pdo->prepare("SELECT Estado_Instalacion, Monto_Instalacion FROM solicitud_cotizacion WHERE ID_Solicitud = ? AND Estado = 'Pendiente'");
        $stmtCheck->execute([$sol_id]);
        $sol = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$sol) {
            throw new Exception('La solicitud no existe o ya fue procesada.');
        }

        $estadoInstalacion = $sol['Estado_Instalacion'] ?? 'Pendiente';
        $montoInstalacion = (float)($sol['Monto_Instalacion'] ?? 0);

        if ($estadoInstalacion !== 'Aprobada') {
            throw new Exception('La instalación debe estar aprobada antes de aceptar la solicitud de plan.');
        }

        if ($montoInstalacion <= 0) {
            throw new Exception('Debe registrarse un monto estimado de instalación antes de aceptar la solicitud.');
        }

        $pdo->prepare("UPDATE solicitud_cotizacion SET Estado = 'Aprobada' WHERE ID_Solicitud = ? AND Estado = 'Pendiente'")->execute([$sol_id]);

        $affected = $pdo->prepare("SELECT ID_Solicitud FROM solicitud_cotizacion WHERE ID_Solicitud = ? AND Estado = 'Aprobada'");
        $affected->execute([$sol_id]);
        if (!$affected->fetch()) {
            throw new Exception('La solicitud no existe o ya fue procesada.');
        }

        $pdo->commit();
        $_SESSION['cot_success'] = 'Solicitud #' . $sol_id . ' aprobada correctamente. La instalación ya había sido aprobada y el monto estimado quedó registrado.';
        header('Location: ' . $panel_url);
        exit();
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['cot_error'] = 'Error al aprobar la solicitud: ' . $e->getMessage();
        header('Location: ' . $create_url . $sol_id);
        exit();
    }
}

$_SESSION['cot_error'] = 'Acción no reconocida.';
header('Location: ' . $panel_url);
exit();
