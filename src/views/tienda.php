<?php
$require_login = true;
$page_title = 'Panel de Control - Proyecto 3ro';
require_once(__DIR__ . '/../../includes/config.php');
require_once(INCLUDES_DIR . '/header.php');
// Definir si el usuario tiene permisos de administrador (Rol 2 o 3)
$es_admin = isset($_SESSION['usuario_rol']) && in_array($_SESSION['usuario_rol'], [2, 3]);
?>

<head></head>
<body></body>



<div>
    <h1>Bienvenido a la Tienda</h1>
    <p>Explora nuestros productos y ofertas exclusivas.</p>


<?php require_once(__DIR__ . '/../../includes/footer.php'); ?>