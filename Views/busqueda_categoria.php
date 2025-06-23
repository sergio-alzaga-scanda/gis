<?php

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
    <a href="../Views/menu.php" class="btn btn-secondary me-3 btn-volver">Volver</a>
    <button id="verSeleccionBtn" class="btn btn-success me-3">Ver Selección</button>
    <button id="cerrarSesionBtn" class="btn btn-danger">Cerrar sesión</button>

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
        <button type="button" class="btn btn-primary" id="btnSeleccionarCategoria">Seleccionar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Ver Selección -->
<div class="modal fade" id="modalSeleccion" tabindex="-1" aria-labelledby="modalSeleccionLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSeleccionLabel">Detalle de Selección</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h5>Empleado Seleccionado</h5>
            <table class="table table-bordered table-success" id="tablaEmpleadoSeleccionado"></table>
          </div>
          <div class="col-md-6">
            <h5>Categorías</h5>
            <table class="table table-bordered table-primary" id="tablaCategoriasSeleccionadas"></table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
        <button id="btnBorrarSeleccion" class="btn btn-danger">Borrar Selección</button>
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
        if (data.mensaje) {
            Swal.fire('Resultado', data.mensaje, 'info');
        } else {
            data.forEach(cat => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td><a href="#" class="detalle-link" data-id="${cat.id}">${cat.categoria_es || ''}</a></td>
                    <td>${cat.categoria_en || ''}</td>
                    <td>${cat.subcategoria_es || ''}</td>
                    <td>${cat.subcategoria_en || ''}</td>
                    <td>${cat.categoria_tercer_nivel_es || ''}</td>
                    <td>${cat.categoria_tercer_nivel_en || ''}</td>`;
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
                        const etiquetas = {
                            categoria_es: 'Categoría ES',
                            categoria_en: 'Categoría EN',
                            subcategoria_es: 'Subcategoría ES',
                            subcategoria_en: 'Subcategoría EN',
                            categoria_tercer_nivel_es: 'Categoría 3er Nivel ES',
                            categoria_tercer_nivel_en: 'Categoría 3er Nivel EN',
                            incidente: 'Incidente',
                            solicitud: 'Solicitud',
                            impacto: 'Impacto',
                            urgencia: 'Urgencia',
                            severidad: 'Severidad',
                            grupo_solucion: 'Grupo Solución ES',
                            grupo_solucion_en: 'Grupo Solución EN',
                            primary_owner: 'Primary Owner',
                            responsable_1: 'Responsable 1',
                            correo_1: 'Correo 1',
                            extension_1: 'Extensión 1',
                            responsable_2: 'Responsable 2',
                            correo_2: 'Correo 2',
                            extension_2: 'Extensión 2',
                            responsable_3: 'Responsable 3',
                            correo_3: 'Correo 3',
                            extension_3: 'Extensión 3',
                            gerente_lider: 'Gerente Líder',
                            servicio: 'Servicio'
                        };
                        let contenido = '<ul class="list-group">';
                        for (const [clave, etiqueta] of Object.entries(etiquetas)) {
                            contenido += `<li class="list-group-item"><strong>${etiqueta}:</strong> ${cat[clave] || ''}</li>`;
                        }
                        contenido += '</ul>';
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

                // Construir categoría completa (idioma español)
                const categoriaFull = 
                    (data.categoria || '') + 
                    (data.subcategoria ? ' / ' + data.subcategoria : '') + 
                    (data.categoria_tercer_nivel ? ' / ' + data.categoria_tercer_nivel : '');

                // Construir categoría completa (idioma inglés) si existe
                const categoriaFullEn = 
                    (data.category || '') + 
                    (data.subcategory ? ' / ' + data.subcategory : '') + 
                    (data.third_level_category ? ' / ' + data.third_level_category : '');

                // Mostrar en tabla la categoría en ambos idiomas y los campos que mencionaste
                const camposMostrar = [
                    { clave: 'Categoría (ES)', valor: data.categoria_es + "/ " + data.subcategoria_es + "/ " + data.categoria_tercer_nivel_es},
                    { clave: 'Category (EN)', valor: data.categoria_en + "/ " + data.subcategoria_en + "/ " + data.categoria_tercer_nivel_en},
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
        alert('Error al cargar los detalles del empleado.');
    });
});

// Cerrar sesión
document.getElementById('cerrarSesionBtn').addEventListener('click', function() {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¿Deseas cerrar sesión?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cerrar sesión',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../Controllers/cerrar-sesion.php';
        }
    });
});
</script>
</body>
</html>
