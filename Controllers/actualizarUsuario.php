<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
include("bd.php");

$id = $_POST['id'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$contrasena = $_POST['contrasena'] ?? '';
$tipo_usuario = $_POST['tipo_usuario'] ?? '';

if (!$id || !$nombre || !$correo || !$tipo_usuario) {
    header("Location: ../Views/listadoUsuarios.php?status=error&msg=Faltan+datos");
    exit;
}

if (!in_array($tipo_usuario, ['1', '2'])) {
    header("Location: ../Views/listadoUsuarios.php?status=error&msg=Tipo+de+usuario+inv%C3%A1lido");
    exit;
}

if ($contrasena !== '') {
    $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, contrasena = ?, tipo_usuario = ? WHERE id_usuario = ?");
    $stmt->bind_param("sssii", $nombre, $correo, $contrasena, $tipo_usuario, $id);
} else {
    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, correo = ?, tipo_usuario = ? WHERE id_usuario = ?");
    $stmt->bind_param("ssii", $nombre, $correo, $tipo_usuario, $id);
}

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: ../Views/listadoUsuarios.php?status=success&msg=Usuario+actualizado+correctamente");
    exit;
} else {
    $stmt->close();
    $conn->close();
    header("Location: ../Views/listadoUsuarios.php?status=error&msg=Error+al+actualizar+usuario");
    exit;
}
