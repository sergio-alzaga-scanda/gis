<?php
// API: obtener configuración de búsqueda
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// CREDENCIALES DEL API
$validUser = "Beto2025";
$validPass = "D3v_2025";

// LEER ENTRADA (JSON o POST normal)
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

$usuario = $input["usuario"] ?? ($_POST["usuario"] ?? "");
$password = $input["password"] ?? ($_POST["password"] ?? "");

// VALIDAR CREDENCIALES
if ($usuario !== $validUser || $password !== $validPass) {
    echo json_encode([
        "status" => "error",
        "code" => 401,
        "message" => "Credenciales inválidas"
    ]);
    exit;
}

// CONEXIÓN MYSQL
$host = "localhost";
$user = "root";
$pass = "Melco154.,";
$dbname = "gis_db"; // <-- CAMBIA ESTO

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "code" => 500,
        "message" => "Error en conexión a MySQL"
    ]);
    exit;
}

// CONSULTA
$query = "SELECT id, periodo_busqueda, ejecucion_activa, hora_ejecucion, fecha_actualizacion 
          FROM configuracion_busqueda 
          WHERE id = 1 LIMIT 1";

$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "code" => 404,
        "message" => "No se encontró la configuración"
    ]);
    exit;
}

$row = $result->fetch_assoc();

// RESPUESTA JSON
echo json_encode([
    "status" => "success",
    "code" => 200,
    "data" => [
        "id" => (int)$row["id"],
        "periodo_busqueda" => $row["periodo_busqueda"],
        "ejecucion_activa" => (bool)$row["ejecucion_activa"],
        "hora_ejecucion" => $row["hora_ejecucion"],
        "fecha_actualizacion" => $row["fecha_actualizacion"]
    ]
]);

$conn->close();
?>
