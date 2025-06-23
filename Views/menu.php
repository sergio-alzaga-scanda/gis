<?php

include("../Controllers/bd.php");

// Validar que el usuario está logueado y tiene tipo_usuario
if ($_SESSION['tipo_usuario'] =! 1) {
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Acciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
    <style>
        #loadingSplash {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
    </style>
</head>
<body>

<!-- Cargando -->
<div id="loadingSplash">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Cargando...</span>
    </div>
</div>

<div class="container py-5">
    <h2 class="mb-4 text-center">Panel de acciones</h2>

    <div class="d-grid gap-3 col-12 col-md-6 mx-auto">
        <a href="user_information.php" class="btn btn-primary btn-lg">Consultar</a>
        <button id="verSeleccionBtn" class="btn btn-success text-white btn-lg">Ver Selección</button>
        <?php if ($_SESSION['tipo_usuario'] < 2): ?>
            <a href="users.php" class="btn btn-warning text-dark btn-lg">Alta de usuarios</a>
            <a href="update_user_information.php" class="btn btn-dark text-white btn-lg">Update User Information</a>
            <a href="update_resolutor_information.php" class="btn btn-dark text-white btn-lg">Update Resolutor Information</a>
            <a href="cargarVacaciones.php" class="btn btn-dark text-white btn-lg">Registro de vacaciones</a>
        <?php endif; ?>

        

        <a href="../Controllers/cerrar-sesion.php" class="btn btn-danger btn-lg">Cerrar sesión</a>
    </div>
</div>

<!-- Modal Ver Selección -->
<div class="modal fade" id="modalSeleccion" tabindex="-1" aria-labelledby="modalSeleccionLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header d-flex flex-column align-items-center">
        <h5 class="modal-title" id="modalSeleccionLabel">Detalle de Selección</h5>
        
        <button id="btnBorrarSeleccion" class="btn btn-danger my-2">Borrar Selección</button>
        
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div>
            <h5>Empleado Seleccionado</h5>
            <table class="table table-bordered table-success" id="tablaEmpleadoSeleccionado"></table>
          </div>
          <br>
          <div>
            <h5>Categorías</h5>
            <table class="table table-bordered table-primary" id="tablaCategoriasSeleccionadas"></table>
          </div>
        </div>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function getCookie(nombre) {
    const cookies = document.cookie.split("; ");
    for (let c of cookies) {
        const [key, val] = c.split("=");
        if (key === nombre) return decodeURIComponent(val);
    }
    return null;
}
// Borrar selección con confirmación
document.getElementById('btnBorrarSeleccion').addEventListener('click', function () {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esto eliminará la selección guardada.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, borrar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Elimina cookies
            document.cookie = "empleado_seleccionado=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            document.cookie = "categoria=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
            Swal.fire('Borrado', 'La selección ha sido eliminada.', 'success');
            bootstrap.Modal.getInstance(document.getElementById('modalSeleccion')).hide();
        }
    });
});
document.getElementById('verSeleccionBtn').addEventListener('click', function () {
    const id = getCookie("empleado_seleccionado");
    if (!id) {
        Swal.fire("Sin selección", "No hay empleado seleccionado.", "info");
        return;
    }

    const loadingSplash = document.getElementById('loadingSplash');
    loadingSplash.style.display = 'flex';

    fetch('../Controllers/buscar_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'detalle=' + id
    })
    .then(res => res.json())
    .then(empleado => {
        const tablaEmpleado = document.getElementById('tablaEmpleadoSeleccionado');
        tablaEmpleado.innerHTML = '';
        for (const [clave, valor] of Object.entries(empleado)) {
            tablaEmpleado.innerHTML += `<tr><th>${clave}</th><td>${valor}</td></tr>`;
        }

        const categoriasStr = getCookie("categoria");
        const tablaCategorias = document.getElementById('tablaCategoriasSeleccionadas');
        tablaCategorias.innerHTML = '';

        if (categoriasStr) {
            try {
                const data = JSON.parse(categoriasStr);

                const camposMostrar = [
                    { clave: 'Categoría (ES)', valor: `${data.categoria_es} / ${data.subcategoria_es} / ${data.categoria_tercer_nivel_es}` },
                    { clave: 'Category (EN)', valor: `${data.categoria_en} / ${data.subcategoria_en} / ${data.categoria_tercer_nivel_en}` },
                    { clave: 'Severidad', valor: data.severidad || '' },
                    { clave: 'Grupo Solución', valor: data.grupo_solucion || '' },
                    { clave: 'Primary Owner', valor: data.primary_owner || '' },

                    { clave: 'Responsable 1', valor: data.responsable_1 || '' },
                    { clave: 'Correo 1', valor: data.correo_1 || '' },
                    { clave: 'Extensión 1', valor: data.extension_1 || '' },

                    { clave: 'Responsable 2', valor: data.responsable_2 || '' },
                    { clave: 'Correo 2', valor: data.correo_2 || '' },
                    { clave: 'Extensión 2', valor: data.extension_2 || '' },

                    { clave: 'Gerente Líder', valor: data.gerente_lider || '' },
                    { clave: 'Servicio', valor: data.servicio || '' },
                ];

                for (const campo of camposMostrar) {
                    tablaCategorias.innerHTML += `<tr><th>${campo.clave}</th><td>${campo.valor}</td></tr>`;
                }
            } catch (e) {
                tablaCategorias.innerHTML = `<tr><td colspan="2">Error al cargar categorías: ${e.message}</td></tr>`;
            }
        } else {
            tablaCategorias.innerHTML = `<tr><td colspan="2">No hay categorías seleccionadas.</td></tr>`;
        }

        loadingSplash.style.display = 'none';
        new bootstrap.Modal(document.getElementById('modalSeleccion')).show();
    })
    .catch(() => {
        loadingSplash.style.display = 'none';
        Swal.fire("Error", "No se pudo cargar la información del empleado.", "error");
    });
});
</script>

</body>
</html>
