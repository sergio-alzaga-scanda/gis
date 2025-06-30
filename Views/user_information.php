<?php
session_start();
$localidad = isset($_GET['localidad']) ? $_GET['localidad'] : '';
include("../Controllers/bd.php");
$sql = "SELECT * FROM grupos_soporte";
if ($localidad !== '') {
    $stmt = $conn->prepare("SELECT * FROM grupos_soporte WHERE localidad = ?");
    $stmt->bind_param("s", $localidad);
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $resultado = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Búsqueda de Empleados</title>
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
    <?php if ($_SESSION['tipo_usuario'] < 2): ?>
    <a href="../Views/menu.php" class="btn btn-secondary me-3 btn-volver">Volver</a>
    <?php endif; ?>
    <button id="verSeleccionBtn" class="btn btn-success me-3">Ver Selección</button>
    <button id="btnMexico" class="btn btn-dark me-2">Directorio México</button>
    <button id="btnEuropa" class="btn btn-dark me-3">Directorio Europa</button>
    <button id="cerrarSesionBtn" class="btn btn-danger">Cerrar sesión</button>
<br><br>
    <div style="background-color: #b0f5d8; padding: 10px;">
        <h2 class="mt-4">Búsqueda de Empleados</h2>
        <form id="formBusqueda" class="mb-4 d-flex" role="search">
            <input type="text" class="form-control me-2" name="nombre" id="nombre" placeholder="Escribe el nombre a buscar" required>
            <button type="submit" class="btn btn-dark">Buscar</button>
        </form>

        <table class="table table-striped" id="tablaResultados">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Fecha Efectiva</th>
                    <th>Correo Electrónico</th>
                    <th>Empresa Física</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="directorioModal" tabindex="-1" aria-labelledby="directorioModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="directorioModalLabel">Directorio</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="contenidoDirectorio">
        Cargando...
      </div>
    </div>
  </div>
</div>
</div>
<div class="container mt-5">
    <!-- Sección de Búsqueda de Categorías -->
<div style="background-color: #b0eaf5;  padding: 10px;">
    <h2 class="mt-4">Búsqueda de Categorías</h2>
    <form id="formBusquedaCategoria" class="mb-4 d-flex" role="search">
        <input type="text" class="form-control me-2" id="nombreCategoria" placeholder="Buscar categoría (es/en)" required>
        <button type="submit" class="btn btn-dark">Buscar</button>
    </form>

    <table class="table table-striped" id="tablaResultadosCategorias">
        <thead>
            <tr>
                <th>Categoría ES</th><th>Categoría EN</th>
                <th>Grupo resolutor</th>
                <th>Severidad</th>
                <th>Resolutor 1</th>
                <th>Resolutor 2</th>
                <th>Resolutor 3</th>
                		
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
</div>



<div id="loadingSplash">
    <div class="spinner-border text-primary" role="status"></div>
    <span class="visually-hidden">Cargando...</span>
</div>

<!-- Modal Detalle Empleado -->
<div class="modal fade" id="modalEmpleado" tabindex="-1" aria-labelledby="modalEmpleadoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEmpleadoLabel">Detalles del Empleado</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalContenido"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" id="btnSeleccionarEmpleado">Seleccionar</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal Detalle Categoría -->
<div class="modal fade" id="modalCategoria" tabindex="-1" aria-labelledby="modalCategoriaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"> 
        <h5 class="modal-title" id="modalCategoriaLabel">Detalles de Categoría</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalContenidoCategoria"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" id="btnSeleccionarCategoria">Seleccionar</button>
      </div>
    </div>
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


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

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

const loadingSplash = document.getElementById('loadingSplash');
let empleadoSeleccionadoId = null;

// Funciones cookies
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

// Buscar empleados
document.getElementById('formBusqueda').addEventListener('submit', function(e) {
    e.preventDefault();
    const nombre = document.getElementById('nombre').value.trim();
    if (nombre.length < 2) {
        Swal.fire({ icon: 'warning', title: 'Atención', text: 'Ingrese al menos 2 caracteres para buscar.' });
        return;
    }

    loadingSplash.style.display = 'flex';
    fetch('../Controllers/buscar_usuario.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'nombre=' + encodeURIComponent(nombre)
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.querySelector('#tablaResultados tbody');
        tbody.innerHTML = '';

        if (data.mensaje) {
            Swal.fire({ icon: 'info', title: 'Resultado', text: data.mensaje });
        } else {
            data.forEach(empleado => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td><a href="#" class="detalle-link" data-id="${empleado.id}">${empleado.nombre}</a></td>
                    <td>${empleado.fecha_efectiva}</td>
                    <td>${empleado.correo_electronico || ''}</td>
                    <td>${empleado.empresa_fisica || ''}</td>`;
                tbody.appendChild(fila);
            });

            document.querySelectorAll('.detalle-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    empleadoSeleccionadoId = id;
                    loadingSplash.style.display = 'flex';

                    fetch('../Controllers/buscar_usuario.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'detalle=' + id
                    })
                    .then(response => response.json())
                    .then(empleado => {
                        const etiquetas = {
                            nombre: 'Affected User',
                            correo_electronico: 'E - Mail',
                            numero_empleado: 'Employee Number',
                            departamento: 'Department',
                            titulo_puesto: 'Title Position',
                            empresa_fisica: 'Physical Company',
                            compania: 'Company',
                            tipo_empleado: 'Employee Type'
                        };
                        let contenido = '<ul class="list-group">';
                        for (const [clave, etiqueta] of Object.entries(etiquetas)) {
                            contenido += `<li class="list-group-item"><strong>${etiqueta}:</strong> ${empleado[clave] || ''}</li>`;
                        }
                        contenido += '</ul>';
                        document.getElementById('modalContenido').innerHTML = contenido;
                        new bootstrap.Modal(document.getElementById('modalEmpleado')).show();
                        loadingSplash.style.display = 'none';
                    })
                    .catch(() => {
                        loadingSplash.style.display = 'none';
                        alert('Error al cargar los detalles del empleado.');
                    });
                });
            });
        }
        loadingSplash.style.display = 'none';
    })
    .catch(() => {
        loadingSplash.style.display = 'none';
        alert('Error al buscar empleados.');
    });
});

// Seleccionar empleado
document.getElementById('btnSeleccionarEmpleado').addEventListener('click', function () {
    if (empleadoSeleccionadoId) {
        setCookie("empleado_seleccionado", empleadoSeleccionadoId, 1);
        Swal.fire({ icon: 'success', title: 'Empleado seleccionado', text: 'Se ha guardado la selección.' });
        bootstrap.Modal.getInstance(document.getElementById('modalEmpleado')).hide();
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

//_________________________________________________________________________________________________________________

let categoriaSeleccionada = null;

// Buscar categoría
document.getElementById('formBusquedaCategoria').addEventListener('submit', function(e) {
    e.preventDefault();
    const nombre = document.getElementById('nombreCategoria').value.trim();
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
        const tbody = document.querySelector('#tablaResultadosCategorias tbody');
        tbody.innerHTML = '';
        if (data.mensaje) {
            Swal.fire('Resultado', data.mensaje, 'info');
        } else {
            data.forEach(cat => {
    const categoriaES = `${cat.categoria_es || ''} / ${cat.subcategoria_es || ''} / ${cat.categoria_tercer_nivel_es || ''}`;
    const categoriaEN = `${cat.categoria_en || ''} / ${cat.subcategoria_en || ''} / ${cat.categoria_tercer_nivel_en || ''}`;

    const resolutor1 = cat.responsable_1 ? `${cat.responsable_1}<br>(${cat.correo_1 || ''})` : '';
    const resolutor2 = cat.responsable_2 ? `${cat.responsable_2}<br>(${cat.correo_2 || ''})` : '';
    const resolutor3 = cat.responsable_3 ? `${cat.responsable_3}<br>(${cat.correo_3 || ''})` : '';

    const fila = document.createElement('tr');
    fila.innerHTML = `
        <td><a href="#" class=".detalle-link-cat" data-id="${cat.id}">${categoriaES}</a></td>
        <td>${categoriaEN}</td>
        <td>${cat.grupo_solucion || ''}</td>
        <td>${cat.severidad || ''}</td>
        <td>${resolutor1}</td>
        <td>${resolutor2}</td>
        <td>${resolutor3}</td>
    `;
    tbody.appendChild(fila);
});

            // Mostrar detalles de la categoría al hacer clic
            document.querySelectorAll('.detalle-link-cat').forEach(link => {
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

                        document.getElementById('modalContenidoCategoria').innerHTML = contenido;
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

// Seleccionar categoría
document.getElementById('btnSeleccionarCategoria').addEventListener('click', function () {
    if (categoriaSeleccionada) {
        setCookie("categoria", JSON.stringify(categoriaSeleccionada), 1);
        Swal.fire("Seleccionado", "La categoría ha sido seleccionada correctamente.", "success");
        bootstrap.Modal.getInstance(document.getElementById('modalCategoria')).hide();
    }
});
</script>
<?php 
//include("../Views/resolutor_information.php");
include("../Views/vacaciones.php");
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('btnMexico').addEventListener('click', function () {
    cargarDirectorio('Mexico');
});
document.getElementById('btnEuropa').addEventListener('click', function () {
    cargarDirectorio('Europa');
});


function cargarDirectorio(localidad) {
    fetch(`../Controllers/directorio_ajax.php?localidad=${encodeURIComponent(localidad)}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('contenidoDirectorio').innerHTML = data;

            // Mostrar el modal
            let myModal = new bootstrap.Modal(document.getElementById('directorioModal'));
            myModal.show();

            // Esperar a que el modal esté visible y luego aplicar DataTable
            setTimeout(() => {
                if ($.fn.DataTable.isDataTable('#tablaDirectorio')) {
                    $('#tablaDirectorio').DataTable().destroy();
                }
                $('#tablaDirectorio').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    responsive: true
                });
            }, 300); // pequeño delay para asegurar que el DOM esté listo
        })
        .catch(err => {
            document.getElementById('contenidoDirectorio').innerHTML = 'Error al cargar el directorio.';
        });
}

</script>
</body>
</html>
