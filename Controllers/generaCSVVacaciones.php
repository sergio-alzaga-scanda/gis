<?php
ob_start();
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php");

// Consulta para la tabla vacaciones
$query = "SELECT 
    Resolutor_Vacaciones, Resolutor_Guardia, Telefono_Contacto_Resolutor,
    Correo_Resolutor, Fecha_Inicio, Fecha_Termino,
    Jefe_Inmediato, fecha_registro
    FROM vacaciones";

$resultado = $conn->query($query);

if (!$resultado) {
    die("Error al ejecutar la consulta: " . $conn->error);
}

$archivoCSV = '../docs/vacaciones.csv';
$fp = fopen($archivoCSV, 'w');

// Escribir BOM UTF-8 para que Excel reconozca bien la codificación
fwrite($fp, "\xEF\xBB\xBF");

$encabezados = [
    "Resolutor_Vacaciones", "Resolutor_Guardia", "Telefono_Contacto_Resolutor",
    "Correo_Resolutor", "Fecha_Inicio", "Fecha_Termino",
    "Jefe_Inmediato", "fecha_registro"
];

fputcsv($fp, $encabezados, ",");

/**
 * Función para limpiar texto y eliminar caracteres problemáticos.
 */
function limpiarTexto($texto) {
    // Normaliza a UTF-8, elimina caracteres no imprimibles
    $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
    $texto = preg_replace('/[\x00-\x1F\x7F]/u', '', $texto);
    
    // Opcional: reemplazar caracteres especiales acentuados por equivalentes sin acento
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

    // Correcciones de caracteres mal codificados (UTF-8 mal leído como Latin-1)
    'Ã¡' => 'á', 'Ã©' => 'é', 'Ã­' => 'í', 'Ã³' => 'ó', 'Ãº' => 'ú',
    'Ã' => 'Á', 'Ã‰' => 'É', 'Ã' => 'Í', 'Ã“' => 'Ó', 'Ãš' => 'Ú',
    'Ã±' => 'ñ', 'Ã‘' => 'Ñ',
    'Â¿' => '¿', 'Â¡' => '¡', 'Â´' => '´',
    'Ã¼' => 'ü', 'Ãœ' => 'Ü',
    'Ã ' => 'à', 'Ã¨' => 'è', 'Ã¬' => 'ì', 'Ã²' => 'ò', 'Ã¹' => 'ù',
    'Ã€' => 'À', 'Ãˆ' => 'È', 'ÃŒ' => 'Ì', 'Ã’' => 'Ò', 'Ã™' => 'Ù',
    'Ã¤' => 'ä', 'Ã«' => 'ë', 'Ã¯' => 'ï', 'Ã¶' => 'ö', 'Ã' => 'Ü',
    'Ã¤' => 'ä', 'Ã¢' => 'â', 'Ãª' => 'ê', 'Ã®' => 'î', 'Ã´' => 'ô', 'Ã»' => 'û',
    'Â'  => '',   // Carácter basura común
    'Ã'  => 'í'   // Solo si estás seguro de que este caso se presenta solo como error de í
);

    $texto = strtr($texto, $trans);

    return $texto;
}

while ($fila = $resultado->fetch_assoc()) {
    // Limpia cada campo antes de escribir
    $fila_limpia = array_map('limpiarTexto', $fila);
    fputcsv($fp, $fila_limpia, ",");
}

fclose($fp);
$conn->close();

ob_end_clean();

header('Content-Type: application/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="vacaciones.csv"');
readfile($archivoCSV);
exit;
