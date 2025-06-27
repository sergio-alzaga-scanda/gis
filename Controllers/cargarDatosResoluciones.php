<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php");
$conn->set_charset("utf8mb4");

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];

    if (!file_exists($archivo)) {
        mostrarAlerta('error', 'Archivo CSV no encontrado.');
        exit;
    }

    if (!$conn->query("TRUNCATE TABLE incidentes")) {
        mostrarAlerta('error', 'Error al truncar la tabla: ' . $conn->error);
        exit;
    }

    if (($handle = fopen($archivo, "r")) !== false) {
        fgetcsv($handle); // Saltar encabezado

        $stmt = $conn->prepare("INSERT INTO incidentes (
            categoria_es, categoria_en, subcategoria_es, subcategoria_en,
            categoria_tercer_nivel_es, categoria_tercer_nivel_en, incidente,
            solicitud, impacto, urgencia, severidad, grupo_solucion,
            grupo_solucion_en, primary_owner, responsable_1, correo_1,
            extension_1, responsable_2, correo_2, extension_2,
            responsable_3, correo_3, extension_3, gerente_lider, servicio
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            mostrarAlerta('error', 'Error en prepare(): ' . $conn->error);
            exit;
        }

        //$logfile = fopen("inserciones_log.txt", "a");
        $exitos = 0;
        $errores = 0;

        while (($datos = fgetcsv($handle, 10000, ",")) !== false) {
            $datos = array_map('limpiarTexto', $datos);

            // Asegura que haya exactamente 25 columnas
            while (count($datos) < 25) {
                $datos[] = null;
            }

            list(
                $categoria_es, $categoria_en, $subcategoria_es, $subcategoria_en,
                $categoria_tercer_nivel_es, $categoria_tercer_nivel_en, $incidente,
                $solicitud, $impacto, $urgencia, $severidad, $grupo_solucion,
                $grupo_solucion_en, $primary_owner, $responsable_1, $correo_1,
                $extension_1, $responsable_2, $correo_2, $extension_2,
                $responsable_3, $correo_3, $extension_3, $gerente_lider, $servicio
            ) = $datos;

            $stmt->bind_param(
                "sssssssssssssssssssssssss",
                $categoria_es, $categoria_en, $subcategoria_es, $subcategoria_en,
                $categoria_tercer_nivel_es, $categoria_tercer_nivel_en, $incidente,
                $solicitud, $impacto, $urgencia, $severidad, $grupo_solucion,
                $grupo_solucion_en, $primary_owner, $responsable_1, $correo_1,
                $extension_1, $responsable_2, $correo_2, $extension_2,
                $responsable_3, $correo_3, $extension_3, $gerente_lider, $servicio
            );

            if (!$stmt->execute()) {
                fwrite($logfile, "Error al insertar fila: " . implode(" | ", $datos) . " - " . $stmt->error . "\n");
                $errores++;
            } else {
                fwrite($logfile, "Fila insertada correctamente.\n");
                $exitos++;
            }
        }

        fclose($handle);
        fclose($logfile);
        $stmt->close();
        $conn->close();

        mostrarAlerta('success', "Carga completada. Filas insertadas: $exitos. Errores: $errores.");
        exit;
    } else {
        mostrarAlerta('error', 'No se pudo abrir el archivo CSV.');
        exit;
    }
} else {
    mostrarAlerta('error', 'Petición inválida o archivo no recibido.');
    exit;
}

// ✅ Función para mostrar SweetAlert2
function mostrarAlerta($tipo, $mensaje) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Resultado de la carga</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: '<?= $tipo ?>',
                title: 'Resultado de la carga',
                text: '<?= $mensaje ?>',
                showConfirmButton: false,
                timer: 3000
            });
            setTimeout(() => window.location.href = '../Views/menu.php', 3000);
        </script>
    </body>
    </html>
    <?php
}

// ✅ Función para limpiar texto y normalizar codificación
function limpiarTexto($texto) {
    $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
    $texto = preg_replace('/[\x00-\x1F\x7F]/u', '', $texto);
    $texto = str_replace(
        ["\xC2\x93", "\xC2\x94", "\xE2\x80\x9C", "\xE2\x80\x9D", "\xE2\x80\x99"],
        '"',
        $texto
    );
    return trim($texto);
}
?>
