<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
}
?>  
<?php
include("../Controllers/bd.php");

// Mostrar errores (desactivar en producción)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn->autocommit(true);

// Función para convertir fecha de Excel (número de serie) a YYYY-MM-DD
function excelDateToMySQLDate($excelDate) {
    $timestamp = ($excelDate - 25569) * 86400;
    return gmdate("Y-m-d", $timestamp);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];

    if (!file_exists($archivo)) {
        mostrarAlerta('error', 'Archivo CSV no encontrado.');
        exit;
    }

    // Limpiar tabla
    if (!$conn->query("TRUNCATE TABLE empleados")) {
        mostrarAlerta('error', 'Error al truncar la tabla: ' . $conn->error);
        exit;
    }

    if (($handle = fopen($archivo, "r")) !== false) {
        fgetcsv($handle); // Saltar encabezado

        $stmt = $conn->prepare("INSERT INTO empleados (
            numero_empleado, nombre, fecha_efectiva, status, correo_electronico,
            ubicacion, departamento, titulo_puesto, empresa_fisica, compania,
            tipo_empleado, numero_jefe, nombre_jefe, correo_jefe
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            mostrarAlerta('error', 'Error en prepare(): ' . $conn->error);
            exit;
        }

        //$logfile = fopen("inserciones_log.txt", "a");
        $exitos = 0;
        $errores = 0;

        while (($datos = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($datos) < 14) {
                fwrite($logfile, "Línea con datos insuficientes: " . implode(",", $datos) . "\n");
                $errores++;
                continue;
            }

            if (strlen($datos[0]) > 10) {
                fwrite($logfile, "Número de empleado muy largo: " . $datos[0] . "\n");
                $errores++;
                continue;
            }

            $numero_empleado   = $datos[0];
            $nombre            = $datos[1];
            $fecha_efectiva    = is_numeric($datos[2]) ? excelDateToMySQLDate($datos[2]) : $datos[2];
            $status            = $datos[3];
            $correo_electronico = !empty($datos[4]) ? $datos[4] : null;
            $ubicacion         = $datos[5];
            $departamento      = $datos[6];
            $titulo_puesto     = $datos[7];
            $empresa_fisica    = $datos[8];
            $compania          = $datos[9];
            $tipo_empleado     = $datos[10];
            $numero_jefe       = $datos[11];
            $nombre_jefe       = $datos[12];
            $correo_jefe       = $datos[13];

            $stmt->bind_param(
                "ssssssssssssss",
                $numero_empleado, $nombre, $fecha_efectiva, $status,
                $correo_electronico, $ubicacion, $departamento, $titulo_puesto,
                $empresa_fisica, $compania, $tipo_empleado, $numero_jefe,
                $nombre_jefe, $correo_jefe
            );

            if (!$stmt->execute()) {
                fwrite($logfile, "Error al insertar fila: " . implode(",", $datos) . " - " . $stmt->error . "\n");
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

// Función para mostrar alerta con SweetAlert2
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
                timer: 2000
            });

            setTimeout(function() {
                window.location.href = '../Views/menu.php';
            }, 2000);
        </script>
    </body>
    </html>
    <?php
}
?>
