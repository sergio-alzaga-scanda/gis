<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php");

// Buscar por nombre
if (isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);

    if (strlen($nombre) >= 2) {
        $busqueda = '%' . $nombre . '%';

        $stmt = $conn->prepare("SELECT id, nombre, fecha_efectiva, correo_electronico, empresa_fisica 
                                FROM empleados 
                                WHERE nombre LIKE ?");
        $stmt->bind_param("s", $busqueda);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $empleados = [];
        while ($fila = $resultado->fetch_assoc()) {
            if (!empty($fila['fecha_efectiva'])) {
                $fecha = date_create($fila['fecha_efectiva']);
                $fila['fecha_efectiva'] = date_format($fecha, 'd/m/Y');
            }
            $empleados[] = $fila;
        }
        $stmt->close();

        if (empty($empleados)) {
            echo json_encode(['mensaje' => 'No se encontraron coincidencias.']);
        } else {
            echo json_encode($empleados);
        }
    } else {
        echo json_encode(['mensaje' => 'Ingrese al menos 2 caracteres para buscar.']);
    }
    exit;
}

// Detalle por id
if (isset($_POST['detalle'])) {
    $id = intval($_POST['detalle']);
    $stmt = $conn->prepare("SELECT * FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        if (!empty($fila['fecha_efectiva'])) {
            $fecha = date_create($fila['fecha_efectiva']);
            $fila['fecha_efectiva'] = date_format($fecha, 'd/m/Y');
        }
        echo json_encode($fila);
    } else {
        echo json_encode(['error' => 'Empleado no encontrado']);
    }
    $stmt->close();
    exit;
}
?>
