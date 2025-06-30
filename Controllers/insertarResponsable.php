<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php");

$localidad = $_POST['localidad'] ?? '';
$consultor = $_POST['consultor'] ?? '';

if (empty($localidad) || empty($consultor)) {
    header("Location: ../Views/nuevoResponsable.php?error=1");
    exit;
}

$stmt = $conn->prepare("INSERT INTO responsables_negocio (localidad, consultor_negocio) VALUES (?, ?)");
$stmt->bind_param("ss", $localidad, $consultor);

if ($stmt->execute()) {
    header("Location: ../Views/listadoResponsables.php?inserted=1");
} else {
    header("Location: ../Views/nuevoResponsable.php?error=2");
}

$stmt->close();
$conn->close();
