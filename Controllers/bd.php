<?php
// Datos de conexión
//$servername = "localhost"; // Nombre del servidor
//$port = 3307;              // Puerto MySQL personalizado
//$username = "root";        // Nombre de usuario
//$password = "";        // Contraseña del usuario
//$database = "gis";         // Nombre de la base de datos
//
//// Crear conexión con puerto
//$conn = new mysqli($servername, $username, $password, $database, $port);
//
//// Verificar conexión
//if ($conn->connect_error) {
//    die("Conexión fallida: " . $conn->connect_error);
//}



$servername = "localhost";
$username = "root";
$password = "Melco154.,";
$database = "gis_db";
$port = 3306;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>


