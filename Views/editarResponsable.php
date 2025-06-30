<!-- archivo editarResponsable.php -->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
include("../Controllers/bd.php");

$id = $_GET['id'] ?? '';
if (!$id) {
    header("Location: responsables_negocio.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM responsables_negocio WHERE id_responsable = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
$responsable = $resultado->fetch_assoc();
if (!$responsable) {
    echo "Responsable no encontrado.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Editar Responsable</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
  <h2>Editar Responsable de Negocio</h2>
  <form id="formActualizar" method="post" action="../Controllers/actualizarResponsable.php">
    <input type="hidden" name="id" value="<?= $responsable['id_responsable'] ?>" />
    <div class="mb-3">
      <label for="localidad" class="form-label">Localidad</label>
      <input type="text" class="form-control" name="localidad" value="<?= htmlspecialchars($responsable['localidad']) ?>" required />
    </div>
    <div class="mb-3">
      <label for="consultor" class="form-label">Consultor de Negocio</label>
      <input type="text" class="form-control" name="consultor" value="<?= htmlspecialchars($responsable['consultor_negocio']) ?>" required />
    </div>
    <div class="d-flex justify-content-start gap-2 mt-3">
      <button type="button" class="btn btn-primary" id="btnConfirmar">Actualizar</button>
      <a href="../Views/menu.php" class="btn btn-secondary">Volver</a>
      <a href="../Controllers/eliminarResponsable.php?id=<?= $responsable['id_responsable'] ?>" 
         class="btn btn-danger" 
         onclick="return confirm('¿Estás seguro de eliminar este responsable?');">
         Eliminar
      </a>
    </div>
  </form>
</div>

<script>
document.getElementById('btnConfirmar').addEventListener('click', function (e) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "¿Deseas actualizar la información del responsable?",
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
