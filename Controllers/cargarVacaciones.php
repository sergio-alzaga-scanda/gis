<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
    exit;
}

include("../Controllers/bd.php");

ini_set('display_errors', 1);
error_reporting(E_ALL);



$conn->autocommit(true);

function excelDateToMySQLDate($excelDate) {
    $timestamp = ($excelDate - 25569) * 86400;
    return gmdate("Y-m-d", $timestamp);
}

function convertirFecha($fecha) {
    $partes = explode('/', $fecha);
    if (count($partes) === 3) {
        $dia = str_pad($partes[0], 2, "0", STR_PAD_LEFT);
        $mes = str_pad($partes[1], 2, "0", STR_PAD_LEFT);
        $anio = $partes[2];
        return "$anio-$mes-$dia";
    }
    return $fecha;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_csv'])) {
    $archivo = $_FILES['archivo_csv']['tmp_name'];

    if (!file_exists($archivo)) {
        mostrarAlerta('error', 'Archivo CSV no encontrado.');
        exit;
    }

    if (($handle = fopen($archivo, "r")) !== false) {
        fgetcsv($handle); // Saltar encabezado

        // Paso 1: Marcar todos los registros existentes como status = 0
        $conn->query("UPDATE vacaciones SET status = 0");
        $actualizados = $conn->affected_rows;

        // Paso 2: Preparar inserción de nuevos registros con status = 1
        $stmt = $conn->prepare("INSERT INTO vacaciones (
            Resolutor_Vacaciones,
            Resolutor_Guardia,
            Telefono_Contacto_Resolutor,
            Correo_Resolutor,
            Fecha_Inicio,
            Fecha_Termino,
            Jefe_Inmediato,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            mostrarAlerta('error', 'Error en prepare(): ' . $conn->error);
            exit;
        }

        $exitos = 0;
        $errores = 0;

        while (($datos = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($datos) < 7) {
                $errores++;
                continue;
            }

            list(
                $resolutorVacaciones,
                $resolutorGuardia,
                $telefono,
                $correo,
                $fechaInicio,
                $fechaFin,
                $jefe
            ) = $datos;

            $fechaInicio = trim($fechaInicio);
            $fechaFin = trim($fechaFin);

            $fechaInicio = ($fechaInicio === '') ? null : (is_numeric($fechaInicio) ? excelDateToMySQLDate($fechaInicio) : convertirFecha($fechaInicio));
            $fechaFin = ($fechaFin === '') ? null : (is_numeric($fechaFin) ? excelDateToMySQLDate($fechaFin) : convertirFecha($fechaFin));

            $status = 1;

            $stmt->bind_param(
                "sssssssi",
                $resolutorVacaciones,
                $resolutorGuardia,
                $telefono,
                $correo,
                $fechaInicio,
                $fechaFin,
                $jefe,
                $status
            );

            if (!$stmt->execute()) {
                $errores++;
            } else {
                $exitos++;
            }
        }

        fclose($handle);
        $stmt->close();
        $conn->close();

        mostrarAlerta('success', "Carga completada. Filas insertadas: $exitos. Registros antiguos desactivados: $actualizados. Errores: $errores.");
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
                timer: 3000
            });

            setTimeout(function() {
                window.location.href = '../Views/menu.php';
            }, 3000);
        </script>
    </body>
    </html>
    <?php
}
?>
