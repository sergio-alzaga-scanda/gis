<?php
include("../Controllers/bd.php");
$conn->set_charset("utf8mb4");

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

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_seleccionada'])) {
    $fechaSeleccionada = $_POST['fecha_seleccionada'];

    // Preparar la consulta - ajustar el filtro según tu lógica:
    // Ejemplo: si 'fecha_creacion' es exacta, está bien
    $stmt = $conn->prepare("SELECT * FROM vacaciones 
        WHERE fecha_creacion = ?
          AND (
                Resolutor_Vacaciones <> '' 
                OR Resolutor_Guardia <> '' 
                OR Telefono_Contacto_Resolutor <> '' 
                OR Correo_Resolutor <> '' 
                OR Jefe_Inmediato <> ''
              )
       ");
    $stmt->bind_param("s", $fechaSeleccionada);
    $stmt->execute();
    $resultadoBusqueda = $stmt->get_result();

    if ($resultadoBusqueda->num_rows > 0) {
        $data = [];
        while ($fila = $resultadoBusqueda->fetch_assoc()) {
            // Corregir caracteres en cada campo de la fila
            foreach ($fila as $key => $value) {
                if (is_string($value)) {
                    $fila[$key] = corregirCaracteres($value);
                }
            }
            $data[] = $fila;
        }

        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'not_found', 'data' => []]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se recibió fecha']);
}

$conn->close();
?>
