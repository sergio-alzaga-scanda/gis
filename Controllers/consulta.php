<?php
include("../Controllers/bd.php");

$conn->set_charset("utf8mb4");
$conn->query("SET NAMES 'utf8mb4'");

function corregirCaracteres($texto) {
    $reemplazos = array(
        'Ã¡' => 'á',
        'Ã©' => 'é',
        'Ã­' => 'í',
        'Ã³' => 'ó',
        'Ãº' => 'ú',
        'Ã' => 'Á',
        'Ã‰' => 'É',
        'Ã' => 'Í',
        'Ã“' => 'Ó',
        'Ãš' => 'Ú',
        'Ã±' => 'ñ',
        'Ã‘' => 'Ñ',
        'Ã¼' => 'ü',
        'Ã' => 'Ü'
    );
    return strtr($texto, $reemplazos);
}

$sql = "SELECT * FROM vacaciones 
        WHERE status = '1'
          AND (
                Resolutor_Vacaciones <> '' 
                OR Resolutor_Guardia <> '' 
                OR Telefono_Contacto_Resolutor <> '' 
                OR Correo_Resolutor <> '' 
                OR Jefe_Inmediato <> ''
              )
       ";

$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $fecha_inicio = $fila['Fecha_Inicio'] ? date("d/m/Y", strtotime($fila['Fecha_Inicio'])) : '';
        $fecha_fin    = $fila['Fecha_Termino'] ? date("d/m/Y", strtotime($fila['Fecha_Termino'])) : '';

        echo "<tr>
            <td>" . corregirCaracteres($fila['Resolutor_Vacaciones']) . "</td>
            <td>" . corregirCaracteres($fila['Resolutor_Guardia']) . "</td>
            <td>" . corregirCaracteres($fila['Telefono_Contacto_Resolutor']) . "</td>
            <td>" . corregirCaracteres($fila['Correo_Resolutor']) . "</td>
            <td>$fecha_inicio</td>
            <td>$fecha_fin</td>
            <td>" . corregirCaracteres($fila['Jefe_Inmediato']) . "</td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='7'>No hay datos disponibles</td></tr>";
}

$conn->close();
?>