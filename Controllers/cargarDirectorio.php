<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php");

$conn->set_charset("utf8mb4");
ini_set('display_errors', 1);
error_reporting(E_ALL);
$conn->autocommit(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];

    if (!file_exists($archivo)) {
        mostrarAlerta('error', 'Archivo CSV no encontrado.');
        exit;
    }

    if (!$conn->query("TRUNCATE TABLE grupos_soporte")) {
        mostrarAlerta('error', 'Error al truncar la tabla: ' . $conn->error);
        exit;
    }

    if (($handle = fopen($archivo, "r")) !== false) {
        $encabezado = fgets($handle);
        rewind($handle);

        if (strpos($encabezado, ";") !== false) {
            $delimitador = ";";
        } elseif (strpos($encabezado, "\t") !== false) {
            $delimitador = "\t";
        } else {
            $delimitador = ",";
        }

        fgetcsv($handle, 1000, $delimitador);

        $stmt = $conn->prepare("INSERT INTO grupos_soporte (
            localidad, grupo_torre, resolutores, correo, telefono,
            grupo_distribucion, gerente_responsable
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            mostrarAlerta('error', 'Error en prepare(): ' . $conn->error);
            exit;
        }

        $exitos = 0;
        $errores = 0;

        while (($datos = fgetcsv($handle, 1000, $delimitador)) !== false) {
            if (count($datos) < 7) {
                $errores++;
                continue;
            }

            $localidad           = mb_convert_encoding(trim($datos[0]), 'UTF-8', 'Windows-1252');
            $grupo_torre         = mb_convert_encoding(trim($datos[1]), 'UTF-8', 'Windows-1252');
            $resolutores         = mb_convert_encoding(trim($datos[2]), 'UTF-8', 'Windows-1252');
            $correo              = mb_convert_encoding(trim($datos[3]), 'UTF-8', 'Windows-1252');
            $telefono            = mb_convert_encoding(trim($datos[4]), 'UTF-8', 'Windows-1252');
            $grupo_distribucion  = mb_convert_encoding(trim($datos[5]), 'UTF-8', 'Windows-1252');
            $gerente_responsable = mb_convert_encoding(trim($datos[6]), 'UTF-8', 'Windows-1252');

            $stmt->bind_param("sssssss", $localidad, $grupo_torre, $resolutores, $correo, $telefono, $grupo_distribucion, $gerente_responsable);

            if (!$stmt->execute()) {
                $errores++;
            } else {
                $exitos++;
            }
        }

        fclose($handle);
        $stmt->close();
        $conn->close();

        mostrarResumen($exitos, $errores);
        exit;
    } else {
        mostrarAlerta('error', 'No se pudo abrir el archivo CSV.');
        exit;
    }
} else {
    mostrarAlerta('error', 'Petición inválida o archivo no recibido.');
    exit;
}

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
                timer: 1000
            });

            setTimeout(function() {
                window.location.href = '../Views/menu.php';
            }, 1000);
        </script>
    </body>
    </html>
    <?php
}

function mostrarResumen($exitos, $errores) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Resumen</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'info',
                title: 'Carga completada',
                html: '✅ Filas insertadas: <b><?= $exitos ?></b><br>❌ Errores: <b><?= $errores ?></b>',
                showConfirmButton: false,
                timer: 1000
            });

            setTimeout(function() {
                window.location.href = '../Views/menu.php';
            }, 4000);
        </script>
    </body>
    </html>
    <?php
}
?>
