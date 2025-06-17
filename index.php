<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Sistema</title>
  
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom Styles -->
  <style>
    body {
      background: #f5f7fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .card {
      border-radius: 1rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .form-control:focus {
      box-shadow: none;
      border-color: #20c997; /* verde azulado */
    }

    .btn-custom {
      background-color: #28a745; /* verde */
      border-color: #28a745;
      color: white;
    }

    .btn-custom:hover {
      background-color: #20c997; /* verde azulado */
      border-color: #20c997;
    }

    .text-custom {
      color: #28a745;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-lg-5">
        <div class="card p-4">
          <h3 class="text-center mb-4 text-custom">Iniciar sesión</h3>
          <form id="loginForm" method="POST" action="Controllers/sesion.php">
            <div class="mb-3">
              <label for="correo" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="correo" name="correo" placeholder="correo@example.com" required>
            </div>
            <div class="mb-3">
              <label for="pass" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="pass" name="pass" placeholder="Contraseña" required>
            </div>
            <div class="d-grid">
              <button type="submit" class="btn btn-custom">Ingresar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
