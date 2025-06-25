<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
include("bd.php");

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: ../Views/listadoUsuarios.php?deleted=0");
    exit;
}

// Verificar si el usuario es administrador
$stmt = $conn->prepare("SELECT tipo_usuario FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();
$stmt->close();

if (!$usuario) {
    header("Location: ../Views/listadoUsuarios.php?deleted=0");
    exit;
}

if ($usuario['tipo_usuario'] == 1) {
    // Si es administrador, no permitir la eliminación
    header("Location: ../Views/listadoUsuarios.php?deleted=0&error=admin");
    exit;
}

// Proceder a eliminar si no es administrador
$stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$success = $stmt->execute();
$stmt->close();
$conn->close();

if ($success) {
    header("Location: ../Views/listadoUsuarios.php?deleted=1");
} else {
    header("Location: ../Views/listadoUsuarios.php?deleted=0");
}
exit;
