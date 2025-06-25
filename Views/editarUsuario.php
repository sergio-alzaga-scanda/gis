<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
include("../Controllers/bd.php");

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: listadoUsuarios.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
if (!$usuario) {
    echo "Usuario no encontrado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Usuario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
  <h2>Editar Usuario</h2>
  <form id="formActualizar" method="post" action="../Controllers/actualizarUsuario.php">
    <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>" />
    <div class="mb-3">
      <label for="nombre" class="form-label">Nombre</label>
      <input type="text" class="form-control" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required />
    </div>
    <div class="mb-3">
      <label for="correo" class="form-label">Correo</label>
      <input type="email" class="form-control" name="correo" value="<?= htmlspecialchars($usuario['correo']) ?>" required />
    </div>
    <div class="mb-3">
      <label for="contrasena" class="form-label">Nueva Contraseña (dejar vacío para mantener)</label>
      <input type="text" class="form-control" name="contrasena" />
    </div>
    <div class="mb-3">
      <label for="tipo_usuario" class="form-label">Tipo</label>
      <select class="form-select" name="tipo_usuario" required>
        <option value="1" <?= $usuario['tipo_usuario'] == 1 ? 'selected' : '' ?>>Administrador</option>
        <option value="2" <?= $usuario['tipo_usuario'] == 2 ? 'selected' : '' ?>>Agente</option>
      </select>
    </div>
    <div class="d-flex justify-content-start gap-2 mt-3">
      <button type="button" class="btn btn-primary" id="btnConfirmar">Actualizar</button>
      <a href="listadoUsuarios.php" class="btn btn-secondary">Volver</a>
    </div>
  </form>
</div>

<script>
document.getElementById('btnConfirmar').addEventListener('click', function (e) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "¿Deseas actualizar la información del usuario?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, actualizar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('formActualizar').submit();
    }
  });
});
</script>
</body>
</html>
