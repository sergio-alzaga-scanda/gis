<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php");

$id = $_POST['id'] ?? '';
$localidad = $_POST['localidad'] ?? '';
$consultor = $_POST['consultor'] ?? '';

if (empty($id) || empty($localidad) || empty($consultor)) {
    header("Location: ../Views/editarResponsable.php?id=$id&error=1");
    exit;
}

$stmt = $conn->prepare("UPDATE responsables_negocio SET localidad = ?, consultor_negocio = ? WHERE id_responsable = ?");
$stmt->bind_param("ssi", $localidad, $consultor, $id);

if ($stmt->execute()) {
    header("Location: ../Views/listadoResponsables.php?updated=1");
} else {
    header("Location: ../Views/editarResponsable.php?id=$id&error=2");
}

$stmt->close();
$conn->close();
