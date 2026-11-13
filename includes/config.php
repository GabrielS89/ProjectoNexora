<?php

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require_once(__DIR__ . '/../database/database.php');

// Calcular la URL base de forma automática para local y hosting
$baseUrl = getenv('BASE_URL');
if ($baseUrl === false || trim((string)$baseUrl) === '') {
    $docRoot = rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
    $projectDir = rtrim((string)dirname(__DIR__), '/');

    if ($docRoot !== '' && strpos($projectDir, $docRoot) === 0) {
        $relative = str_replace($docRoot, '', $projectDir);
        $baseUrl = $relative !== '' ? $relative : '';
    } else {
        $baseUrl = '/Proyecto3ro';
    }
}

// Definir URLs base
define('BASE_URL', $baseUrl);
define('PUBLIC_PATH', BASE_URL . '/public');
define('INCLUDES_PATH', BASE_URL . '/includes');
define('VIEWS_PATH', BASE_URL . '/src/views');

// Definir rutas de sistema (paths) reutilizables para includes y public
define('ROOT_DIR', dirname(__DIR__));
define('INCLUDES_DIR', __DIR__);
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('MODELS_PATH', ROOT_DIR . '/src/models');
define('CONTROLLERS_PATH', ROOT_DIR . '/src/controllers');

// Función para verificar si el usuario está logueado
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función para obtener el usuario actual
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        return array(
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'] ?? null,
            'nombre' => $_SESSION['nombre'] ?? null,
            'tipo_usuario' => $_SESSION['tipo_usuario'] ?? null
        );
    }
    return null;
}

// Función para cerrar sesión
function logout() {
    session_destroy();
    header('Location: ' . PUBLIC_PATH . '/index.php?logout=exito');
    exit();
}
?>
