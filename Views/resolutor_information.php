<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Búsqueda de Categorías</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      #loadingSplash {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(255,255,255,0.8);
        display: flex; justify-content: center; align-items: center;
        z-index: 1055; display: none;
      }
      .btn-volver { min-width: 120px; }
    </style>
</head>
<body>
<div class="container mt-5">
    <!--<a href="../Views/menu.php" class="btn btn-secondary me-3 btn-volver">Volver</a>
    <button id="verSeleccionBtn" class="btn btn-success me-3">Ver Selección</button>
    <button id="cerrarSesionBtn" class="btn btn-danger">Cerrar sesión</button> -->
    <div style="background-color: #b0eaf5;  padding-right: 10px; padding-left: 10px; padding-bottom: 10px;">
        <h2 class="mt-4">Búsqueda de Categorías</h2>
    <form id="formBusqueda" class="mb-4 d-flex" role="search">
        <input type="text" class="form-control me-2" id="nombre" placeholder="Buscar categoría (es/en)" required>
        <button type="submit" class="btn btn-primary">Buscar</button>
    </form>

    <table class="table table-striped" id="tablaResultados">
        <thead>
            <tr>
                <th>Categoría ES</th><th>Categoría EN</th>
                <th>Subcategoría ES</th><th>Subcategoría EN</th>
                <th>3er Nivel ES</th><th>3er Nivel EN</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    </div>
    
</div>

<div id="loadingSplash">
    <div class="spinner-border text-primary"></div>
    <span class="visually-hidden">Cargando...</span>
</div>

<!-- Modal Detalle Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCategoriaLabel">Detalles de Categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalContenido"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" id="btnSeleccionarCategoria">Seleccionar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Selección -->
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const loadingSplash = document.getElementById('loadingSplash');
let categoriaSeleccionada = null;

// Cookies
function setCookie(nombre, valor, dias) {
    const fecha = new Date();
    fecha.setTime(fecha.getTime() + (dias*24*60*60*1000));
    document.cookie = nombre + "=" + encodeURIComponent(valor) + ";expires=" + fecha.toUTCString() + ";path=/";
}

function getCookie(nombre) {
    const cookies = document.cookie.split("; ");
    for (let c of cookies) {
        const [key, val] = c.split("=");
        if (key === nombre) return decodeURIComponent(val);
    }
    return null;
}

// Buscar categoría
document.getElementById('formBusqueda').addEventListener('submit', function(e) {
    e.preventDefault();
    const nombre = document.getElementById('nombre').value.trim();
    if (nombre.length < 2) {
        Swal.fire('Atención', 'Ingrese al menos 2 caracteres para buscar.', 'warning');
        return;
    }

    loadingSplash.style.display = 'flex';
    fetch('../Controllers/buscar_categoria.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'nombre=' + encodeURIComponent(nombre)
    })
    .then(res => res.json())
   .then(data => {
    const tbody = document.querySelector('#tablaResultados tbody');
    tbody.innerHTML = '';

    // Cambiar los encabezados de tabla
    document.querySelector('#tablaResultados thead').innerHTML = `
        <tr>
            <th>Categoría (ES)</th>
            <th>Category (EN)</th>
            <th>Grupo Solución</th>
            <th>Severidad</th>
            <th>Resolutor</th>
        </tr>
    `;

    if (data.mensaje) {
        Swal.fire('Resultado', data.mensaje, 'info');
    } else {
        data.forEach(cat => {
            const categoriaES = `${cat.categoria_es || ''} / ${cat.subcategoria_es || ''} / ${cat.categoria_tercer_nivel_es || ''}`;
            const categoriaEN = `${cat.categoria_en || ''} / ${cat.subcategoria_en || ''} / ${cat.categoria_tercer_nivel_en || ''}`;

            const resolutores = [
                cat.responsable_1 ? `${cat.responsable_1} (${cat.correo_1 || ''})` : '',
                cat.responsable_2 ? `${cat.responsable_2} (${cat.correo_2 || ''})` : '',
                cat.responsable_3 ? `${cat.responsable_3} (${cat.correo_3 || ''})` : ''
            ].filter(r => r).join('<br>');

            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td><a href="#" class="detalle-link" data-id="${cat.id}">${categoriaES}</a></td>
                <td>${categoriaEN}</td>
                <td>${cat.grupo_solucion || ''}</td>
                <td>${cat.severidad || ''}</td>
                <td>${resolutores}</td>
            `;
            tbody.appendChild(fila);
        });

            document.querySelectorAll('.detalle-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    loadingSplash.style.display = 'flex';

                    fetch('../Controllers/buscar_categoria.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'detalle=' + encodeURIComponent(id)
                    })
                    .then(res => res.json())
                    .then(cat => {
                        categoriaSeleccionada = cat;
                        // Campos que queremos mostrar, igual que en "Ver Selección"
                        const camposMostrar = [
                            { clave: 'Categoría (ES)', valor: cat.categoria_es + " / " + cat.subcategoria_es + " / " + cat.categoria_tercer_nivel_es },
                            { clave: 'Category (EN)', valor: cat.categoria_en + " / " + cat.subcategoria_en + " / " + cat.categoria_tercer_nivel_en },
                            { clave: 'Severidad', valor: cat.severidad || '' },
                            { clave: 'Grupo Solución', valor: cat.grupo_solucion || '' },
                            { clave: 'Primary Owner', valor: cat.primary_owner || '' },
                            { clave: 'Responsable 1', valor: cat.responsable_1 || '' },
                            { clave: 'Correo 1', valor: cat.correo_1 || '' },
                            { clave: 'Extensión 1', valor: cat.extension_1 || '' },
                            { clave: 'Responsable 2', valor: cat.responsable_2 || '' },
                            { clave: 'Correo 2', valor: cat.correo_2 || '' },
                            { clave: 'Extensión 2', valor: cat.extension_2 || '' },
                            { clave: 'Responsable 3', valor: cat.responsable_3 || '' },
                            { clave: 'Correo 3', valor: cat.correo_3 || '' },
                            { clave: 'Extensión 3', valor: cat.extension_3 || '' },
                            { clave: 'Gerente Líder', valor: cat.gerente_lider || '' },
                            { clave: 'Servicio', valor: cat.servicio || '' },
                        ];

                        let contenido = '<table class="table table-bordered">';
                        for (const campo of camposMostrar) {
                            contenido += `<tr><th>${campo.clave}</th><td>${campo.valor}</td></tr>`;
                        }
                        contenido += '</table>';

                        document.getElementById('modalContenido').innerHTML = contenido;
                        new bootstrap.Modal(document.getElementById('modalCategoria')).show();
                        loadingSplash.style.display = 'none';
                    })
                    .catch(() => {
                        loadingSplash.style.display = 'none';
                        alert('Error al cargar los detalles de la categoría.');
                    });
                });
            });
        }
        loadingSplash.style.display = 'none';
    })
    .catch(() => {
        loadingSplash.style.display = 'none';
        alert('Error al buscar categorías.');
    });
});

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

// Guardar categoría seleccionada
document.getElementById('btnSeleccionarCategoria').addEventListener('click', function() {
    if (categoriaSeleccionada) {
        setCookie("categoria", JSON.stringify(categoriaSeleccionada), 1);
        Swal.fire("Seleccionado", "La categoría ha sido seleccionada correctamente.", "success");
        bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
    }
});

document.getElementById('verSeleccionBtn').addEventListener('click', function () {
    const id = getCookie("empleado_seleccionado");
    if (!id) {
        Swal.fire("Sin selección", "No hay empleado seleccionado.", "info");
        return;
    }
    loadingSplash.style.display = 'flex';

    fetch('../Controllers/buscar_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'detalle=' + id
    })
    .then(res => res.json())
    .then(empleado => {
        // Mostrar datos del empleado
        const tablaEmpleado = document.getElementById('tablaEmpleadoSeleccionado');
        tablaEmpleado.innerHTML = '';
        for (const [clave, valor] of Object.entries(empleado)) {
            tablaEmpleado.innerHTML += `<tr><th>${clave}</th><td>${valor}</td></tr>`;
        }

        // Obtener categorías y demás datos desde cookie (asumimos JSON)
        const categoriasStr = getCookie("categoria");
        const tablaCategorias = document.getElementById('tablaCategoriasSeleccionadas');
        tablaCategorias.innerHTML = '';

        if (categoriasStr) {
            try {
                const data = JSON.parse(categoriasStr);

                const camposMostrar = [
                    { clave: 'Categoría (ES)', valor: data.categoria_es + " / " + data.subcategoria_es + " / " + data.categoria_tercer_nivel_es },
                    { clave: 'Category (EN)', valor: data.categoria_en + " / " + data.subcategoria_en + " / " + data.categoria_tercer_nivel_en },
                    { clave: 'Severidad', valor: data.severidad || '' },
                    { clave: 'Grupo Solución', valor: data.grupo_solucion || '' },
                    { clave: 'Primary Owner', valor: data.primary_owner || '' },
                    { clave: 'Responsable 1', valor: data.responsable_1 || '' },
                    { clave: 'Correo 1', valor: data.correo_1 || '' },
                    { clave: 'Extensión 1', valor: data.extension_1 || '' },
                    { clave: 'Responsable 2', valor: data.responsable_2 || '' },
                    { clave: 'Correo 2', valor: data.correo_2 || '' },
                    { clave: 'Extensión 2', valor: data.extension_2 || '' },
                    { clave: 'Responsable 3', valor: data.responsable_3 || '' },
                    { clave: 'Correo 3', valor: data.correo_3 || '' },
                    { clave: 'Extensión 3', valor: data.extension_3 || '' },
                    { clave: 'Gerente Líder', valor: data.gerente_lider || '' },
                    { clave: 'Servicio', valor: data.servicio || '' },
                ];

                let contenido = '';
                for (const campo of camposMostrar) {
                    contenido += `<tr><th>${campo.clave}</th><td>${campo.valor}</td></tr>`;
                }
                tablaCategorias.innerHTML = contenido;
            } catch (e) {
                tablaCategorias.innerHTML = '<tr><td colspan="2">No hay categorías seleccionadas o el formato es inválido.</td></tr>';
            }
        } else {
            tablaCategorias.innerHTML = '<tr><td colspan="2">No hay categorías seleccionadas.</td></tr>';
        }

        new bootstrap.Modal(document.getElementById('modalSeleccion')).show();
        loadingSplash.style.display = 'none';
    })
    .catch(() => {
        loadingSplash.style.display = 'none';
        alert('Error al cargar la selección.');
    });
});

// Cerrar sesión
document.getElementById('cerrarSesionBtn').addEventListener('click', function() {
    Swal.fire({
        title: '¿Cerrar sesión?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No'
    }).then((result) => {
        if(result.isConfirmed){
            window.location.href = '../Controllers/logout.php';
        }
    });
});
</script>
<?php 
include("../Views/vacaciones.php");
?>

</body>
</html>
