<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("../Controllers/bd.php");

// Recibir datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$tipo_usuario = $_POST['tipo_usuario'] ?? '';

// Validaciones básicas
if ($nombre === '' || $correo === '' || $contrasena === '' || $tipo_usuario === '') {
    echo "Por favor completa todos los campos.";
    exit;
}

if (!in_array($tipo_usuario, ['1', '2'])) {
    echo "Tipo de usuario no válido.";
    exit;
}

$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO usuarios (nombre, correo, contrasena, tipo_usuario) VALUES (?, ?, ?, ?)");

if (!$stmt) {
    echo "Error en la preparación de la consulta: " . $conn->error;
    exit;
}

$stmt->bind_param("sssi", $nombre, $correo, $contrasena_hash, $tipo_usuario);

if ($stmt->execute()) {
    // Redirigir a menú si se inserta correctamente
    header("Location: ../Views/menu.php");
    exit;
} else {
    if ($conn->errno === 1062) {
        echo "El correo ya está registrado.";
    } else {
        echo "Error al registrar el usuario: " . $conn->error;
    }
}

$stmt->close();
$conn->close();
?>
