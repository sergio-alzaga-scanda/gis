
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

<!-- DataTables y Buttons CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css"/>

<!-- jQuery, DataTables y Buttons JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- Buttons y dependencias para PDF -->
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>

<script>
  $(document).ready(function() {
    $('#tablaEmpleadoSeleccionado').DataTable({
      dom: 'Bfrtip',   // Define la posición del botón
      buttons: [
        {
          extend: 'pdfHtml5',
          text: 'Exportar a PDF',
          title: 'Empleado Seleccionado',
          exportOptions: {
            columns: ':visible'
          }
        }
      ],
      paging: false, // Opcional, según si quieres paginación o no
      searching: false, // Opcional, si quieres quitar búsqueda
      info: false // Opcional, quitar info
    });

    $('#tablaCategoriasSeleccionadas').DataTable({
      dom: 'Bfrtip',
      buttons: [
        {
          extend: 'pdfHtml5',
          text: 'Exportar a PDF',
          title: 'Categorías Seleccionadas',
          exportOptions: {
            columns: ':visible'
          }
        }
      ],
      paging: false,
      searching: false,
      info: false
    });
  });

function getCookie(nombre) {
    const cookies = document.cookie.split("; ");
    for (let c of cookies) {
        const [key, val] = c.split("=");
        if (key === nombre) return decodeURIComponent(val);
    }
    return null;
}

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

</script>
