<?php
ob_start();
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php"); // Se asume que estás en la carpeta Controllers

// Reemplaza 'tu_tabla' con el nombre real de tu tabla en la BD
$query = "SELECT 
    categoria_es, categoria_en, subcategoria_es, subcategoria_en,
    categoria_tercer_nivel_es, categoria_tercer_nivel_en,
    incidente, solicitud, impacto, urgencia, severidad,
    grupo_solucion, grupo_solucion_en, primary_owner,
    responsable_1, correo_1, extension_1,
    responsable_2, correo_2, extension_2,
    responsable_3, correo_3, extension_3,
    gerente_lider, servicio, fecha_registro
    FROM incidentes"; // <-- Cambia esto por el nombre real

$resultado = $conn->query($query);


if (!$resultado) {
    die("Error al ejecutar la consulta: " . $conexion->error);
}

$archivoCSV = '../docs/incidentes.csv';
$fp = fopen($archivoCSV, 'w');

$encabezados = [
    "categoria_es", "categoria_en", "subcategoria_es", "subcategoria_en",
    "categoria_tercer_nivel_es", "categoria_tercer_nivel_en",
    "incidente", "solicitud", "impacto", "urgencia", "severidad",
    "grupo_solucion", "grupo_solucion_en", "primary_owner",
    "responsable_1", "correo_1", "extension_1",
    "responsable_2", "correo_2", "extension_2",
    "responsable_3", "correo_3", "extension_3",
    "gerente_lider", "servicio", "fecha_registro"
];

fputcsv($fp, $encabezados, ",");

while ($fila = $resultado->fetch_assoc()) {
    fputcsv($fp, $fila, ",");
}

fclose($fp);
$conn->close();

ob_end_clean(); // o ob_clean();

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="incidentes.csv"');
readfile($archivoCSV);
exit;
exit;
