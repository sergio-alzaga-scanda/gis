<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php");

// Verifica conexión
if (!$conn) {
    die("Error: No se pudo conectar a la base de datos.");
}

$query = "SELECT 
    Resolutor_Vacaciones, Resolutor_Guardia, Telefono_Contacto_Resolutor,
    Correo_Resolutor, Fecha_Inicio, Fecha_Termino,
    Jefe_Inmediato, fecha_creacion
    FROM vacaciones";

$resultado = $conn->query($query);
if (!$resultado) {
    die("Error al ejecutar la consulta: " . $conn->error);
}

$archivoCSV = '../docs/vacaciones_' . session_id() . '.csv';
$fp = fopen($archivoCSV, 'w');
if (!$fp) {
    die("Error: no se pudo crear el archivo CSV en $archivoCSV");
}

// Escribir BOM UTF-8
fwrite($fp, "\xEF\xBB\xBF");

$encabezados = [
    "Resolutor_Vacaciones", "Resolutor_Guardia", "Telefono_Contacto_Resolutor",
    "Correo_Resolutor", "Fecha_Inicio", "Fecha_Termino",
    "Jefe_Inmediato", "fecha_creacion"
];
fputcsv($fp, $encabezados, ",");

function limpiarTexto($texto) {
    if ($texto === null) return '';
    if (function_exists('mb_convert_encoding')) {
        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
    }
    $texto = preg_replace('/[\x00-\x1F\x7F]/u', '', $texto);

    $trans = [
        'Á'=>'A','É'=>'E','Í'=>'I','Ó'=>'O','Ú'=>'U','Ñ'=>'N','ñ'=>'n',
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u'
    ];
    return strtr($texto, $trans);
}

while ($fila = $resultado->fetch_assoc()) {
    $fila_limpia = array_map('limpiarTexto', $fila);
    fputcsv($fp, $fila_limpia, ",");
}

fclose($fp);
$conn->close();

if (ob_get_length()) ob_end_clean();

if (!file_exists($archivoCSV)) {
    die("Error: El archivo CSV no se generó correctamente.");
}

header('Content-Type: application/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="vacaciones.csv"');
readfile($archivoCSV);
unlink($archivoCSV); // elimina archivo temporal
exit;
