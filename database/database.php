<?php
// conexión a la base de datos
$host = 'localhost';      // Servidor
$dbname = 'nexorabd'; // Nombre de la base de datos
$username = 'root';        // Usuario de la base de datos
$password = '';            // Contraseña del usuario

try {
    // Creamos un objeto PDO con la información de conexión
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Configuramos PDO para que lance excepciones si hay errores
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   // conexión fue exitosa
} catch (PDOException $e) {
    // Si ocurre un error, mostramos el mensaje y detenemos la ejecución
    die("Error connecting to the database: " . $e->getMessage());
}
?>
