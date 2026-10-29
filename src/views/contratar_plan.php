<?php
require_once(__DIR__ . '/../../includes/config.php');
require_once(MODELS_PATH . '/plan.php');

if (!isUserLoggedIn()) {
    header('Location: ' . PUBLIC_PATH . '/login.php');
    exit();
}

$user         = getCurrentUser();
$userId       = $user['id'];
$tipo_usuario = $_SESSION['tipo_usuario'] ?? '';

if ($tipo_usuario !== 'Empresa') {
    $_SESSION['error_message'] = 'Solo los usuarios de tipo Empresa pueden crear solicitudes de infraestructura.';
    header('Location: ' . VIEWS_PATH . '/planes.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? 'cotizacion';
    $planModel = new Plan($pdo);
    $planModel->ensureSolicitudCotizacionColumns();

    // ── Cancelar solicitud pendiente de cotización ────────────────────────────
    if ($action === 'cancel_solicitud') {
        // Marcar la solicitud pendiente del usuario como Rechazada
        $stmt = $pdo->prepare("
            UPDATE solicitud_cotizacion
            SET Estado = 'Rechazada'
            WHERE ID_Usuario = ? AND Estado = 'Pendiente'
        ");
        $stmt->execute([$userId]);

        if ($stmt->rowCount() > 0) {
            $_SESSION['success_message'] = 'Tu solicitud de cotización ha sido cancelada.';
        } else {
            $_SESSION['error_message'] = 'No se encontró una solicitud pendiente para cancelar.';
        }

    // ── Enviar solicitud de cotización personalizada ──────────────────────────
    } elseif ($action === 'cotizacion') {
        $descripcion     = trim($_POST['descripcion']    ?? '');
        $plan_id         = isset($_POST['plan_id']) ? (int)$_POST['plan_id'] : 0;
        $cant_pc         = max(0, (int)($_POST['cant_pc']         ?? 0));
        $cant_impresoras = max(0, (int)($_POST['cant_impresoras'] ?? 0));
        $cant_puntos_red = max(0, (int)($_POST['cant_puntos_red'] ?? 0));
        $cant_servidores = max(0, (int)($_POST['cant_servidores'] ?? 0));
        $cant_wifi       = max(0, (int)($_POST['cant_wifi']       ?? 0));
        $notas_extra     = trim($_POST['notas_extra']    ?? '') ?: null;
        $tipo_ordenador  = trim($_POST['tipo_ordenador'] ?? 'Oficina');
        $gama_equipo     = trim($_POST['gama_equipo']    ?? 'Media');

        if ($plan_id > 0 && empty($descripcion)) {
            $descripcion = 'Solicitud de paquete ' . $plan_id . ' - tipo ' . $tipo_ordenador . ' - gama ' . $gama_equipo;
        }

        // Total solo para mostrar el mensaje de confirmación al usuario
        $total_calculado =
            ($cant_pc        * 800)  +
            ($cant_impresoras * 400)  +
            ($cant_puntos_red * 150)  +
            ($cant_servidores * 1200) +
            ($cant_wifi       * 250);

        if (empty($descripcion)) {
            $_SESSION['error_message'] = 'Por favor ingresa una breve descripción del proyecto o requerimiento.';
        } else {
            // Solo inserta en solicitud_cotizacion — no toca usuario_plan ni plan_oficina
            if ($planModel->createSolicitudCotizacion(
                $userId,
                $descripcion,
                $cant_pc,
                $cant_impresoras,
                $cant_puntos_red,
                $cant_servidores,
                $cant_wifi,
                $notas_extra,
                $tipo_ordenador,
                $gama_equipo,
                'Pendiente'
            )) {
                $_SESSION['success_message'] =
                    '¡Solicitud creada correctamente! ' .
                    'Presupuesto estimado: <strong>$' . number_format($total_calculado, 2) . ' USD</strong>. ' .
                    'Nuestro equipo revisará tu solicitud y aprobará o rechazará la cotización antes de pasar a instalación.';
            } else {
                $_SESSION['error_message'] = 'Error al enviar la solicitud: ' . $planModel->getLastError();
            }
        }

    } else {
        // Bloquear si tiene una solicitud pendiente
        $stmtCheck = $pdo->prepare("
            SELECT ID_Solicitud FROM solicitud_cotizacion
            WHERE ID_Usuario = ? AND Estado = 'Pendiente'
            LIMIT 1
        ");
        $stmtCheck->execute([$userId]);
        if ($stmtCheck->fetch()) {
            $_SESSION['error_message'] = 'Tienes una solicitud pendiente. Debes cancelarla antes de crear otra.';
            header('Location: ' . VIEWS_PATH . '/planes.php');
            exit();
        }

        $_SESSION['error_message'] = 'La contratación directa ya no está disponible. Debes crear una solicitud y esperar aprobación.';
    }
}

header('Location: ' . VIEWS_PATH . '/planes.php');
exit();
