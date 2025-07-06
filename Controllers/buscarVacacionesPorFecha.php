<?php
include("../Controllers/bd.php");

$conn->set_charset("utf8mb4");
$conn->query("SET NAMES 'utf8mb4'");

// Meses en español
$meses = [
    '01' => 'enero', '02' => 'febrero', '03' => 'marzo', '04' => 'abril',
    '05' => 'mayo', '06' => 'junio', '07' => 'julio', '08' => 'agosto',
    '09' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'
];

// Cambiamos la consulta para traer fecha y hora completa
$sql = "SELECT DISTINCT fecha_creacion 
        FROM vacaciones
        ORDER BY fecha_creacion DESC";

$resultado = $conn->query($sql);
$fechas = [];

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $fechaOriginal = $fila['fecha_creacion']; // yyyy-mm-dd hh:mm:ss
        $fechaObj = new DateTime($fechaOriginal);
        $dia = ltrim($fechaObj->format('d'), '0');
        $mesTexto = $meses[$fechaObj->format('m')];
        $anio = $fechaObj->format('Y');
        $hora = $fechaObj->format('H:i:s');  // Hora con segundos

        $texto = "$dia de $mesTexto del $anio"; // Puedes incluir hora aquí si quieres

        $fechas[] = [
            'valor' => $fechaOriginal,  // Fecha completa con hora
            'texto' => $texto,
            'hora' => $hora
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($fechas);
$conn->close();
?>
