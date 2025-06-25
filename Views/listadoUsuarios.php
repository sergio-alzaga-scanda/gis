<!-- archivo: ../Views/listadoUsuarios.php -->
<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php");
    exit;
}
include("../Controllers/bd.php");

$resultado = $conn->query("SELECT * FROM usuarios");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Listado de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" />
</head>
<body>
<div class="container mt-5">
  <h2>Listado de Usuarios</h2>
  <a href="../Views/nuevoUsuario.php" class="btn btn-success mb-3">Nuevo Usuario</a>
  
  <table id="usuarios" class="table table-bordered table-striped">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Correo</th>
        <th>Tipo</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    <?php while ($row = $resultado->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= htmlspecialchars($row['correo']) ?></td>
        <td><?= $row['tipo_usuario'] == 1 ? 'Administrador' : 'Agente' ?></td>
        <td>
          <a href="editarUsuario.php?id=<?= $row['id_usuario'] ?>" class="btn btn-primary btn-sm">Editar</a>
          <a href="../Controllers/eliminarUsuario.php?id=<?= $row['id_usuario'] ?>" class="btn btn-danger btn-sm"
             onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</a>
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
    $('#usuarios').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
      }
    });
  });
</script>
</body>
</html>
