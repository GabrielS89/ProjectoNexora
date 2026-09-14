<?php
// Script automático para crear e instalar la base de datos nexorabd con datos por defecto
if (file_exists(__DIR__ . 'C:\xampp\htdocs\Proyecto3ro\database\instalar_bd.php')) {
    require_once(__DIR__ . 'C:\xampp\htdocs\Proyecto3ro\database\instalar_bd.php');
}

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'nexorabd';

try {
    // Conexión sin seleccionar base de datos
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Leer el archivo SQL
    $sqlFile = __DIR__ . '/script bd.sql';
    if (!file_exists($sqlFile)) {
        die("Error: No se encontró el archivo 'script bd.sql'");
    }

    $sql = file_get_contents($sqlFile);

    // Ejecutar el script SQL completo
    $pdo->exec($sql);

    // Re-conectar a la base de datos nexorabd
    $pdoDb = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdoDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Hashear contraseñas exactas con la versión local de PHP para garantizar login perfecto
    $passAdmin = password_hash('Admin123!', PASSWORD_DEFAULT);
    $passUser  = password_hash('User123!', PASSWORD_DEFAULT);
    $passEmp   = password_hash('Empresa123!', PASSWORD_DEFAULT);

    $updateStmt = $pdoDb->prepare("UPDATE usuario SET Contrasena = ? WHERE Email = ?");
    $updateStmt->execute([$passAdmin, 'admin@nexora.com']);
    $updateStmt->execute([$passAdmin, 'admin2@nexora.com']);
    $updateStmt->execute([$passUser, 'usuario@nexora.com']);
    $updateStmt->execute([$passEmp, 'empresa@nexora.com']);

    echo "<div style='font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #107c54; border-radius: 8px; background: #faf7f2; color: #1a2620;'>";
    echo "<h2 style='color: #107c54;'>¡Base de Datos Instalada Correctamente!</h2>";
    echo "<p>La base de datos <strong>nexorabd</strong> ha sido creada e inicializada con los datos por defecto.</p>";
    echo "<h3>Cuentas Disponibles:</h3>";
    echo "<ul>";
    echo "<li><strong>Super Admin:</strong> admin@nexora.com | <strong>Clave:</strong> Admin123!</li>";
    echo "<li><strong>Admin Soporte:</strong> admin2@nexora.com | <strong>Clave:</strong> Admin123!</li>";
    echo "<li><strong>Usuario Regular:</strong> usuario@nexora.com | <strong>Clave:</strong> User123!</li>";
    echo "<li><strong>Usuario Empresa:</strong> empresa@nexora.com | <strong>Clave:</strong> Empresa123!</li>";
    echo "</ul>";
    echo "<p><a href='../public/login.php' style='display: inline-block; background: #107c54; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Ir al Login</a></p>";
    echo "</div>";

} catch (PDOException $e) {
    die("<div style='font-family: sans-serif; color: red; padding: 20px;'>Error en la instalación de la base de datos: " . htmlspecialchars($e->getMessage()) . "</div>");
}
