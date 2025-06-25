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
