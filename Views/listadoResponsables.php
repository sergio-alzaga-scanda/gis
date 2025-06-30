<!-- archivo: ../Views/listadoResponsables.php -->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
include("../Controllers/bd.php");

$resultado = $conn->query("SELECT * FROM responsables_negocio");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Listado de Responsables de Negocio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" />
</head>
<body>
<div class="container mt-5">
  <h2>Listado de Responsables de Negocio</h2>
  
  <div class="d-flex justify-content-start gap-2 mt-3">
      <a href="../Views/nuevoResponsable.php" class="btn btn-success mb-3">Nuevo Responsable</a>
      <a href="menu.php" class="btn btn-secondary mb-3">Volver</a>
  </div>

  <table id="responsables" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Localidad</th>
        <th>Consultor de Negocio</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['localidad']) ?></td>
        <td><?= htmlspecialchars($row['consultor_negocio']) ?></td>
        <td>
          <a href="editarResponsable.php?id=<?= $row['id_responsable'] ?>" class="btn btn-primary btn-sm">Editar</a>
          <a href="../Controllers/eliminarResponsable.php?id=<?= $row['id_responsable'] ?>" class="btn btn-danger btn-sm"
             onclick="return confirm('¿Estás seguro de eliminar este responsable?');">Eliminar</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

<script>
  $(document).ready(function() {
    $('#responsables').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
      }
    });
  });
</script>

<?php if (isset($_GET['deleted'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    <?php if ($_GET['deleted'] == '1'): ?>
      Swal.fire({
        icon: 'success',
        title: 'Eliminado',
        text: 'El responsable fue eliminado correctamente.',
        timer: 2000,
        showConfirmButton: false
      });
    <?php else: ?>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'No se pudo eliminar el responsable.',
        confirmButtonText: 'Cerrar'
      });
    <?php endif; ?>
  });
</script>
<?php endif; ?>
</body>
</html>
