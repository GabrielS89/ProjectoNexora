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
    $_SESSION['error_message'] = 'Solo los usuarios de tipo Empresa pueden contratar o cambiar planes de infraestructura.';
    header('Location: ' . VIEWS_PATH . '/planes.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? 'contract';
    $planModel = new Plan($pdo);

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
            $_SESSION['success_message'] = 'Tu solicitud de cotizacion ha sido cancelada.';
        } else {
            $_SESSION['error_message'] = 'No se encontro una solicitud pendiente para cancelar.';
        }

    // ── Dar de baja un plan estándar activo ───────────────────────────────────
    } elseif ($action === 'cancel') {
        if ($planModel->cancelPlan($userId)) {
            $_SESSION['success_message'] = 'Has dado de baja tu plan exitosamente.';
        } else {
            $_SESSION['error_message'] = 'Error al dar de baja el plan: ' . $planModel->getLastError();
        }

    // ── Enviar solicitud de cotización personalizada ──────────────────────────
    } elseif ($action === 'cotizacion') {
        $descripcion     = trim($_POST['descripcion']    ?? '');
        $cant_pc         = max(0, (int)($_POST['cant_pc']         ?? 0));
        $cant_impresoras = max(0, (int)($_POST['cant_impresoras'] ?? 0));
        $cant_puntos_red = max(0, (int)($_POST['cant_puntos_red'] ?? 0));
        $cant_servidores = max(0, (int)($_POST['cant_servidores'] ?? 0));
        $cant_wifi       = max(0, (int)($_POST['cant_wifi']       ?? 0));
        $notas_extra     = trim($_POST['notas_extra']    ?? '') ?: null;

        // Total solo para mostrar el mensaje de confirmación al usuario
        $total_calculado =
            ($cant_pc        * 800)  +
            ($cant_impresoras * 400)  +
            ($cant_puntos_red * 150)  +
            ($cant_servidores * 1200) +
            ($cant_wifi       * 250);

        if (empty($descripcion)) {
            $_SESSION['error_message'] = 'Por favor ingresa una breve descripcion del proyecto o requerimiento.';
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
                $notas_extra
            )) {
                $_SESSION['success_message'] =
                    '¡Solicitud enviada correctamente! ' .
                    'Presupuesto estimado: <strong>$' . number_format($total_calculado, 2) . ' USD</strong>. ' .
                    'Nuestro equipo revisara tu solicitud y te asignara un plan personalizado a la brevedad.';
            } else {
                $_SESSION['error_message'] = 'Error al enviar la solicitud: ' . $planModel->getLastError();
            }
        }

    // ── Contratar un plan estándar ────────────────────────────────────────────
    } else {
        // Bloquear si tiene una solicitud pendiente
        $stmtCheck = $pdo->prepare("
            SELECT ID_Solicitud FROM solicitud_cotizacion
            WHERE ID_Usuario = ? AND Estado = 'Pendiente'
            LIMIT 1
        ");
        $stmtCheck->execute([$userId]);
        if ($stmtCheck->fetch()) {
            $_SESSION['error_message'] = 'Tienes una solicitud de Plan Custom pendiente. Cancela la solicitud antes de contratar otro plan.';
            header('Location: ' . VIEWS_PATH . '/planes.php');
            exit();
        }

        $plan_id = filter_input(INPUT_POST, 'plan_id', FILTER_VALIDATE_INT);
        if ($plan_id && $plan_id > 0 && $plan_id !== 4) {
            if ($planModel->assignPlan($userId, $plan_id)) {
                $planDetails = $planModel->getPlanById($plan_id);
                $nombrePlan  = $planDetails ? $planDetails['Nombre_Plan'] : 'Plan #' . $plan_id;
                $_SESSION['success_message'] = '¡Has contratado el <strong>' . htmlspecialchars($nombrePlan) . '</strong> exitosamente!';
            } else {
                $_SESSION['error_message'] = 'Error al contratar el plan: ' . $planModel->getLastError();
            }
        } else {
            $_SESSION['error_message'] = 'ID de plan no valido.';
        }
    }
}

header('Location: ' . VIEWS_PATH . '/planes.php');
exit();
