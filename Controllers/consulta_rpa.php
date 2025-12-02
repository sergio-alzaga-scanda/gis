<?php
// ========== HEADERS ==========
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// ========== CREDENCIALES VALIDAS ==========
$validUser = "Beto2025";
$validPass = "D3v_2025";

// ========== LECTURA DE ENTRADA JSON ==========
$raw = file_get_contents("php://input");
$input = json_decode($raw, true);

// Entrada por JSON o POST normal
$usuario = $input["usuario"] ?? ($_POST["usuario"] ?? null);
$password = $input["password"] ?? ($_POST["password"] ?? null);

// ========== LECTURA DE BASIC AUTH ==========
$basicUser = $_SERVER["PHP_AUTH_USER"] ?? null;
$basicPass = $_SERVER["PHP_AUTH_PW"] ?? null;

// Si viene Basic Auth, tiene prioridad
if ($basicUser !== null && $basicPass !== null) {
    $usuario = $basicUser;
    $password = $basicPass;
}

// ========== VALIDACIÓN DE CREDENCIALES ==========
if ($usuario !== $validUser || $password !== $validPass) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "code" => 401,
        "message" => "Credenciales inválidas"
    ]);
    exit;
}

// ========== CONEXIÓN A MYSQL ==========
$host = "localhost";
$user = "root";
$pass = "Melco154.,";
$dbname = "gis_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "code" => 500,
        "message" => "Error en conexión a MySQL"
    ]);
    exit;
}

// ========== CONSULTA ==========
$query = "SELECT id, periodo_busqueda, ejecucion_activa, hora_ejecucion, fecha_actualizacion
          FROM configuracion_busqueda 
          WHERE id = 1 LIMIT 1";

$result = $conn->query($query);

if (!$result || $result->num_rows === 0) {
    http_response_code(404);
    echo json_encode([
        "status" => "error",
        "code" => 404,
        "message" => "No se encontró la configuración"
    ]);
    exit;
}

$row = $result->fetch_assoc();

// ========== RESPUESTA ==========
echo json_encode([
       
        "periodo_busqueda" => $row["periodo_busqueda"],
        "ejecucion_activa" => (bool)$row["ejecucion_activa"],
        "hora_ejecucion" => $row["hora_ejecucion"]
    
    
]);

$conn->close();
?>
