<?php
require_once '../../database/database.php';
require_once '../models/User.php';

$user = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    if ($_POST['action'] === 'login') {
        // Manejo de login
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        // Validación básica
        if (empty($email) || empty($password)) {
            header('Location: /git/login/Proyecto3ro/public/login.php?error=campos_vacios');
            exit();
        }
        
        // Intentar login
        if ($user->login($email, $password)) {
            header('Location: /git/login/Proyecto3ro/public/welcome.php');
            exit();
        } else {
            header('Location: /git/login/Proyecto3ro/public/login.php?error=credenciales_invalidas');
            exit();
        }
        
    } elseif ($_POST['action'] === 'register') {
        
        $tipo_usuario = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : 'empresa';
        
        if ($tipo_usuario === 'unipersonal') {
            // Registro Unipersonal
            $datosRegistro = [
                'email'    => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'nombre'   => htmlspecialchars($_POST['nombre']),
                'password' => $_POST['password'],
                'telefono' => htmlspecialchars($_POST['telefono']),
                'tipo'     => 'Unipersonal'
            ];

            // Validación básica
            if (empty($datosRegistro['email']) || empty($datosRegistro['password']) || empty($datosRegistro['nombre'])) {
                header('Location: /git/login/Proyecto3ro/public/registro_unipersonal.php?error=campos_vacios');
                exit();
            }

            // Intentar registro unipersonal
            if ($user->registerUnipersonal($datosRegistro)) {
                header('Location: /git/login/Proyecto3ro/public/login.php?registro=exito');
                exit();
            } else {
                header('Location: /git/login/Proyecto3ro/public/registro_unipersonal.php?error=registro_fallido');
                exit();
            }
        } else {
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

            // Validación básica: campos obligatorios
            if (empty($datosRegistro['email']) || empty($datosRegistro['password']) || empty($datosRegistro['rut'])) {
                header('Location: /git/login/Proyecto3ro/public/registro_empresa.php?error=campos_vacios');
                exit();
            }

            // Intentar realizar el registro a través del modelo
            if ($user->register($datosRegistro)) {
                header('Location: /git/login/Proyecto3ro/public/login.php?registro=exito');
                exit();
            } else {
                header('Location: /git/login/Proyecto3ro/public/registro_empresa.php?error=registro_fallido');
                exit();
            }
        }
    }
} else {
    // Si se accede directamente sin POST
    header('Location: /git/login/Proyecto3ro/public/login.php');
    exit();
}
?>