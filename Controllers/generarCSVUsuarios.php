<?php
ob_start();
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php"); // Archivo que contiene la conexión $conn

// Función para limpiar caracteres mal codificados y con tildes
function limpiarTexto($texto) {
    $trans = array(
        'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
        'À' => 'A', 'È' => 'E', 'Ì' => 'I', 'Ò' => 'O', 'Ù' => 'U',
        'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U',
        'Â' => 'A', 'Ê' => 'E', 'Î' => 'I', 'Ô' => 'O', 'Û' => 'U',

        'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
        'à' => 'a', 'è' => 'e', 'ì' => 'i', 'ò' => 'o', 'ù' => 'u',
        'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
        'â' => 'a', 'ê' => 'e', 'î' => 'i', 'ô' => 'o', 'û' => 'u',

        'Ñ' => 'N', 'ñ' => 'n',
        'Ç' => 'C', 'ç' => 'c',
        'ß' => 'ss',
        'Æ' => 'AE', 'æ' => 'ae',
        'Ø' => 'O', 'ø' => 'o',
        'Œ' => 'OE', 'œ' => 'oe',

        // Correcciones de codificación
        'Ã¡' => 'á', 'Ã©' => 'é', 'Ã­' => 'í', 'Ã³' => 'ó', 'Ãº' => 'ú',
        'Ã' => 'Á', 'Ã‰' => 'É', 'Ã' => 'Í', 'Ã“' => 'Ó', 'Ãš' => 'Ú',
        'Ã±' => 'ñ', 'Ã‘' => 'Ñ',
        'Â¿' => '¿', 'Â¡' => '¡', 'Â´' => '´',
        'Ã¼' => 'ü', 'Ãœ' => 'Ü',
        'Ã ' => 'à', 'Ã¨' => 'è', 'Ã¬' => 'ì', 'Ã²' => 'ò', 'Ã¹' => 'ù',
        'Ã€' => 'À', 'Ãˆ' => 'È', 'ÃŒ' => 'Ì', 'Ã’' => 'Ò', 'Ã™' => 'Ù',
        'Ã¤' => 'ä', 'Ã«' => 'ë', 'Ã¯' => 'ï', 'Ã¶' => 'ö',
        'Ã¢' => 'â', 'Ãª' => 'ê', 'Ã®' => 'î', 'Ã´' => 'ô', 'Ã»' => 'û',
        'Â'  => '',
        'Ã'  => 'í' // solo si aplica en tu contexto
    );

    return strtr($texto, $trans);
}

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

// Limpieza por fila
while ($fila = $resultado->fetch_assoc()) {
    $fila_limpia = array_map('limpiarTexto', $fila);
    fputcsv($fp, $fila_limpia, ",");
}

fclose($fp);
$conn->close();

ob_end_clean();

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="empleados.csv"');
readfile($archivoCSV);
exit;
