<?php

include("../Controllers/bd.php");

if (isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);

    if (strlen($nombre) >= 2) {
        $palabras = preg_split('/\s+/', $nombre);

        $whereParts = [];
        $tipos = '';
        $parametros = [];

        foreach ($palabras as $palabra) {
            $like = '%' . $palabra . '%';

            // Ampliar columnas para buscar en todas
            $columnas = [
                'categoria_es', 'categoria_en',
                'subcategoria_es', 'subcategoria_en',
                'categoria_tercer_nivel_es', 'categoria_tercer_nivel_en',
                'incidente', 'solicitud', 'impacto', 'urgencia', 'severidad',
                'grupo_solucion', 'grupo_solucion_en', 'primary_owner',
                'responsable_1', 'correo_1', 'extension_1',
                'responsable_2', 'correo_2', 'extension_2',
                'responsable_3', 'correo_3', 'extension_3',
                'gerente_lider', 'servicio'
            ];

            $subCondiciones = [];
            foreach ($columnas as $col) {
                $subCondiciones[] = "$col LIKE ?";
                $tipos .= 's';
                $parametros[] = $like;
            }

            $whereParts[] = '(' . implode(' OR ', $subCondiciones) . ')';
        }

        $whereClause = implode(' AND ', $whereParts);

        $query = "
            SELECT id, categoria_es, categoria_en, subcategoria_es, subcategoria_en,
                   categoria_tercer_nivel_es, categoria_tercer_nivel_en,
                   incidente, solicitud, impacto, urgencia, severidad,
                   grupo_solucion, grupo_solucion_en, primary_owner,
                   responsable_1, correo_1, extension_1,
                   responsable_2, correo_2, extension_2,
                   responsable_3, correo_3, extension_3,
                   gerente_lider, servicio, fecha_registro
            FROM incidentes 
            WHERE $whereClause
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param($tipos, ...$parametros);
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
