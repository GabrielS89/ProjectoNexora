<?php
// conexión a la base de datos
$host = 'localhost';      // Servidor
$dbname = 'nexorabd'; // Nombre de la base de datos
$username = 'root';        // Usuario de la base de datos
$password = '';            // Contraseña del usuario

$charset = 'utf8mb4';

$dbname = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dbname, $username, $password, $options);
} catch (\PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>