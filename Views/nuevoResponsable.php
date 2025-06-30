<!-- archivo: ../Views/nuevoResponsable.php -->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Nuevo Responsable</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="container mt-5">
  <h2>Agregar Nuevo Responsable de Negocio</h2>
  
  <form id="formNuevo" method="post" action="../Controllers/insertarResponsable.php">
    <div class="mb-3">
      <label for="localidad" class="form-label">Localidad</label>
      <input type="text" class="form-control" name="localidad" id="localidad" required />
    </div>
    <div class="mb-3">
      <label for="consultor" class="form-label">Consultor de Negocio</label>
      <input type="text" class="form-control" name="consultor" id="consultor" required />
    </div>
    <div class="d-flex gap-2">
      <button type="button" class="btn btn-success" id="btnGuardar">Guardar</button>
      <a href="listadoResponsables.php" class="btn btn-secondary">Volver</a>
    </div>
  </form>
</div>

<script>
document.getElementById('btnGuardar').addEventListener('click', function () {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "¿Deseas guardar este nuevo responsable?",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, guardar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('formNuevo').submit();
    }
  });
});
</script>
</body>
</html>
