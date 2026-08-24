<?php
require_once(__DIR__ . '/../../includes/config.php');
require_once(__DIR__ . '/../models/User.php');

$user = new User($pdo);

/**
 * Funciones de validación reutilizables
 */
function validarPassword(string $password): array {
    $errors = [];
    if (strlen($password) < 8) $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    if (!preg_match('/[A-Z]/', $password)) $errors[] = 'Debe contener al menos una letra mayúscula.';
    if (!preg_match('/[a-z]/', $password)) $errors[] = 'Debe contener al menos una letra minúscula.';
    if (!preg_match('/[0-9]/', $password)) $errors[] = 'Debe contener al menos un número.';
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};.:,<>?]/', $password)) $errors[] = 'Debe contener al menos un carácter especial.';
    return $errors; 
}

function camposVacios(array $required, array $data): array {
    $missing = [];
    foreach ($required as $key) {
        if (!isset($data[$key]) || trim((string)$data[$key]) === '') {
            $missing[] = $key;
        }
    }
    return $missing;
}

function verificarUnicidadEmpresa(PDO $pdo, array $datos): array {
    $conflictos = [];

    // Email
    if (!empty($datos['email'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Email = ? AND Tipo_Persona = 'Empresa'");
        $stmt->execute([$datos['email']]);
        if ($stmt->fetchColumn() > 0) $conflictos[] = 'email';
    }

    // Teléfono
    if (!empty($datos['telefono'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Telefono = ? AND Tipo_Persona = 'Empresa'");
        $stmt->execute([$datos['telefono']]);
        if ($stmt->fetchColumn() > 0) $conflictos[] = 'telefono';
    }

    // RUT
    if (!empty($datos['rut'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM empresa WHERE RUT = ?");
        $stmt->execute([$datos['rut']]);
        if ($stmt->fetchColumn() > 0) $conflictos[] = 'rut';
    }

    // Nombre empresa
    if (!empty($datos['nombre_empresa'])) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM empresa WHERE Nombre_Empresa = ?");
        $stmt->execute([$datos['nombre_empresa']]);
        if ($stmt->fetchColumn() > 0) $conflictos[] = 'nombre_empresa';
    }

    return $conflictos;
}

/**
 * Manejo de solicitudes
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'login') {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            header('Location: ' . PUBLIC_PATH . '/login.php?error=campos_vacios');
            exit();
        }

        $pwErr = validarPassword($password);
        if (!empty($pwErr)) {
            $mensaje = urlencode(implode(' ', $pwErr));
            header('Location: ' . PUBLIC_PATH . '/login.php?error=password_invalido&mensaje=' . $mensaje);
            exit();
        }

        if ($user->login($email, $password)) {
            header('Location: ' . PUBLIC_PATH . '/../src/views/welcome.php');
            exit();
        }

        header('Location: ' . PUBLIC_PATH . '/login.php?error=credenciales_invalidas');
        exit();
    }

    if ($action === 'register') {
        $tipo_usuario = strtolower(trim($_POST['tipo_usuario'] ?? 'empresa'));

        if ($tipo_usuario === 'persona') {
            $datos = [
                'email'    => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
                'nombre'   => htmlspecialchars($_POST['nombre'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'telefono' => htmlspecialchars($_POST['telefono'] ?? '')
            ];

            // Validaciones
            if (!empty(camposVacios(['email','nombre','password','telefono'], $datos))) {
                header('Location: ' . PUBLIC_PATH . '/registro.php?error=campos_vacios');
                exit();
            }

            $pwErr = validarPassword($datos['password']);
            if (!empty($pwErr)) {
                $mensaje = urlencode(implode(' ', $pwErr));
                header('Location: ' . PUBLIC_PATH . '/registro.php?error=password_invalido&mensaje=' . $mensaje);
                exit();
            }

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE Email = ?");
            $stmt->execute([$datos['email']]);
            if ($stmt->fetchColumn() > 0) {
                header('Location: ' . PUBLIC_PATH . '/registro.php?error=email_usado');
                exit();
            }

            if ($user->registerUnipersonal($datos)) {
                header('Location: ' . PUBLIC_PATH . '/login.php?registro=exito');
                exit();
            }

            $mensaje = urlencode($user->getLastError() ?: 'Error al registrar usuario personal.');
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=registro_fallido&mensaje=' . $mensaje);
            exit();
        }

        if ($tipo_usuario === 'empresa') {
        $datos = [
            'email'          => filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL),
            'nombre_empresa' => htmlspecialchars($_POST['nombre_empresa'] ?? ''),
            'password'       => $_POST['password'] ?? '',
            'telefono'       => htmlspecialchars($_POST['telefono'] ?? ''),
            'rut'            => htmlspecialchars($_POST['rut'] ?? ''),
            'calle'      => htmlspecialchars($_POST['calle'] ?? ''),
            'esquina'      => htmlspecialchars($_POST['esquina'] ?? ''),
            'num-puerta'      => htmlspecialchars($_POST['num-puerta'] ?? ''),
            'categoria'      => htmlspecialchars($_POST['categoria'] ?? ''),
            'tamano'      => htmlspecialchars($_POST['tamano'] ?? ''),
            'descripcion'    => htmlspecialchars($_POST['descripcion'] ?? ''),
            'rep_nombre'     => htmlspecialchars($_POST['rep_nombre'] ?? ''),
            'rep_cargo'      => htmlspecialchars($_POST['rep_cargo'] ?? ''),
            'rep_email'      => filter_var($_POST['rep_email'] ?? '', FILTER_SANITIZE_EMAIL),
            'rep_telefono'   => htmlspecialchars($_POST['rep_telefono'] ?? '')
        ];

            // Validaciones
            if (!empty(camposVacios(
                ['email','nombre_empresa','password','telefono','rut','calle', 'esquina', 'num-puerta','categoria', 'tamano','descripcion','rep_nombre','rep_cargo','rep_email','rep_telefono'],
                $datos
            ))) {
                header('Location: ' . PUBLIC_PATH . '/registro.php?error=campos_vacios');
                exit();
            }

            $pwErr = validarPassword($datos['password']);
            if (!empty($pwErr)) {
                $mensaje = urlencode(implode(' ', $pwErr));
                header('Location: ' . PUBLIC_PATH . '/registro.php?error=password_invalido&mensaje=' . $mensaje);
                exit();
            }

            $conflicts = verificarUnicidadEmpresa($pdo, $datos);
            if (!empty($conflicts)) {
                $mensaje = 'Conflictos en: ' . implode(', ', $conflicts);
                header('Location: ' . PUBLIC_PATH . '/registro.php?error=registro_fallido&mensaje=' . urlencode($mensaje));
                exit();
            }

            if ($user->registerEmpresa($datos)) {
                header('Location: ' . PUBLIC_PATH . '/login.php?registro=exito');
                exit();
            }

            $mensaje = urlencode($user->getLastError() ?: 'Error al registrar la empresa.');
            header('Location: ' . PUBLIC_PATH . '/registro.php?error=registro_fallido&mensaje=' . $mensaje);
            exit();
        }
    }
}

// Si acceden por GET u otro método
header('Location: ' . PUBLIC_PATH . '/login.php');
exit();
