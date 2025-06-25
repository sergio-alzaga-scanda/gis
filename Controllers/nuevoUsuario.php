<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$tipo_usuario = $_POST['tipo_usuario'] ?? '';

if ($nombre === '' || $correo === '' || $contrasena === '' || $tipo_usuario === '') {
    mostrar_alerta("Error", "Por favor completa todos los campos.", "error", false);
    exit;
}

if (!in_array($tipo_usuario, ['1', '2'])) {
    mostrar_alerta("Error", "Tipo de usuario no válido.", "error", false);
    exit;
}

$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, tipo_usuario) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    mostrar_alerta("Error", "Error en la preparación de la consulta: " . $conn->error, "error", false);
    exit;
}
$stmt->bind_param("sssi", $nombre, $correo, $contrasena_hash, $tipo_usuario);

try {
    $stmt->execute();
    mostrar_alerta("¡Éxito!", "Usuario registrado correctamente.", "success", true);
} catch (mysqli_sql_exception $e) {
    if ($e->getCode() == 1062) {
        mostrar_alerta("Error", "El correo ya está registrado.", "error", false);
    } else {
        mostrar_alerta("Error", "Error al registrar el usuario: " . $e->getMessage(), "error", false);
    }
}

$stmt->close();
$conn->close();

function mostrar_alerta($titulo, $mensaje, $icono, $exito) {
    // $exito = true => redirigir a ../Views/menu.php
    // $exito = false => volver a la página anterior
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8" />
        <title>Resultado</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
    <script>
        Swal.fire({
            title: "<?php echo $titulo; ?>",
            text: "<?php echo $mensaje; ?>",
            icon: "<?php echo $icono; ?>",
            confirmButtonText: "Aceptar"
        }).then((result) => {
            if (result.isConfirmed) {
                <?php if ($exito): ?>
                    window.location.href = "../Views/menu.php";
                <?php else: ?>
                    history.back();
                <?php endif; ?>
            }
        });
    </script>
    </body>
    </html>
    <?php
}
?>
