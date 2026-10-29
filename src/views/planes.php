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
        <div class="alerta alerta--exito">
            <?php echo htmlspecialchars($success_msg); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alerta alerta--error">
            <?php echo htmlspecialchars($error_msg); ?>
        </div>
    <?php endif; ?>

    <?php if ($is_standalone): ?>
    <div class="hero-section">
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
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Confirmas que deseas solicitar este paquete de infraestructura?');">
                                <input type="hidden" name="action" value="cotizacion">
                                <input type="hidden" name="plan_id" value="1">
                                <div class="config-item-row compact">
                                    <label>Tipo</label>
                                    <select name="tipo_ordenador" class="custom-select" required>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Desarrollo">Desarrollo</option>
                                    </select>
                                </div>
                                <div class="config-item-row compact">
                                    <label>Gama</label>
                                    <select name="gama_equipo" class="custom-select" required>
                                        <option value="Baja">Baja</option>
                                        <option value="Media" selected>Media</option>
                                        <option value="Alta">Alta</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Solicitar este paquete</button>
                            </form>
                        <?php else: ?>
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Deseas enviar una solicitud para este paquete?');">
                                <input type="hidden" name="action" value="cotizacion">
                                <input type="hidden" name="plan_id" value="1">
                                <div class="config-item-row compact">
                                    <label>Tipo</label>
                                    <select name="tipo_ordenador" class="custom-select" required>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Desarrollo">Desarrollo</option>
                                    </select>
                                </div>
                                <div class="config-item-row compact">
                                    <label>Gama</label>
                                    <select name="gama_equipo" class="custom-select" required>
                                        <option value="Baja">Baja</option>
                                        <option value="Media" selected>Media</option>
                                        <option value="Alta">Alta</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Solicitar compra</button>
                            </form>
                        <?php endif; ?>
                    <?php elseif ($isLoggedIn): ?>
                        <button class="btn btn-disabled" disabled>Exclusivo Empresas</button>
                    <?php else: ?>
                        <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-primary btn-block">Iniciar Sesion para Solicitar</a>
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
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Confirmas que deseas solicitar este paquete de infraestructura?');">
                                <input type="hidden" name="action" value="cotizacion">
                                <input type="hidden" name="plan_id" value="2">
                                <div class="config-item-row compact">
                                    <label>Tipo</label>
                                    <select name="tipo_ordenador" class="custom-select" required>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Desarrollo">Desarrollo</option>
                                    </select>
                                </div>
                                <div class="config-item-row compact">
                                    <label>Gama</label>
                                    <select name="gama_equipo" class="custom-select" required>
                                        <option value="Baja">Baja</option>
                                        <option value="Media" selected>Media</option>
                                        <option value="Alta">Alta</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Solicitar este paquete</button>
                            </form>
                        <?php else: ?>
                            <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" onsubmit="return confirm('¿Deseas enviar una solicitud para este paquete?');">
                                <input type="hidden" name="action" value="cotizacion">
                                <input type="hidden" name="plan_id" value="2">
                                <div class="config-item-row compact">
                                    <label>Tipo</label>
                                    <select name="tipo_ordenador" class="custom-select" required>
                                        <option value="Oficina">Oficina</option>
                                        <option value="Desarrollo">Desarrollo</option>
                                    </select>
                                </div>
                                <div class="config-item-row compact">
                                    <label>Gama</label>
                                    <select name="gama_equipo" class="custom-select" required>
                                        <option value="Baja">Baja</option>
                                        <option value="Media" selected>Media</option>
                                        <option value="Alta">Alta</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Solicitar compra</button>
                            </form>
                        <?php endif; ?>
                    <?php elseif ($isLoggedIn): ?>
                        <button class="btn btn-disabled" disabled>Exclusivo Empresas</button>
                    <?php else: ?>
                        <a href="<?php echo PUBLIC_PATH; ?>/login.php" class="btn btn-outline btn-block">Iniciar Sesion para Solicitar</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- PLAN CUSTOM / COTIZACION CON COTIZADOR -->
            <div class="plan-card <?php echo $solicitudPendiente ? 'is-current' : ''; ?>">
                <?php if ($solicitudPendiente): ?>
                    <div class="badge-current">En Revision</div>
                <?php else: ?>
                    <div class="badge">Personalizado</div>
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
                            <button class="btn btn-active-plan" disabled>Solicitud en Revision</button>
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
<div id="modalInfoStartup" class="custom-modal-overlay" onclick="closePlanInfoOnBackdrop(event, 'modalInfoStartup')">
    <div class="custom-modal-content modal-info-layout">
        <div class="modal-header">
            <div class="modal-header-group">
                <h3 class="modal-title">Plan Start-Up</h3>
                <span class="badge">Popular</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closePlanInfoModal('modalInfoStartup')">&times;</button>
        </div>
        
        <div class="modal-info-body">
            <div class="modal-info-left">
                <p class="plan-subtitle">4 Estaciones de Trabajo</p>
                <p class="price-desc">Ideal para equipos en crecimiento inicial</p>
                
                <h4 class="modal-section-title">Detalle del Paquete:</h4>
                <ul class="modal-features-list">
                    <li>Suministro de 4 computadoras completas de escritorio.</li>
                    <li>Instalacion y cableado estructurado Cat 6.</li>
                    <li>Red Wi-Fi corporativa de alta velocidad y switch base.</li>
                    <li>Configuracion de entorno de trabajo e impresoras.</li>
                    <li>Soporte tecnico estandar y mantenimiento preventivo.</li>
                </ul>

                <div class="modal-info-note">
                    <small>
                        <strong>Distribución Técnica:</strong> Configuración en isla de 4 puestos de trabajo conectados directamente a switch de red y punto de acceso Wi-Fi corporativo.
                    </small>
                </div>
            </div>

            <div class="modal-info-right">
                <div class="modal-image-wrapper">
                    <img src="<?php echo VIEWS_PATH; ?>/uploads/plan_startup_isometric.jpg" alt="Distribución 3D Isométrica Plan Start-Up" class="modal-plan-img">
                    <span class="modal-image-badge">Distribución Isométrica 3D</span>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-outline" onclick="closePlanInfoModal('modalInfoStartup')">Cerrar</button>
            <?php if ($isEmpresa && $currentPlanId !== 1 && !$solicitudPendiente): ?>
                <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST">
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
<div id="modalInfoBusiness" class="custom-modal-overlay" onclick="closePlanInfoOnBackdrop(event, 'modalInfoBusiness')">
    <div class="custom-modal-content modal-info-layout">
        <div class="modal-header">
            <div class="modal-header-group">
                <h3 class="modal-title">Plan Business</h3>
                <span class="badge">Recomendado</span>
            </div>
            <button type="button" class="modal-close-btn" onclick="closePlanInfoModal('modalInfoBusiness')">&times;</button>
        </div>
        
        <div class="modal-info-body">
            <div class="modal-info-left">
                <p class="plan-subtitle">8 Estaciones + Sala de Juntas</p>
                <p class="price-desc">Diseñado para pymes y oficinas consolidadas</p>
                
                <h4 class="modal-section-title">Detalle del Paquete:</h4>
                <ul class="modal-features-list">
                    <li><strong>8 Computadoras de alto rendimiento</strong> para el área operativa.</li>
                    <li><strong>Sala de Juntas multimedia:</strong> equipada con proyector HD, pantalla blanca retráctil y <strong>1 computadora dedicada</strong> exclusiva para operar presentaciones.</li>
                    <li><strong>Red troncal Gigabit:</strong> switch administrable de alta capacidad y sistema Wi-Fi Mesh de máxima cobertura.</li>
                    <li><strong>Servidor local y políticas de red:</strong> control de acceso, seguridad y almacenamiento compartido.</li>
                    <li><strong>Soporte prioritario 24/7</strong> con asistencia técnica remota y presencial directa.</li>
                </ul>

                <div class="modal-info-note">
                    <small>
                        <strong>Distribución Técnica:</strong> Zona operativa con 8 puestos en red Gigabit + Sala de conferencias independiente con sistema audiovisual autónomo (proyector + pantalla + PC de control).
                    </small>
                </div>
            </div>

            <div class="modal-info-right">
                <div class="modal-image-wrapper">
                    <img src="<?php echo VIEWS_PATH; ?>/uploads/plan_business_isometric.jpg" alt="Distribución 3D Isométrica Plan Business" class="modal-plan-img">
                    <span class="modal-image-badge">Distribución Isométrica 3D</span>
                </div>
            </div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-outline" onclick="closePlanInfoModal('modalInfoBusiness')">Cerrar</button>
            <?php if ($isEmpresa && $currentPlanId !== 2 && !$solicitudPendiente): ?>
                <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST">
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

<?php if ($isEmpresa): ?>
<div id="cotizacionModal" class="custom-modal-overlay" onclick="closeModalOnBackdrop(event)">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Cotizador de Plan Custom</h3>
            <button type="button" class="modal-close-btn" onclick="closeCotizacionModal()">&times;</button>
        </div>
        <p class="modal-subtitle">Selecciona los componentes para tu infraestructura tecnologica y obten un presupuesto estimado al instante.</p>
        
        <form action="<?php echo VIEWS_PATH; ?>/contratar_plan.php" method="POST" class="modal-form">
            <input type="hidden" name="action" value="cotizacion">

            <label for="custom_descripcion"><strong>Descripcion del Proyecto:</strong> <span class="required-mark">*</span></label>
            <input type="text" id="custom_descripcion" name="descripcion" placeholder="Ej: Nueva sucursal para 6 puestos operativos y sala tecnica" required>

            <div class="configurator-section-title">Tipo de equipos y nivel de gama</div>

            <div class="config-item-row">
                <div class="item-info">
                    <span class="item-title">Tipo de ordenadores</span>
                    <span class="item-unit-price">Oficina o desarrollo</span>
                </div>
                <div class="item-controls">
                    <select name="tipo_ordenador" class="custom-select" required>
                        <option value="Oficina">Oficina</option>
                        <option value="Desarrollo">Desarrollo</option>
                    </select>
                </div>
            </div>

            <div class="config-item-row">
                <div class="item-info">
                    <span class="item-title">Gama de equipos</span>
                    <span class="item-unit-price">Baja, media o alta</span>
                </div>
                <div class="item-controls">
                    <select name="gama_equipo" class="custom-select" required>
                        <option value="Baja">Baja</option>
                        <option value="Media" selected>Media</option>
                        <option value="Alta">Alta</option>
                    </select>
                </div>
            </div>

            <div class="configurator-section-title">Componentes y Equipamiento</div>

            <div class="config-items-list">
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

            <div class="custom-total-card">
                <span class="total-label">Presupuesto Total Estimado:</span>
                <span class="total-amount" id="precio_total_display">$800.00 USD</span>
            </div>

            <label for="custom_notas"><strong>Notaciones y requerimientos adicionales de la empresa:</strong></label>
            <textarea id="custom_notas" name="notas_extra" rows="3" placeholder="Indica detalles particulares: marcas de preferencia, distribucion fisica, sistemas operativos, fechas tentativas de instalacion, etc."></textarea>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeCotizacionModal()">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="mostrarConfirmacionCotizacion()">Enviar Solicitud de Cotizacion</button>
            </div>
        </form>
    </div>
</div>

<div id="popupConfirmacionCotizacion" class="custom-modal-overlay" onclick="closePopupOnBackdrop(event)">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Confirmacion de Solicitud</h3>
            <button type="button" class="modal-close-btn" onclick="cerrarPopupConfirmacion()">&times;</button>
        </div>
        
        <div class="confirmacion-cotizacion-box">
            <strong>Proceso de Evaluacion:</strong>
            El personal de Nexora revisara la solicitud detallada para crear tu Plan Custom. Nuestro equipo se pondra en comunicacion directa con la empresa en caso de requerir precisiones tecnicas o tener dudas sobre la infraestructura solicitada.
        </div>

        <div class="confirmacion-cotizacion-resumen">
            <div><strong>Proyecto:</strong> <span id="popup_desc_preview"></span></div>
            <div><strong>Presupuesto estimado:</strong> <span id="popup_total_preview"></span></div>
        </div>

        <div class="modal-actions">
            <button type="button" class="btn btn-outline" onclick="cerrarPopupConfirmacion()">Modificar</button>
            <button type="button" class="btn btn-primary" onclick="confirmarYEnviarCotizacion()">Confirmar y Enviar</button>
        </div>
    </div>
</div>
<?php endif; ?>

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