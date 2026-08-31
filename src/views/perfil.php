<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
require_once(__DIR__ . '/../models/plan.php');
// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);

$es_empresa = isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === "Empresa";
?>

<div>
    <h1>Mi Perfil</h1>
    <a href="<?php echo PUBLIC_PATH; ?>/logout.php">Cerrar Sesión</a>
</div>

<div>
    <div>
        <h2>¡Bienvenido, <?php echo htmlspecialchars($_SESSION['email'] ?? 'Usuario'); ?>!</h2>
        
        <div>
            <p><strong>ID de Usuario:</strong> #<?php echo $_SESSION['user_id'] ?? 'N/A'; ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'N/A'); ?></p>
            <p><strong>Tipo de Usuario:</strong> <?php echo ucfirst($_SESSION['tipo_usuario'] ?? 'N/A'); ?></p>
        </div>
    </div>
<br>

    <?php if ($es_empresa): 
        $planModel = new Plan($pdo);
        $userPlan = $planModel->getUserPlan((int)$_SESSION['user_id']);
    ?>
        <div class="user-plan-card">
            <h3>Infraestructura y Plan</h3>
            <p><strong>Plan actual:</strong> <?php echo htmlspecialchars($userPlan['Nombre_Plan']); ?></p>
            <p class="text-muted-custom font-09rem"><?php echo htmlspecialchars($userPlan['Descripcion'] ?? ''); ?></p>
            <?php if (!empty($userPlan['Fecha_Contratacion'])): ?>
                <p class="font-088rem text-muted-custom mt-04rem">
                    <strong>Contratado el:</strong> <?php echo date('d/m/Y H:i', strtotime($userPlan['Fecha_Contratacion'])); ?> hs
                </p>
            <?php endif; ?>
            <div class="mt-1rem">
                <a href="<?php echo VIEWS_PATH; ?>/planes.php" class="btn btn-outline btn-sm">Gestionar o Cambiar Plan</a>
            </div>
        </div>
    <?php endif; ?>

    








    </div>


    <br>
    <div>

    <h2>Actualice su Foto de Perfil</h2>
    <p>Sube una nueva foto de perfil para personalizar tu cuenta.</p>

    </div>
    <div>


        <br>

        
        <br>

        <div>
            <h3>Configuración</h3>
            <p>Ajusta tus preferencias de privacidad y notificaciones del sistema.</p>
            <a href="<?php echo VIEWS_PATH; ?>/settings.php">Ir a Configuración</a>
        </div>
        
        <br>

        <div>
            <h3>Soporte</h3>
            <p>Si tienes dudas o necesitas ayuda, contáctate con nuestro equipo de soporte.</p>
            <a href="<?php echo PUBLIC_PATH; ?>/contacto.php">Contactar Soporte</a>
        </div>
    </div>
</div>

<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>
