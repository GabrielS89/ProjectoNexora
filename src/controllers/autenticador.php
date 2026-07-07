<?php
require_once(__DIR__ . '/../../includes/config.php');
require_once(__DIR__ . '/../models/User.php');

$user = new User($pdo);

// Función para validar contraseña
function validarPassword($password) {
    $errores = [];
    
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra mayúscula";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errores[] = "La contraseña debe contener al menos una letra minúscula";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errores[] = "La contraseña debe contener al menos un número";
    }
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};:\'",.<>?\\/]/', $password)) {
        $errores[] = "La contraseña debe contener al menos un carácter especial (!@#$%^&*...)";
    }
    
    return $errores;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'login') {
        // Manejo de login
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        // Validación básica
        if (empty($email) || empty($password)) {
            header('Location: ' . PUBLIC_PATH . '/login.php?error=campos_vacios');
            exit();
        }
        
        // Validar formato de contraseña
        $erroresPassword = validarPassword($password);
        if (!empty($erroresPassword)) {
            $mensajeError = urlencode(implode(". ", $erroresPassword));
            header('Location: ' . PUBLIC_PATH . '/login.php?error=password_invalido&mensaje=' . $mensajeError);
            exit();
        }
        
        // Intentar login
        if ($user->login($email, $password)) {
            header('Location: ' . PUBLIC_PATH . '/../src/views/welcome.php');
            exit();
        } else {
            header('Location: ' . PUBLIC_PATH . '/login.php?error=credenciales_invalidas');
            exit();
        }
        
    } elseif ($_POST['action'] === 'register') {
    
    $tipo_usuario = isset($_POST['tipo_usuario']) ? strtolower(trim($_POST['tipo_usuario'])) : 'empresa';

    if ($tipo_usuario === 'persona') {
        // Registro Persona
        $datosRegistro = [
            'email'    => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'nombre'   => htmlspecialchars($_POST['nombre']),
            'password' => $_POST['password'],
            'telefono' => htmlspecialchars($_POST['telefono']),
            'tipo'     => 'Persona'
        ];

        // Validación básica
        if (empty($datosRegistro['email']) || empty($datosRegistro['password']) || 
            empty($datosRegistro['nombre']) || empty($datosRegistro['telefono'])) {
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=campos_vacios');
            exit();
        }

        // Validar contraseña
        $erroresPassword = validarPassword($datosRegistro['password']);
        if (!empty($erroresPassword)) {
            $mensajeError = urlencode(implode(". ", $erroresPassword));
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=password_invalido&mensaje=' . $mensajeError);
            exit();
        }

        // Intentar registro
        if ($user->registerUnipersonal($datosRegistro)) {
            header('Location: ' . PUBLIC_PATH . '/login.php?registro=exito');
            exit();
        } else {
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=registro_fallido');
            exit();
        }

    } elseif ($tipo_usuario === 'empresa') {
        // Registro Empresa
        $datosRegistro = [
            'email'           => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'nombre_empresa'  => htmlspecialchars($_POST['nombre_empresa']),
            'password'        => $_POST['password'],
            'telefono'        => htmlspecialchars($_POST['telefono']),
            'rut'             => htmlspecialchars($_POST['rut']),
            'direccion'       => htmlspecialchars($_POST['direccion']),
            'sector'          => htmlspecialchars($_POST['sector']),
            'descripcion'     => htmlspecialchars($_POST['descripcion']),
            'rep_nombre'      => htmlspecialchars($_POST['rep_nombre']),
            'rep_cargo'       => htmlspecialchars($_POST['rep_cargo']),
            'rep_email'       => filter_var($_POST['rep_email'], FILTER_SANITIZE_EMAIL),
            'rep_telefono'    => htmlspecialchars($_POST['rep_telefono'])
        ];

        // Validación básica
        if (empty($datosRegistro['email']) || empty($datosRegistro['password']) || empty($datosRegistro['rut']) ||
            empty($datosRegistro['nombre_empresa']) || empty($datosRegistro['telefono']) || empty($datosRegistro['direccion']) ||
            empty($datosRegistro['sector']) || empty($datosRegistro['descripcion']) || empty($datosRegistro['rep_nombre']) ||
            empty($datosRegistro['rep_cargo']) || empty($datosRegistro['rep_email']) || empty($datosRegistro['rep_telefono'])) {
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=campos_vacios');
            exit();
        }

        // Validar contraseña
        $erroresPassword = validarPassword($datosRegistro['password']);
        if (!empty($erroresPassword)) {
            $mensajeError = urlencode(implode(". ", $erroresPassword));
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=password_invalido&mensaje=' . $mensajeError);
            exit();
        }

        // Intentar registro
        if ($user->register($datosRegistro)) {
            header('Location: ' . PUBLIC_PATH . '/login.php?registro=exito');
            exit();
        } else {
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=registro_fallido');
            exit();
        }
    }
}

} else {
    // Si se accede directamente sin POST
    header('Location: ' . PUBLIC_PATH . '/login.php');
    exit();
}
?>