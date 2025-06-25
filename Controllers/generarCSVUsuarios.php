<?php
ob_start();
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php"); // Archivo que contiene la conexión $conn

$query = "SELECT 
    numero_empleado, nombre, fecha_efectiva, status, correo_electronico, 
    ubicacion, departamento, titulo_puesto, empresa_fisica, compania, 
    tipo_empleado, numero_jefe, nombre_jefe, correo_jefe
    FROM empleados";

$resultado = $conn->query($query);

if (!$resultado) {
    die("Error al ejecutar la consulta: " . $conn->error);
}

$archivoCSV = '../docs/empleados.csv';
$fp = fopen($archivoCSV, 'w');

// Encabezados del CSV según los campos
$encabezados = [
    "numero_empleado", "nombre", "fecha_efectiva", "status", "correo_electronico", 
    "ubicacion", "departamento", "titulo_puesto", "empresa_fisica", "compania", 
    "tipo_empleado", "numero_jefe", "nombre_jefe", "correo_jefe"
];

fputcsv($fp, $encabezados, ",");

while ($fila = $resultado->fetch_assoc()) {
    fputcsv($fp, $fila, ",");
}

fclose($fp);
$conn->close();

ob_end_clean();

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="empleados.csv"');
readfile($archivoCSV);
exit;
