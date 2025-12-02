<?php
include("../Controllers/bd.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $periodo = $_POST['periodo_busqueda'];
    $hora = $_POST['hora_ejecucion'];
    $activa = isset($_POST['ejecucion_activa']) ? 1 : 0;

    $update = $conn->prepare("
        UPDATE configuracion_busqueda 
        SET periodo_busqueda=?, hora_ejecucion=?, ejecucion_activa=?
    ");

    $update->bind_param("ssi", $periodo, $hora, $activa);

    if ($update->execute()) {
        header("Location: ../Views/rpa.php?success=1");
        exit;
    } else {
        echo "Error al actualizar configuración.";
    }
}
?>
