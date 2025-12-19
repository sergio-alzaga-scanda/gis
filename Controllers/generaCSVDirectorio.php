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

// CONSULTA MODIFICADA para la nueva tabla
// Nota: Ajusta el nombre de la tabla según tu base de datos
$query = "SELECT 
    localidad, 
    grupo_torre, 
    resolutores, 
    correo, 
    telefono, 
    grupo_distribucion, 
    gerente_responsable
    FROM grupos_soporte"; // CAMBIAR: nombre_de_tu_tabla por el nombre real de tu tabla

$resultado = $conn->query($query);

if (!$resultado) {
    die("Error al ejecutar la consulta: " . $conn->error);
}

$archivoCSV = '../docs/responsables.csv'; // Nombre modificado para reflejar el contenido
$fp = fopen($archivoCSV, 'w');

// ENCABEZADOS MODIFICADOS según los campos de tu tabla
$encabezados = [
    "localidad",
    "grupo_torre", 
    "resolutores", 
    "correo", 
    "telefono", 
    "grupo_distribucion", 
    "gerente_responsable"
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

// Configurar headers para descarga
header('Content-Type: application/csv');
header('Content-Disposition: attachment; filename="directorio.csv"');
header('Content-Length: ' . filesize($archivoCSV));

readfile($archivoCSV);

// Opcional: Eliminar el archivo temporal después de enviarlo
unlink($archivoCSV);
exit;