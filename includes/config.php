<?php

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión a la base de datos
require_once(__DIR__ . '/../database/database.php');

// Definir URLs base
define('BASE_URL', 'http://localhost//git/Proyecto3ro');
define('PUBLIC_PATH', BASE_URL . '/public');
define('INCLUDES_PATH', BASE_URL . '/includes');
define('VIEWS_PATH', BASE_URL . '/src/views');

// Definir rutas de sistema (paths) reutilizables para includes y public

define('ROOT_DIR', dirname(__DIR__));
define('INCLUDES_DIR', __DIR__); // directorio físico de includes
define('PUBLIC_DIR', ROOT_DIR . '/public');

// Función auxiliar para verificar si el usuario está logueado
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Función auxiliar para obtener el usuario actual
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
