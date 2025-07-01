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


    $stmt = $conn->prepare("SELECT * FROM responsables_negocio");
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        echo '<meta charset="UTF-8">';
        echo "<h5>Responsables de Negocio - " . htmlspecialchars($localidad) . "</h5>";
        echo "<table id='tablaResponsables' class='table table-bordered table-striped'>";
        echo "<thead class='table-dark'>
                <tr>
                    <th>ID</th>
                    <th>Localidad</th>
                    <th>Consultor de Negocio</th>
                </tr>
              </thead><tbody>";

        while ($row = $resultado->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id_responsable']) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['localidad'])) . "</td>";
            echo "<td>" . htmlspecialchars(corregirCaracteres($row['consultor_negocio'])) . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-warning'>No se encontraron registros para esta localidad.</div>";
    }

    $stmt->close();

?>
