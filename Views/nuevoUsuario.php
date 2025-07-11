<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro de nuevo usuario</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <div class="container mt-5">
    <h2>Registro de nuevo usuario</h2>
    <form method="post" action="../Controllers/nuevoUsuario.php" enctype="multipart/form-data" id="formulario">
      <!-- Nombre -->
      <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" maxlength="100" required />
      </div>

      <!-- Correo -->
      <div class="mb-3">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo" maxlength="255" required />
      </div>

      <!-- Contraseña -->
      <div class="mb-3">
        <label for="contrasena" class="form-label">Contraseña</label>
        <input type="text" class="form-control" id="contrasena" name="contrasena" maxlength="255" required />
      </div>

      <!-- Tipo de usuario -->
      <div class="mb-3">
        <label for="tipo_usuario" class="form-label">Tipo de usuario</label>
        <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
          <option value="1" selected>Administrador</option>
          <option value="2">Agente</option>
        </select>
      </div>

      
      <div class="d-flex justify-content-start gap-2 mt-3">
      <button type="submit" class="btn btn-primary">Enviar</button>
      <a href="listadoUsuarios.php" class="btn btn-secondary">Volver</a>
    </div>
      
    </form>
  </div>

  <!-- Bootstrap JS (opcional, para componentes interactivos) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
