<?php
include("../Controllers/bd.php");

// Validación de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener la localidad desde GET
$localidad = isset($_GET['localidad']) ? $_GET['localidad'] : '';

// Función para corregir caracteres mal codificados
function corregirCaracteres($texto) {
    $reemplazos = array(
        'Ã¡' => 'á', 'Ã©' => 'é', 'Ã­' => 'í', 'Ã³' => 'ó', 'Ãº' => 'ú',
        'Ã' => 'Á', 'Ã‰' => 'É', 'Ã' => 'Í', 'Ã“' => 'Ó', 'Ãš' => 'Ú',
        'Ã±' => 'ñ', 'Ã‘' => 'Ñ', 'Ã¼' => 'ü', 'Ã' => 'Ü'
    );
    return strtr($texto, $reemplazos);
}

if (!empty($localidad)) {
    $stmt = $conn->prepare("SELECT * FROM grupos_soporte WHERE localidad = ?");
    $stmt->bind_param("s", $localidad);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo '<meta charset="UTF-8">';
        echo "<h5>Directorio de Soporte - " . htmlspecialchars($localidad) . "</h5>";
        echo "<table id='tablaDirectorio' class='table table-bordered table-striped'>";
        echo "<thead class='table-dark'>
                <tr>
                    <th>Grupo Torre</th>
                    <th>Resolutores</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Grupo Distribución</th>
                    <th>Gerente Responsable</th>
                    <th>Fecha Registro</th>
                </tr>
              </thead><tbody>";

        while ($row = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['grupo_torre'])) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['resolutores'])) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['correo'])) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['telefono'])) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['grupo_distribucion'])) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['gerente_responsable'])) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['fecha_registro'])) . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-warning'>No se encontraron registros para esta localidad.</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Localidad no especificada.</div>";
}
?>
