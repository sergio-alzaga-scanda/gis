<?php
// Datos de conexión
$servername = "localhost"; // Nombre del servidor
$port = 3307;              // Puerto MySQL personalizado
$username = "root";        // Nombre de usuario
$password = "";        // Contraseña del usuario
$database = "gis";         // Nombre de la base de datos

// Crear conexión con puerto
$conn = new mysqli($servername, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


?>
