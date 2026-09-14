<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
require_once(__DIR__ . '/../models/plan.php');

$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
$es_empresa = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'Empresa';
?>

<div class="pagina-cuenta">
    <div class="encabezado-pagina">
        <div>
            <p class="texto-previo">Cuenta</p>
            <h1>Mi Perfil</h1>
            <p class="descripcion-pagina">Administra tus datos y consulta las herramientas de tu cuenta.</p>
        </div>
        <a class="btn btn-danger" href="<?php echo PUBLIC_PATH; ?>/logout.php">Cerrar Sesión</a>
    </div>

    <section class="feature-card tarjeta-cuenta resumen-cuenta">
        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario'); ?>!</h2>
        <div class="datos-cuenta">
            <p><strong>ID de Usuario:</strong> #<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></p>
        </div>
    </section>

    <?php if ($es_empresa):
        $planModel = new Plan($pdo);
        $userPlan = $planModel->getUserPlan((int)$_SESSION['user_id']);
    ?>
        <section class="feature-card tarjeta-cuenta resumen-plan">
            <h3>Infraestructura y Plan</h3>
            <p><strong>Plan actual:</strong> <?php echo htmlspecialchars($userPlan['Nombre_Plan']); ?></p>
            <p class="texto-atenuado"><?php echo htmlspecialchars($userPlan['Descripcion'] ?? ''); ?></p>
            <?php if (!empty($userPlan['Fecha_Contratacion'])): ?>
                <p class="fecha-plan">
                    <strong>Contratado el:</strong> <?php echo date('d/m/Y H:i', strtotime($userPlan['Fecha_Contratacion'])); ?> hs
                </p>
            <?php endif; ?>
            <div class="acciones-cuenta">
                <a href="<?php echo VIEWS_PATH; ?>/planes.php" class="btn btn-outline btn-sm">Gestionar o Cambiar Plan</a>
            </div>
        </section>
    <?php endif; ?>

    <section class="consumo-calculadora" aria-labelledby="consumo-calculadora-titulo">
        <div class="consumo-calculadora-header">
            <div>
                <p class="consumo-calculadora-eyebrow">Consumo energético</p>
                <h2 id="consumo-calculadora-titulo">Calculadora de consumo de oficina</h2>
                <p>Estima el consumo de tus equipos por hora a partir de la cantidad de PCs.</p>
            </div>
            <span class="consumo-calculadora-icon" aria-hidden="true">W</span>
        </div>

        <form id="consumo-form" class="consumo-form">
            <div class="consumo-campo">
                <label for="cantidad-pcs">Cantidad de PCs</label>
                <input id="cantidad-pcs" name="cantidad-pcs" type="number" min="1" step="1" value="1" required>
            </div>
            <div class="consumo-campo">
                <label for="watts-por-pc">Consumo por hora (W por PC)</label>
                <input id="watts-por-pc" name="watts-por-pc" type="number" min="1" step="1" value="200" required>
            </div>
            <button class="btn btn-primary" type="submit">Calcular consumo</button>
        </form>

        <div id="consumo-resultado" class="consumo-resultado" role="status" aria-live="polite">
            <span class="consumo-resultado-label">Consumo estimado por hora</span>
            <strong><span id="consumo-total-watts">200</span> W</strong>
            <span class="consumo-resultado-kilowatts">(<span id="consumo-total-kilowatts">0.20</span> kWh)</span>
        </div>
    </section>

    <div class="rejilla-secundaria">
        <section class="feature-card tarjeta-cuenta tarjeta-vacia">
            <p class="texto-previo">Personalización</p>
            <h2>Foto de perfil</h2>
            <p>La gestión de tu imagen de perfil estará disponible próximamente.</p>
        </section>

        <section class="feature-card tarjeta-cuenta tarjeta-soporte">
            <p class="texto-previo">Ayuda</p>
            <h2>Soporte</h2>
            <p>Si tienes dudas o necesitas ayuda, contáctate con nuestro equipo.</p>
            <a class="btn btn-outline btn-sm" href="<?php echo PUBLIC_PATH; ?>/contacto.php">Contactar Soporte</a>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const consumoForm = document.getElementById('consumo-form');
    const cantidadPcs = document.getElementById('cantidad-pcs');
    const wattsPorPc = document.getElementById('watts-por-pc');
    const totalWatts = document.getElementById('consumo-total-watts');
    const totalKilowatts = document.getElementById('consumo-total-kilowatts');

    consumoForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const consumoTotal = Number(cantidadPcs.value) * Number(wattsPorPc.value);
        totalWatts.textContent = consumoTotal.toLocaleString('es-ES');
        totalKilowatts.textContent = (consumoTotal / 1000).toLocaleString('es-ES', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    });
});
</script>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>