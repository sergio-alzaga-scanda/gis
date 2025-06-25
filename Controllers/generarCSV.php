<?php
ob_start();
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php"); // Se asume que estás en la carpeta Controllers

// Corrección de caracteres mal codificados
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
        'Ã'  => 'í' // Solo si estás seguro de su uso
    );

    return strtr($texto, $trans);
}

// Consulta
$query = "SELECT 
    categoria_es, categoria_en, subcategoria_es, subcategoria_en,
    categoria_tercer_nivel_es, categoria_tercer_nivel_en,
    incidente, solicitud, impacto, urgencia, severidad,
    grupo_solucion, grupo_solucion_en, primary_owner,
    responsable_1, correo_1, extension_1,
    responsable_2, correo_2, extension_2,
    responsable_3, correo_3, extension_3,
    gerente_lider, servicio, fecha_registro
    FROM incidentes";

$resultado = $conn->query($query);

if (!$resultado) {
    die("Error al ejecutar la consulta: " . $conn->error);
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

// Limpieza y escritura de datos
while ($fila = $resultado->fetch_assoc()) {
    $fila_limpia = array_map('limpiarTexto', $fila);
    fputcsv($fp, $fila_limpia, ",");
}

fclose($fp);
$conn->close();

ob_end_clean();

header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="incidentes.csv"');
readfile($archivoCSV);
exit;
