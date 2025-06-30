<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}

include("bd.php");

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: ../Views/listadoResponsables.php?deleted=0");
    exit;
}

$stmt = $conn->prepare("DELETE FROM responsables_negocio WHERE id_responsable = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../Views/listadoResponsables.php?deleted=1");
} else {
    header("Location: ../Views/listadoResponsables.php?deleted=0");
}

$stmt->close();
$conn->close();
