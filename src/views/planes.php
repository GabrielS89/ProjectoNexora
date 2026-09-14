<?php
$page_title = 'Planes de Infraestructura - Nexora';
require_once(__DIR__ . '/../../includes/config.php');
require_once(MODELS_PATH . '/plan.php');

$is_standalone = (!isset($is_embedded) || !$is_embedded);

if ($is_standalone) {
    require_once(INCLUDES_DIR . '/header.php');
}

$planModel = new Plan($pdo);
$isLoggedIn = isUserLoggedIn();
$currentUser = getCurrentUser();
$tipoUsuario = $_SESSION['tipo_usuario'] ?? '';
$isEmpresa = $isLoggedIn && ($tipoUsuario === 'Empresa');

// ── Estado del plan y solicitud pendiente para empresas ───────────────────────
$currentPlan     = null;
$currentPlanId   = 0;
$solicitudPendiente = null;   // fila de solicitud_cotizacion si está Pendiente

if ($isEmpresa && isset($currentUser['id'])) {
    $currentPlan   = $planModel->getUserPlan((int)$currentUser['id']);
    $currentPlanId = (int)($currentPlan['ID_Plan'] ?? 0);

    // Verificar si tiene una solicitud de cotización custom pendiente de aprobación
    $stmtSol = $pdo->prepare("
        SELECT ID_Solicitud, Fecha_Solicitud,
               Cant_PC, Cant_Impresoras, Cant_Puntos_Red, Cant_Servidores, Cant_Wifi
        FROM solicitud_cotizacion
        WHERE ID_Usuario = ? AND Estado = 'Pendiente'
        ORDER BY Fecha_Solicitud DESC
        LIMIT 1
    ");
    $stmtSol->execute([$currentUser['id']]);
    $solicitudPendiente = $stmtSol->fetch(PDO::FETCH_ASSOC) ?: null;
}

// Obtener y limpiar mensajes de feedback de sesión
$success_msg = $_SESSION['success_message'] ?? null;
$error_msg = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<div class="planes-container">

    <!-- Mensajes de feedback -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert-box alert-success">
            <span class="alert-tag">EXITO</span>
            <div><?php echo $success_msg; ?></div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert-box alert-danger">
            <span class="alert-tag">ERROR</span>
            <div><?php echo htmlspecialchars($error_msg); ?></div>
        </div>
    <?php endif; ?>

    <?php if ($is_standalone): ?>
    <div class="hero-section" style="padding-bottom: 1.5rem;">
        <h1>Planes de <span>Infraestructura</span></h1>
        <p class="hero-subtitle">Soluciones tecnologicas llave en mano adaptadas a la escala de tu empresa.</p>
    </div>
    <?php endif; ?>

    <!-- Banner de Estado de Suscripción para Empresas -->
    <?php if ($isEmpresa): ?>
        <?php if ($solicitudPendiente): ?>
        <!-- Solicitud pendiente de aprobación admin -->
        <div class="subscription-status-card">
            <div class="status-header">
                <div class="status-badge-container">
                    <span class="status-dot"></span>
                    <span class="status-title">Solicitud de Plan Custom</span>
                </div>
                <span class="badge-plan-review">En Revision Tecnica</span>
            </div>
            <div class="status-body">
                <div class="status-info-col">
                    <h3 class="status-plan-name">Plan Custom — Pendiente de Aprobacion</h3>
                    <p class="status-plan-desc">
                        Tu solicitud de infraestructura personalizada esta siendo evaluada por nuestro equipo. 
                        No puedes contratar otro plan mientras este en revision.
                    </p>
                    <p class="status-date">
                        <strong>Fecha de solicitud:</strong>
                        <?php echo date('d/m/Y H:i', strtotime($solicitudPendiente['Fecha_Solicitud'])); ?> hs
                    </p>
                </div>
                <div class="status-actions">
                    <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST"
                          onsubmit="return confirm('¿Seguro que deseas cancelar tu solicitud de cotizacion?');">
                        <input type="hidden" name="action" value="cancel_solicitud">
                        <button type="submit" class="btn btn-danger btn-sm">Cancelar Solicitud</button>
                    </form>
                </div>
            </div>
        </div>

        <?php elseif ($currentPlan && $currentPlanId > 0): ?>
        <!-- Plan activo contratado -->
        <div class="subscription-status-card">
            <div class="status-header">
                <div class="status-badge-container">
                    <span class="status-dot"></span>
                    <span class="status-title">Estado de Suscripcion Empresarial</span>
                </div>
                <span class="badge-plan-active">Suscripcion Activa</span>
            </div>
            <div class="status-body">
                <div class="status-info-col">
                    <h3 class="status-plan-name"><?php echo htmlspecialchars($currentPlan['Nombre_Plan']); ?></h3>
                    <p class="status-plan-desc"><?php echo htmlspecialchars($currentPlan['Descripcion'] ?? ''); ?></p>
                    <?php if (!empty($currentPlan['Fecha_Contratacion'])): ?>
                        <p class="status-date">
                            <strong>Contratado el:</strong>
                            <?php echo date('d/m/Y H:i', strtotime($currentPlan['Fecha_Contratacion'])); ?> hs
                        </p>
                    <?php endif; ?>
                </div>
                <div class="status-actions">
                    <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST"
                          onsubmit="return confirm('¿Seguro que deseas dar de baja tu plan actual?');">
                        <input type="hidden" name="action" value="cancel">
                        <button type="submit" class="btn btn-danger btn-sm">Dar de baja plan</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php elseif ($isLoggedIn && !$isEmpresa): ?>
        <!-- Alerta informativa para usuarios particulares -->
        <div class="info-banner-persona">
            <div>
                <strong>Aviso de contratacion:</strong> Has iniciado sesion como <em>Cliente Particular (Persona)</em>. 
                Los planes de infraestructura tecnologica y estaciones de trabajo estan reservados para cuentas de tipo <strong>Empresa</strong>.
            </div>
        </div>

    <?php elseif (!$isLoggedIn): ?>
        <!-- Banner para usuarios no registrados / no logueados -->
        <div class="info-banner-guest">
            <div>
                <strong>Representas a una empresa?</strong> Inicia sesion o crea una cuenta corporativa para contratar y gestionar planes de infraestructura.
            </div>
            <div class="guest-cta-btns">
                <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-outline btn-sm">Iniciar Sesion</a>
                <a href="<?php echo PUBLIC_PATH; ?>/registro.php" class="btn btn-primary btn-sm">Registrar Empresa</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Grilla de Planes -->
    <section id="planes" class="planes-section">
        <h2>Catalogo de Infraestructura</h2>
        <p class="section-desc">Selecciona el paquete que mejor se adapte al volumen de tu equipo o cotiza un plan a medida con calculo en tiempo real.</p>

        <div class="planes-grid">
            
            <!-- PLAN START-UP (ID: 1) -->
            <div class="plan-card <?php echo ($currentPlanId === 1) ? 'is-current destacado' : 'destacado'; ?>">
                <?php if ($currentPlanId === 1): ?>
                    <div class="badge-current">Tu Plan Actual</div>
                <?php else: ?>
                    <div class="badge">Popular</div>
                <?php endif; ?>

                <h3>Plan Start-Up</h3>
                <p class="plan-subtitle">4 Estaciones de Trabajo</p>
                <div class="plan-price-tag">
                    <span class="price-desc">Ideal para equipos en crecimiento inicial</span>
                </div>
                <ul>
                    <li>Suministro de 4 computadoras completas de escritorio.</li>
                    <li>Instalacion y cableado estructurado Cat 6.</li>
                    <li>Red Wi-Fi corporativa de alta velocidad y switch base.</li>
                    <li>Configuracion de entorno de trabajo e impresoras.</li>
                    <li>Soporte tecnico estandar y mantenimiento preventivo.</li>
                </ul>

                <div class="plan-card-actions">
                    <!-- Botón Más Información Plan Start-Up -->
                    <button type="button" class="btn btn-outline btn-sm btn-info-plan" onclick="openPlanInfoModal('startup')">
                        Más Información
                    </button>

                    <?php if ($isEmpresa): ?>
                        <?php if ($solicitudPendiente): ?>
                            <button class="btn btn-disabled" disabled>Bloqueado (Solicitud en Revision)</button>
                        <?php elseif ($currentPlanId === 1): ?>
                            <button class="btn btn-active-plan" disabled>Plan Activo</button>
                        <?php elseif ($currentPlanId > 0): ?>
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Confirmas que deseas cambiar al Plan Start-Up?');">
                                <input type="hidden" name="action" value="contract">
                                <input type="hidden" name="plan_id" value="1">
                                <button type="submit" class="btn btn-primary btn-block">Cambiar a este Plan</button>
                            </form>
                        <?php else: ?>
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Confirmas que deseas contratar el Plan Start-Up?');">
                                <input type="hidden" name="action" value="contract">
                                <input type="hidden" name="plan_id" value="1">
                                <button type="submit" class="btn btn-primary btn-block">Contratar Plan</button>
                            </form>
                        <?php endif; ?>
                    <?php elseif ($isLoggedIn): ?>
                        <button class="btn btn-disabled" disabled>Exclusivo Empresas</button>
                    <?php else: ?>
                        <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-primary btn-block">Iniciar Sesion para Contratar</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PLAN BUSINESS (ID: 2) -->
            <div class="plan-card <?php echo ($currentPlanId === 2) ? 'is-current' : ''; ?>">
                <?php if ($currentPlanId === 2): ?>
                    <div class="badge-current">Tu Plan Actual</div>
                <?php endif; ?>

                <h3>Plan Business</h3>
                <p class="plan-subtitle">8 Estaciones + Sala de Juntas</p>
                <div class="plan-price-tag">
                    <span class="price-desc">Disenado para pymes y oficinas consolidadas</span>
                </div>
                <ul>
                    <li>Suministro de 8 computadoras de alto rendimiento.</li>
                    <li>Infraestructura completa para Sala de Juntas (Proyector/Pantalla).</li>
                    <li>Red troncal Gigabit, Switch administrable y Wi-Fi Mesh.</li>
                    <li>Configuracion de servidores locales y politicas de red.</li>
                    <li>Soporte prioritario 24/7 y asistencia remota directa.</li>
                </ul>

                <div class="plan-card-actions">
                    <!-- Botón Más Información Plan Business -->
                    <button type="button" class="btn btn-outline btn-sm btn-info-plan" onclick="openPlanInfoModal('business')">
                        Más Información
                    </button>

                    <?php if ($isEmpresa): ?>
                        <?php if ($solicitudPendiente): ?>
                            <button class="btn btn-disabled" disabled>Bloqueado (Solicitud en Revision)</button>
                        <?php elseif ($currentPlanId === 2): ?>
                            <button class="btn btn-active-plan" disabled>Plan Activo</button>
                        <?php elseif ($currentPlanId > 0): ?>
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Confirmas que deseas cambiar al Plan Business?');">
                                <input type="hidden" name="action" value="contract">
                                <input type="hidden" name="plan_id" value="2">
                                <button type="submit" class="btn btn-primary btn-block">Cambiar a este Plan</button>
                            </form>
                        <?php else: ?>
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Confirmas que deseas contratar el Plan Business?');">
                                <input type="hidden" name="action" value="contract">
                                <input type="hidden" name="plan_id" value="2">
                                <button type="submit" class="btn btn-primary btn-block">Contratar Plan</button>
                            </form>
                        <?php endif; ?>
                    <?php elseif ($isLoggedIn): ?>
                        <button class="btn btn-disabled" disabled>Exclusivo Empresas</button>
                    <?php else: ?>
                        <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-outline btn-block">Iniciar Sesion para Contratar</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PLAN CUSTOM / COTIZACION CON COTIZADOR -->
            <div class="plan-card <?php echo $solicitudPendiente ? 'is-current' : ''; ?>">
                <?php if ($solicitudPendiente): ?>
                    <div class="badge-current" style="background: linear-gradient(135deg, #d97706, #b45309);">En Revision</div>
                <?php else: ?>
                    <div class="badge" style="background: #2563eb;">Personalizado</div>
                <?php endif; ?>

                <h3>Plan Custom</h3>
                <p class="plan-subtitle">Cotizador a Medida</p>
                <div class="plan-price-tag">
                    <span class="price-desc">Arma tu configuracion con precios calculados al instante</span>
                </div>
                <ul>
                    <li><strong>1 PC / Estacion:</strong> $800 USD c/u.</li>
                    <li><strong>Impresora de red:</strong> $400 USD c/u.</li>
                    <li><strong>Puntos de red / cableado:</strong> $150 USD c/u.</li>
                    <li><strong>Servidor local / Rack:</strong> $1,200 USD c/u.</li>
                    <li><strong>Sistema Wi-Fi Mesh:</strong> $250 USD c/u.</li>
                </ul>

                <div class="plan-card-actions">
                    <!-- Botón Más Información (próximamente) -->
                    <button type="button" class="btn btn-outline btn-sm btn-info-plan" onclick="return false;">
                        Más Información
                    </button>

                    <?php if ($isEmpresa): ?>
                        <?php if ($solicitudPendiente): ?>
                            <button class="btn btn-active-plan" disabled style="background: rgba(234, 179, 8, 0.15) !important; color: #facc15 !important; border-color: rgba(234, 179, 8, 0.4) !important;">Solicitud en Revision</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-primary btn-block" onclick="openCotizacionModal()">
                                Configurar y Cotizar
                            </button>
                        <?php endif; ?>
                    <?php elseif ($isLoggedIn): ?>
                        <button class="btn btn-disabled" disabled>Exclusivo Empresas</button>
                    <?php else: ?>
                        <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-outline btn-block">Iniciar Sesion para Cotizar</a>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- Modal Informativo con Vista Isométrica 3D: Plan Start-Up -->
<div id="modalInfoStartup" class="custom-modal-overlay" style="display:none;" onclick="closePlanInfoOnBackdrop(event, 'modalInfoStartup')">
    <div class="custom-modal-content modal-info-layout">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <h3 style="margin:0;">Plan Start-Up</h3>
                <span class="badge" style="position:static; transform:none; display:inline-block; padding:3px 10px; border-radius:4px; font-size:0.75rem;">Popular</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closePlanInfoModal('modalInfoStartup')">&times;</button>
        </div>
        
        <div class="modal-info-body">
            <!-- Columna Izquierda: Información Detallada del Plan -->
            <div class="modal-info-left">
                <p class="plan-subtitle" style="font-size:1.15rem; margin-bottom:0.35rem; color:var(--primary-green);">4 Estaciones de Trabajo</p>
                <p class="price-desc" style="margin-bottom:1.2rem; font-size:0.9rem;">Ideal para equipos en crecimiento inicial</p>
                
                <h4 style="color:var(--primary-green-hover); font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.75rem;">Detalle del Paquete:</h4>
                <ul class="modal-features-list">
                    <li>Suministro de 4 computadoras completas de escritorio.</li>
                    <li>Instalacion y cableado estructurado Cat 6.</li>
                    <li>Red Wi-Fi corporativa de alta velocidad y switch base.</li>
                    <li>Configuracion de entorno de trabajo e impresoras.</li>
                    <li>Soporte tecnico estandar y mantenimiento preventivo.</li>
                </ul>

                <div class="modal-info-note">
                    <small style="color:var(--text-main); line-height:1.4; display:block;">
                        💡 <strong>Distribución Técnica:</strong> Configuración en isla de 4 puestos de trabajo conectados directamente a switch de red y punto de acceso Wi-Fi corporativo.
                    </small>
                </div>
            </div>

            <!-- Columna Derecha: Render Isométrico 3D -->
            <div class="modal-info-right">
                <div class="modal-image-wrapper">
                    <img src="<?php echo VIEWS_PATH; ?>/uploads/plan_startup_isometric.jpg" alt="Distribución 3D Isométrica Plan Start-Up" class="modal-plan-img">
                    <span class="modal-image-badge">Distribución Isométrica 3D</span>
                </div>
            </div>
        </div>

        <div class="modal-actions" style="margin-top: 1.5rem; border-top: 1px solid var(--card-border); padding-top: 1rem;">
            <button type="button" class="btn btn-outline" onclick="closePlanInfoModal('modalInfoStartup')">Cerrar</button>
            <?php if ($isEmpresa && $currentPlanId !== 1 && !$solicitudPendiente): ?>
                <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" style="margin:0; padding:0; background:transparent; border:none; box-shadow:none;">
                    <input type="hidden" name="action" value="contract">
                    <input type="hidden" name="plan_id" value="1">
                    <button type="submit" class="btn btn-primary">
                        <?php echo ($currentPlanId > 0) ? 'Cambiar a este Plan' : 'Contratar Plan'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Informativo con Vista Isométrica 3D: Plan Business -->
<div id="modalInfoBusiness" class="custom-modal-overlay" style="display:none;" onclick="closePlanInfoOnBackdrop(event, 'modalInfoBusiness')">
    <div class="custom-modal-content modal-info-layout">
        <div class="modal-header">
            <div style="display:flex; align-items:center; gap:0.6rem;">
                <h3 style="margin:0;">Plan Business</h3>
                <span class="badge" style="position:static; transform:none; display:inline-block; padding:3px 10px; border-radius:4px; font-size:0.75rem; background:linear-gradient(135deg, #10b981, #059669);">Recomendado</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closePlanInfoModal('modalInfoBusiness')">&times;</button>
        </div>
        
        <div class="modal-info-body">
            <!-- Columna Izquierda: Información Detallada del Plan -->
            <div class="modal-info-left">
                <p class="plan-subtitle" style="font-size:1.15rem; margin-bottom:0.35rem; color:var(--primary-green);">8 Estaciones + Sala de Juntas</p>
                <p class="price-desc" style="margin-bottom:1.2rem; font-size:0.9rem;">Diseñado para pymes y oficinas consolidadas</p>
                
                <h4 style="color:var(--primary-green-hover); font-size:0.9rem; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.75rem;">Detalle del Paquete:</h4>
                <ul class="modal-features-list">
                    <li><strong>8 Computadoras de alto rendimiento</strong> para el área operativa.</li>
                    <li><strong>Sala de Juntas multimedia:</strong> equipada con proyector HD, pantalla blanca retráctil y <strong>1 computadora dedicada</strong> exclusiva para operar presentaciones.</li>
                    <li><strong>Red troncal Gigabit:</strong> switch administrable de alta capacidad y sistema Wi-Fi Mesh de máxima cobertura.</li>
                    <li><strong>Servidor local y políticas de red:</strong> control de acceso, seguridad y almacenamiento compartido.</li>
                    <li><strong>Soporte prioritario 24/7</strong> con asistencia técnica remota y presencial directa.</li>
                </ul>

                <div class="modal-info-note">
                    <small style="color:var(--text-main); line-height:1.4; display:block;">
                        💡 <strong>Distribución Técnica:</strong> Zona operativa con 8 puestos en red Gigabit + Sala de conferencias independiente con sistema audiovisual autónomo (proyector + pantalla + PC de control).
                    </small>
                </div>
            </div>

            <!-- Columna Derecha: Render Isométrico 3D -->
            <div class="modal-info-right">
                <div class="modal-image-wrapper">
                    <img src="<?php echo VIEWS_PATH; ?>/uploads/plan_business_isometric.jpg" alt="Distribución 3D Isométrica Plan Business" class="modal-plan-img">
                    <span class="modal-image-badge">Distribución Isométrica 3D</span>
                </div>
            </div>
        </div>

        <div class="modal-actions" style="margin-top: 1.5rem; border-top: 1px solid var(--card-border); padding-top: 1rem;">
            <button type="button" class="btn btn-outline" onclick="closePlanInfoModal('modalInfoBusiness')">Cerrar</button>
            <?php if ($isEmpresa && $currentPlanId !== 2 && !$solicitudPendiente): ?>
                <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" style="margin:0; padding:0; background:transparent; border:none; box-shadow:none;">
                    <input type="hidden" name="action" value="contract">
                    <input type="hidden" name="plan_id" value="2">
                    <button type="submit" class="btn btn-primary">
                        <?php echo ($currentPlanId > 0) ? 'Cambiar a este Plan' : 'Contratar Plan'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Interactivo para Cotizacion Personalizada (Plan Custom) -->
<?php if ($isEmpresa): ?>
<style>
#cotizacionModal .config-item-row {
    display: flex !important;
    flex-direction: row !important;
    justify-content: space-between !important;
    align-items: center !important;
    background: rgba(13, 25, 19, 0.75) !important;
    border: 1px solid var(--card-border) !important;
    border-radius: 8px !important;
    padding: 0.75rem 1.1rem !important;
    margin-bottom: 0.65rem !important;
}

#cotizacionModal .item-info {
    flex: 1 1 auto !important;
    text-align: left !important;
}

#cotizacionModal .item-controls {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-end !important;
    gap: 1.2rem !important;
    flex: 0 0 auto !important;
}

#cotizacionModal .qty-control-wrapper {
    display: inline-flex !important;
    flex-direction: row !important;
    align-items: center !important;
    gap: 2px !important;
    background: rgba(0, 0, 0, 0.6) !important;
    padding: 2px !important;
    border-radius: 6px !important;
    border: 1px solid var(--card-border) !important;
    width: auto !important;
    height: auto !important;
}

#cotizacionModal .qty-btn {
    width: 28px !important;
    height: 28px !important;
    min-width: 28px !important;
    max-width: 28px !important;
    min-height: 28px !important;
    max-height: 28px !important;
    padding: 0 !important;
    margin: 0 !important;
    background: rgba(17, 138, 94, 0.3) !important;
    border: 1px solid var(--primary-green) !important;
    color: #fff !important;
    border-radius: 4px !important;
    font-size: 1.1rem !important;
    font-weight: 700 !important;
    cursor: pointer !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    line-height: 1 !important;
    box-sizing: border-box !important;
    flex: 0 0 28px !important;
}

#cotizacionModal .qty-btn:hover {
    background: var(--primary-green) !important;
}

#cotizacionModal .qty-input {
    width: 46px !important;
    height: 28px !important;
    min-width: 46px !important;
    max-width: 46px !important;
    min-height: 28px !important;
    max-height: 28px !important;
    padding: 0 2px !important;
    text-align: center !important;
    margin: 0 !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    border: 1px solid rgba(17, 138, 94, 0.3) !important;
    border-radius: 4px !important;
    background: #040806 !important;
    color: #fff !important;
    line-height: 28px !important;
    box-sizing: border-box !important;
    display: inline-block !important;
    flex: 0 0 46px !important;
}

#cotizacionModal .item-subtotal {
    min-width: 95px !important;
    text-align: right !important;
    font-weight: 700 !important;
    font-size: 0.95rem !important;
    color: #4ade80 !important;
    flex: 0 0 auto !important;
}
</style>
<div id="cotizacionModal" class="custom-modal-overlay" style="display:none;" onclick="closeModalOnBackdrop(event)">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h3>Cotizador de Plan Custom</h3>
            <button type="button" class="modal-close-btn" onclick="closeCotizacionModal()">&times;</button>
        </div>
        <p class="modal-subtitle">Selecciona los componentes para tu infraestructura tecnologica y obten un presupuesto estimado al instante.</p>
        
        <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" style="margin: 0; padding: 0; background: transparent; border: none; box-shadow: none; max-width: 100%;">
            <input type="hidden" name="action" value="cotizacion">

            <!-- Campo Descripcion -->
            <label for="custom_descripcion" style="margin-bottom: 0.3rem;"><strong>Descripcion del Proyecto:</strong> <span style="color:#ef4444;">*</span></label>
            <input type="text" id="custom_descripcion" name="descripcion" placeholder="Ej: Nueva sucursal para 6 puestos operativos y sala tecnica" required style="margin-bottom: 1.25rem;">

            <!-- Opciones Predefinidas de Componentes -->
            <div class="configurator-section-title">Componentes y Equipamiento</div>

            <div class="config-items-list">
                
                <!-- Opcion 1: PCs -->
                <div class="config-item-row">
                    <div class="item-info">
                        <span class="item-title">Computadoras / Estaciones de Trabajo</span>
                        <span class="item-unit-price">$800.00 USD c/u</span>
                    </div>
                    <div class="item-controls">
                        <div class="qty-control-wrapper">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_pc', -1)">-</button>
                            <input type="number" id="cant_pc" name="cant_pc" class="qty-input" min="0" max="100" value="1" oninput="calcularTotalCustom()">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_pc', 1)">+</button>
                        </div>
                        <span class="item-subtotal" id="subtotal_pc">$800.00 USD</span>
                    </div>
                </div>

                <!-- Opcion 2: Impresoras -->
                <div class="config-item-row">
                    <div class="item-info">
                        <span class="item-title">Impresoras Multifuncion de Red</span>
                        <span class="item-unit-price">$400.00 USD c/u</span>
                    </div>
                    <div class="item-controls">
                        <div class="qty-control-wrapper">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_impresoras', -1)">-</button>
                            <input type="number" id="cant_impresoras" name="cant_impresoras" class="qty-input" min="0" max="20" value="0" oninput="calcularTotalCustom()">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_impresoras', 1)">+</button>
                        </div>
                        <span class="item-subtotal" id="subtotal_impresoras">$0.00 USD</span>
                    </div>
                </div>

                <!-- Opcion 3: Puntos de Red y Cableado -->
                <div class="config-item-row">
                    <div class="item-info">
                        <span class="item-title">Puntos de Red y Cableado Estructurado</span>
                        <span class="item-unit-price">$150.00 USD c/u</span>
                    </div>
                    <div class="item-controls">
                        <div class="qty-control-wrapper">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_puntos_red', -1)">-</button>
                            <input type="number" id="cant_puntos_red" name="cant_puntos_red" class="qty-input" min="0" max="100" value="0" oninput="calcularTotalCustom()">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_puntos_red', 1)">+</button>
                        </div>
                        <span class="item-subtotal" id="subtotal_puntos_red">$0.00 USD</span>
                    </div>
                </div>

                <!-- Opcion 4: Servidores / Rack -->
                <div class="config-item-row">
                    <div class="item-info">
                        <span class="item-title">Servidor Local / Rack de Comunicaciones</span>
                        <span class="item-unit-price">$1,200.00 USD c/u</span>
                    </div>
                    <div class="item-controls">
                        <div class="qty-control-wrapper">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_servidores', -1)">-</button>
                            <input type="number" id="cant_servidores" name="cant_servidores" class="qty-input" min="0" max="10" value="0" oninput="calcularTotalCustom()">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_servidores', 1)">+</button>
                        </div>
                        <span class="item-subtotal" id="subtotal_servidores">$0.00 USD</span>
                    </div>
                </div>

                <!-- Opcion 5: Wi-Fi Mesh -->
                <div class="config-item-row">
                    <div class="item-info">
                        <span class="item-title">Sistema Wi-Fi Mesh Empresarial</span>
                        <span class="item-unit-price">$250.00 USD c/u</span>
                    </div>
                    <div class="item-controls">
                        <div class="qty-control-wrapper">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_wifi', -1)">-</button>
                            <input type="number" id="cant_wifi" name="cant_wifi" class="qty-input" min="0" max="10" value="0" oninput="calcularTotalCustom()">
                            <button type="button" class="qty-btn" onclick="adjustQty('cant_wifi', 1)">+</button>
                        </div>
                        <span class="item-subtotal" id="subtotal_wifi">$0.00 USD</span>
                    </div>
                </div>

            </div>

            <!-- Resumen Total Estimado en Vivo -->
            <div class="custom-total-card">
                <span class="total-label">Presupuesto Total Estimado:</span>
                <span class="total-amount" id="precio_total_display">$800.00 USD</span>
            </div>

            <!-- Campo Notaciones Extra -->
            <label for="custom_notas" style="margin-bottom: 0.3rem;"><strong>Notaciones y requerimientos adicionales de la empresa:</strong></label>
            <textarea id="custom_notas" name="notas_extra" rows="3" placeholder="Indica detalles particulares: marcas de preferencia, distribucion fisica, sistemas operativos, fechas tentativas de instalacion, etc." style="margin-bottom: 1.25rem;"></textarea>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeCotizacionModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="mostrarConfirmacionCotizacion()">Enviar Solicitud de Cotizacion</button>
            </div>
        </form>
    </div>
</div>

<!-- Popup de Confirmacion e Informacion antes de enviar la cotizacion -->
<div id="popupConfirmacionCotizacion" class="custom-modal-overlay" style="display:none; z-index: 2500;" onclick="closePopupOnBackdrop(event)">
    <div class="custom-modal-content" style="max-width: 520px; text-align: left; padding: 2rem;">
        <div class="modal-header">
            <h3>Confirmacion de Solicitud</h3>
            <button type="button" class="modal-close-btn" onclick="cerrarPopupConfirmacion()">&times;</button>
        </div>
        
        <div style="background: rgba(17, 138, 94, 0.1); border: 1px solid var(--primary-green); border-radius: 8px; padding: 1.1rem 1.25rem; margin: 1.2rem 0; font-size: 0.92rem; line-height: 1.5; color: var(--text-main);">
            <strong style="color: var(--primary-green-hover); display: block; margin-bottom: 0.4rem;">Proceso de Evaluacion:</strong>
            El personal de Nexora revisara la solicitud detallada para crear tu Plan Custom. Nuestro equipo se pondra en comunicacion directa con la empresa en caso de requerir precisiones tecnicas o tener dudas sobre la infraestructura solicitada.
        </div>

        <div style="background: rgba(0, 0, 0, 0.5); border: 1px solid var(--card-border); border-radius: 6px; padding: 0.9rem 1.1rem; margin-bottom: 1.4rem; font-size: 0.9rem;">
            <div style="margin-bottom: 0.3rem;"><strong>Proyecto:</strong> <span id="popup_desc_preview" style="color: var(--text-muted);"></span></div>
            <div><strong>Presupuesto estimado:</strong> <span id="popup_total_preview" style="color: #4ade80; font-weight: 700;"></span></div>
        </div>

        <div class="modal-actions" style="margin-top: 1rem;">
            <button type="button" class="btn btn-outline" onclick="cerrarPopupConfirmacion()">Modificar</button>
            <button type="button" class="btn btn-primary" onclick="confirmarYEnviarCotizacion()">Confirmar y Enviar</button>
        </div>
    </div>
</div>

<script>
const PRECIOS = {
    pc: 800,
    impresoras: 400,
    puntos_red: 150,
    servidores: 1200,
    wifi: 250
};

function formatUSD(num) {
    return '$' + num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' USD';
}

function adjustQty(inputId, delta) {
    const input = document.getElementById(inputId);
    if (!input) return;
    let current = parseInt(input.value) || 0;
    let next = current + delta;
    let min = parseInt(input.min) || 0;
    let max = parseInt(input.max) || 999;
    if (next < min) next = min;
    if (next > max) next = max;
    input.value = next;
    calcularTotalCustom();
}

function calcularTotalCustom() {
    const cantPC = Math.max(0, parseInt(document.getElementById('cant_pc').value) || 0);
    const cantImp = Math.max(0, parseInt(document.getElementById('cant_impresoras').value) || 0);
    const cantRed = Math.max(0, parseInt(document.getElementById('cant_puntos_red').value) || 0);
    const cantSrv = Math.max(0, parseInt(document.getElementById('cant_servidores').value) || 0);
    const cantWifi = Math.max(0, parseInt(document.getElementById('cant_wifi').value) || 0);

    const subPC = cantPC * PRECIOS.pc;
    const subImp = cantImp * PRECIOS.impresoras;
    const subRed = cantRed * PRECIOS.puntos_red;
    const subSrv = cantSrv * PRECIOS.servidores;
    const subWifi = cantWifi * PRECIOS.wifi;

    const total = subPC + subImp + subRed + subSrv + subWifi;

    document.getElementById('subtotal_pc').innerText = formatUSD(subPC);
    document.getElementById('subtotal_impresoras').innerText = formatUSD(subImp);
    document.getElementById('subtotal_puntos_red').innerText = formatUSD(subRed);
    document.getElementById('subtotal_servidores').innerText = formatUSD(subSrv);
    document.getElementById('subtotal_wifi').innerText = formatUSD(subWifi);

    document.getElementById('precio_total_display').innerText = formatUSD(total);
}

function openCotizacionModal() {
    document.getElementById('cotizacionModal').style.display = 'flex';
    calcularTotalCustom();
}

function closeCotizacionModal() {
    document.getElementById('cotizacionModal').style.display = 'none';
}

function closeModalOnBackdrop(event) {
    if (event.target.id === 'cotizacionModal') {
        closeCotizacionModal();
    }
}

function mostrarConfirmacionCotizacion() {
    const form = document.querySelector('#cotizacionModal form');
    const descInput = document.getElementById('custom_descripcion');
    
    if (!descInput.value.trim()) {
        descInput.focus();
        descInput.reportValidity();
        return;
    }

    const totalCalculado = document.getElementById('precio_total_display').innerText;
    document.getElementById('popup_desc_preview').innerText = descInput.value.trim();
    document.getElementById('popup_total_preview').innerText = totalCalculado;

    document.getElementById('popupConfirmacionCotizacion').style.display = 'flex';
}

function cerrarPopupConfirmacion() {
    document.getElementById('popupConfirmacionCotizacion').style.display = 'none';
}

function closePopupOnBackdrop(event) {
    if (event.target.id === 'popupConfirmacionCotizacion') {
        cerrarPopupConfirmacion();
    }
}

function confirmarYEnviarCotizacion() {
    const form = document.querySelector('#cotizacionModal form');
    if (form) {
        form.submit();
    }
}
</script>
<?php endif; ?>

<script>
function openPlanInfoModal(planKey) {
    if (planKey === 'startup') {
        const modal = document.getElementById('modalInfoStartup');
        if (modal) modal.style.display = 'flex';
    } else if (planKey === 'business') {
        const modal = document.getElementById('modalInfoBusiness');
        if (modal) modal.style.display = 'flex';
    }
}

function closePlanInfoModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'none';
}

function closePlanInfoOnBackdrop(event, modalId) {
    if (event.target.id === modalId) {
        closePlanInfoModal(modalId);
    }
}

// Cerrar con tecla Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        ['modalInfoStartup', 'modalInfoBusiness'].forEach(function(id) {
            const m = document.getElementById(id);
            if (m && m.style.display === 'flex') {
                closePlanInfoModal(id);
            }
        });
    }
});
</script>

<?php
if ($is_standalone) {
    require_once(INCLUDES_DIR . '/footer.php');
}
?>