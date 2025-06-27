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

        $stmt = $conn->prepare("INSERT INTO vacaciones (
            Resolutor_Vacaciones,
            Resolutor_Guardia,
            Telefono_Contacto_Resolutor,
            Correo_Resolutor,
            Fecha_Inicio,
            Fecha_Termino,
            Jefe_Inmediato
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            mostrarAlerta('error', 'Error en prepare(): ' . $conn->error);
            exit;
        }

        //$logfile = fopen("inserciones_log.txt", "a");
        $exitos = 0;
        $errores = 0;

        while (($datos = fgetcsv($handle, 1000, ",")) !== false) {
            if (count($datos) < 7) {
                #fwrite($logfile, "Línea con datos insuficientes: " . implode(",", $datos) . "\n");
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

            // Limpieza y conversión de fechas
            $fechaInicio = trim($fechaInicio);
            $fechaFin = trim($fechaFin);

            if ($fechaInicio === '') {
                $fechaInicio = null;
            } else {
                $fechaInicio = is_numeric($fechaInicio) ? excelDateToMySQLDate($fechaInicio) : convertirFecha($fechaInicio);
            }

            if ($fechaFin === '') {
                $fechaFin = null;
            } else {
                $fechaFin = is_numeric($fechaFin) ? excelDateToMySQLDate($fechaFin) : convertirFecha($fechaFin);
            }

            // mysqli no soporta bind_param con null directamente,
            // por eso usamos esta técnica para pasar null:
            $stmt->bind_param(
                "sssssss",
                $resolutorVacaciones,
                $resolutorGuardia,
                $telefono,
                $correo,
                $fechaInicio,
                $fechaFin,
                $jefe
            );

            // Forzar valores null a mysqli con esta función:
            // Pero bind_param no soporta nulls, entonces usamos bind_param con referencias y ajustamos
            // para que en MySQL se inserte NULL hacemos lo siguiente:

            // NOTA: La solución rápida es usar esta extensión:
            // En este contexto, la manera más sencilla es usar 's' y pasar NULL como NULL (de PHP), pero mysqli lo convierte a ''
            // Si quieres que sea NULL, modifica la consulta para que acepte NULL con el siguiente código:
            // Usamos la función below para pasar NULL como NULL:
            $params = [
                $resolutorVacaciones,
                $resolutorGuardia,
                $telefono,
                $correo,
                $fechaInicio,
                $fechaFin,
                $jefe
            ];

            // Rebind parameters manualmente para forzar nulls:
            $stmt->close();

            // Nueva consulta con valores directos usando placeholders especiales para NULL:
            $sql = "INSERT INTO vacaciones (
                Resolutor_Vacaciones,
                Resolutor_Guardia,
                Telefono_Contacto_Resolutor,
                Correo_Resolutor,
                Fecha_Inicio,
                Fecha_Termino,
                Jefe_Inmediato
            ) VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);

            // Para fechas que son null, pasamos NULL directamente usando types y bind_param:
            $stmt->bind_param(
                "sssssss",
                $params[0],
                $params[1],
                $params[2],
                $params[3],
                $params[4],
                $params[5],
                $params[6]
            );

            if (!$stmt->execute()) {
                //fwrite($logfile, "Error al insertar fila: " . implode(",", $datos) . " - " . $stmt->error . "\n");
                $errores++;
            } else {
                //fwrite($logfile, "Fila insertada correctamente.\n");
                $exitos++;
            }
        }

        fclose($handle);
        //fclose($logfile);
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
