<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php");

// Buscar por categoría (es/en) y subcategorías y tercer nivel
if (isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);

    if (strlen($nombre) >= 2) {
        $busqueda = '%' . $nombre . '%';

        $stmt = $conn->prepare("
            SELECT id, categoria_es, categoria_en, subcategoria_es, subcategoria_en, categoria_tercer_nivel_es, categoria_tercer_nivel_en
            FROM incidentes 
            WHERE categoria_es LIKE ? 
                OR categoria_en LIKE ? 
                OR subcategoria_es LIKE ? 
                OR subcategoria_en LIKE ? 
                OR categoria_tercer_nivel_es LIKE ? 
                OR categoria_tercer_nivel_en LIKE ?
        ");

        // Como hay 6 parámetros iguales, repetimos $busqueda 6 veces
        $stmt->bind_param("ssssss", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $incidentes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $incidentes[] = $fila;
        }
        $stmt->close();

        if (empty($incidentes)) {
            echo json_encode(['mensaje' => 'No se encontraron coincidencias.']);
        } else {
            echo json_encode($incidentes);
        }
    } else {
        echo json_encode(['mensaje' => 'Ingrese al menos 2 caracteres para buscar.']);
    }
    exit;
}

// Detalle por ID
if (isset($_POST['detalle'])) {
    $id = intval($_POST['detalle']);
    $stmt = $conn->prepare("SELECT * FROM incidentes WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($fila = $resultado->fetch_assoc()) {
        echo json_encode($fila);
    } else {
        echo json_encode(['error' => 'Categoría no encontrada']);
    }
    $stmt->close();
    exit;
}
?>
