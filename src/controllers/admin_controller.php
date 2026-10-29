<?php
// Controlador para las acciones del Panel de Administración (CRUD de Usuarios)
require_once(__DIR__ . '/../../includes/config.php');
require_once(__DIR__ . '/../models/User.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar permisos de administrador (Rol 2 o 3)
if (!isset($_SESSION['usuario_rol']) || !in_array($_SESSION['usuario_rol'], [2, 3])) {
    header('Location: ' . PUBLIC_PATH . '/index.php?error=acceso_restringido');
    exit();
}

// Inicializar conexión y modelo
if (!isset($pdo)) {
    require_once(ROOT_DIR . '/database/database.php');
}
$userModel = new User($pdo);

$redirectUrl = VIEWS_PATH . '/adminpanel.php';

if (isset($_GET['action']) && $_GET['action'] === 'get_user_company' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $company = $userModel->getCompanyByUserId($id);
    header('Content-Type: application/json');
    echo json_encode($company ?: []);
    exit();
}

// PROCESAR PETICIONES DE CREAR / EDITAR / ELIMINAR
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    $action = $_POST['action'];

    // 1. CREAR NUEVO USUARIO DESDE EL ADMIN
    if ($action === 'create_user') {
        $nombre       = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $email        = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $telefono     = htmlspecialchars(trim($_POST['telefono'] ?? ''));
        $password     = $_POST['password'] ?? '';
        $tipo_persona = $_POST['tipo_persona'] ?? 'Persona';
        $id_rol       = (int)($_POST['id_rol'] ?? 1);

        $nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
        $rut            = trim($_POST['rut'] ?? '');
        $calle          = trim($_POST['calle'] ?? '');
        $esquina        = trim($_POST['esquina'] ?? '');
        $numpuerta      = trim($_POST['numpuerta'] ?? '');
        $descripcion    = trim($_POST['descripcion'] ?? '');
        $categoria      = trim($_POST['categoria'] ?? '');
        $tamano         = trim($_POST['tamano'] ?? '');
        $rep_nombre     = trim($_POST['rep_nombre'] ?? '');
        $rep_cargo      = trim($_POST['rep_cargo'] ?? '');
        $rep_email      = trim($_POST['rep_email'] ?? '');
        $rep_telefono   = trim($_POST['rep_telefono'] ?? '');

        if ($tipo_persona === 'Empresa' && (empty($nombre_empresa) || empty($rut) || empty($rep_nombre) || empty($rep_email))) {
            header('Location: ' . $redirectUrl . '?error=datos_empresa_incompletos');
            exit();
        }

        if (empty($nombre) || empty($email) || empty($password)) {
            header('Location: ' . $redirectUrl . '?error=campos_vacios');
            exit();
        }

        $datos = [
            'nombre'        => $nombre,
            'email'         => $email,
            'telefono'      => $telefono,
            'password'      => $password,
            'tipo_persona'  => $tipo_persona,
            'id_rol'        => $id_rol,
            'nombre_empresa'=> $nombre_empresa,
            'rut'           => $rut,
            'calle'         => $calle,
            'esquina'       => $esquina,
            'numpuerta'     => $numpuerta,
            'descripcion'   => $descripcion,
            'categoria'     => $categoria,
            'tamano'        => $tamano,
            'rep_nombre'    => $rep_nombre,
            'rep_cargo'     => $rep_cargo,
            'rep_email'     => $rep_email,
            'rep_telefono'  => $rep_telefono
        ];

        if ($userModel->createUserByAdmin($datos)) {
            header('Location: ' . $redirectUrl . '?msg=usuario_creado');
            exit();
        } else {
            header('Location: ' . $redirectUrl . '?error=error_creacion');
            exit();
        }
    }

    // 2. MODIFICAR / ACTUALIZAR USUARIO EXISTENTE
    elseif ($action === 'update_user') {
        $id_usuario   = (int)($_POST['id_usuario'] ?? 0);
        $nombre       = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $email        = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $telefono     = htmlspecialchars(trim($_POST['telefono'] ?? ''));
        $password     = $_POST['password'] ?? ''; // Opcional si desea cambiar la clave
        $tipo_persona = $_POST['tipo_persona'] ?? 'Persona';
        $id_rol       = (int)($_POST['id_rol'] ?? 1);

        $nombre_empresa = trim($_POST['nombre_empresa'] ?? '');
        $rut            = trim($_POST['rut'] ?? '');
        $calle          = trim($_POST['calle'] ?? '');
        $esquina        = trim($_POST['esquina'] ?? '');
        $numpuerta      = trim($_POST['numpuerta'] ?? '');
        $descripcion    = trim($_POST['descripcion'] ?? '');
        $categoria      = trim($_POST['categoria'] ?? '');
        $tamano         = trim($_POST['tamano'] ?? '');
        $rep_nombre     = trim($_POST['rep_nombre'] ?? '');
        $rep_cargo      = trim($_POST['rep_cargo'] ?? '');
        $rep_email      = trim($_POST['rep_email'] ?? '');
        $rep_telefono   = trim($_POST['rep_telefono'] ?? '');

        if ($tipo_persona === 'Empresa' && (empty($nombre_empresa) || empty($rut) || empty($rep_nombre) || empty($rep_email))) {
            header('Location: ' . $redirectUrl . '?error=datos_empresa_incompletos');
            exit();
        }

        if ($id_usuario <= 0 || empty($nombre) || empty($email)) {
            header('Location: ' . $redirectUrl . '?error=campos_vacios');
            exit();
        }

        $datos = [
            'nombre'        => $nombre,
            'email'         => $email,
            'telefono'      => $telefono,
            'password'      => $password,
            'tipo_persona'  => $tipo_persona,
            'id_rol'        => $id_rol,
            'nombre_empresa'=> $nombre_empresa,
            'rut'           => $rut,
            'calle'         => $calle,
            'esquina'       => $esquina,
            'numpuerta'     => $numpuerta,
            'descripcion'   => $descripcion,
            'categoria'     => $categoria,
            'tamano'        => $tamano,
            'rep_nombre'    => $rep_nombre,
            'rep_cargo'     => $rep_cargo,
            'rep_email'     => $rep_email,
            'rep_telefono'  => $rep_telefono
        ];

        if ($userModel->updateUserByAdmin($id_usuario, $datos)) {
            header('Location: ' . $redirectUrl . '?msg=usuario_actualizado');
            exit();
        } else {
            header('Location: ' . $redirectUrl . '?error=error_actualizacion');
            exit();
        }
    }
}

// 3. ELIMINAR USUARIO (VÍA GET O POST)
if (isset($_GET['action']) && $_GET['action'] === 'delete_user' && isset($_GET['id'])) {
    $id_usuario = (int)$_GET['id'];

    // Evitar que el admin se elimine a sí mismo
    if ($id_usuario === (int)$_SESSION['user_id']) {
        header('Location: ' . $redirectUrl . '?error=auto_eliminacion');
        exit();
    }

    if ($id_usuario > 0 && $userModel->deleteUserByAdmin($id_usuario)) {
        header('Location: ' . $redirectUrl . '?msg=usuario_eliminado');
        exit();
    } else {
        header('Location: ' . $redirectUrl . '?error=error_eliminacion');
        exit();
    }
}

header('Location: ' . $redirectUrl);
exit();
