<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Resolutores en Vacaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <!-- Botones de DataTables -->
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-5" style="background-color: #f5eeb0; padding: 10px;">
    <h2 class="mb-4">Resolutores en Vacaciones y Guardias</h2>

    <div class="accordion" id="accordionVacaciones">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingTabla">
                <button 
                    class="accordion-button collapsed" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseTabla" 
                    aria-expanded="false" 
                    aria-controls="collapseTabla">
                    Ver / Ocultar Tabla de Vacaciones
                </button>
            </h2>
            <div id="collapseTabla" class="accordion-collapse collapse" aria-labelledby="headingTabla">
                <div class="accordion-body">
                    <h3>Vacaciones</h3>
                    <!-- Formulario de búsqueda -->
                    <form id="buscarForm" class="d-flex align-items-center gap-2 mb-3">
                        <label for="fecha_seleccionada" class="mb-0">Seleccione fecha y hora:</label>
                        <select name="fecha_seleccionada" id="fecha_seleccionada" class="form-select" required style="max-width: 300px;">
                            <option value="">--Cargando fechas--</option>
                        </select>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>

                    <table id="tablaVacaciones" class="table table-striped">
                      <thead>
                        <tr>
                          <th>Resolutor Vacaciones</th>
                          <th>Resolutor Guardia</th>
                          <th>Teléfono Contacto Resolutor</th>
                          <th>Correo Resolutor</th>
                          <th>Fecha Inicio</th>
                          <th>Fecha Fin</th>
                          <th>Jefe Inmediato</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php include("../Controllers/consulta.php"); ?>
                      </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true" style="overflow-x:auto;">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="infoModalLabel">Resultados de la búsqueda</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body" id="contenidoModal">
        <!-- Aquí irá la tabla dinámica -->
        <div class="text-center">Cargando...</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- DataTables Botones + exportación -->
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#tablaVacaciones').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const selectFecha = document.getElementById("fecha_seleccionada");
    const form = document.getElementById("buscarForm");
    const modalEl = document.getElementById('infoModal');
    const contenidoModal = document.getElementById("contenidoModal");

    // Inicializa el modal Bootstrap
    const bootstrapModal = new bootstrap.Modal(modalEl);

    // Cargar fechas para el select
    fetch('../Controllers/buscarVacacionesPorFecha.php')
    .then(response => response.json())
    .then(data => {
        selectFecha.innerHTML = '<option value="">--Seleccione--</option>';
        data.forEach(fecha => {
            const option = document.createElement('option');
            option.value = fecha.valor;
            // Mostrar fecha y hora juntos
            option.textContent = `${fecha.texto} - ${fecha.hora}`;
            selectFecha.appendChild(option);
        });
    })
    .catch(() => {
        selectFecha.innerHTML = '<option value="">Error al cargar fechas</option>';
    });
    // Variable para la instancia del DataTable dentro del modal (para poder destruirla y crearla cada búsqueda)
    let dataTableModal = null;

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const fechaSeleccionada = selectFecha.value;
        if (!fechaSeleccionada) {
            alert('Por favor selecciona una fecha.');
            return;
        }

        contenidoModal.innerHTML = '<div class="text-center">Buscando información...</div>';
        bootstrapModal.show();

        const formData = new FormData();
        formData.append('fecha_seleccionada', fechaSeleccionada);

        fetch('../Controllers/consultarFechaVacaciones.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && Array.isArray(data.data) && data.data.length > 0) {
                // Construir la tabla HTML
                let tablaHTML = `
                    <table id="tablaModal" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Resolutor Vacaciones</th>
                                <th>Resolutor Guardia</th>
                                <th>Teléfono Contacto</th>
                                <th>Correo Resolutor</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Término</th>
                                <th>Jefe Inmediato</th>
                                <th>Status</th>
                                <th>Fecha Creación</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                data.data.forEach(d => {
                    tablaHTML += `
                        <tr>
                            <td>${d.id}</td>
                            <td>${d.Resolutor_Vacaciones}</td>
                            <td>${d.Resolutor_Guardia}</td>
                            <td>${d.Telefono_Contacto_Resolutor ?? ''}</td>
                            <td>${d.Correo_Resolutor ?? ''}</td>
                            <td>${d.Fecha_Inicio ?? ''}</td>
                            <td>${d.Fecha_Termino ?? ''}</td>
                            <td>${d.Jefe_Inmediato ?? ''}</td>
                            <td>${d.status == 1 ? 'Activo' : 'Inactivo'}</td>
                            <td>${d.fecha_creacion}</td>
                        </tr>
                    `;
                });

                tablaHTML += `
                        </tbody>
                    </table>
                `;

                contenidoModal.innerHTML = tablaHTML;

                // Si ya hay una instancia previa del DataTable, la destruimos para evitar problemas
                if (dataTableModal) {
                    dataTableModal.destroy();
                    dataTableModal = null;
                }

                // Inicializar DataTable en la tabla dentro del modal
                dataTableModal = $('#tablaModal').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    lengthMenu: [5, 10, 25, 50],
                    pageLength: 5,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: ['copy', 'csv', 'excel', 'print']
                });

            } else if (data.status === 'success' && Array.isArray(data.data) && data.data.length === 0) {
                contenidoModal.innerHTML = '<p>No se encontraron registros para la fecha seleccionada.</p>';
            } else if (data.status === 'not_found') {
                contenidoModal.innerHTML = '<p>No se encontró información para la fecha seleccionada.</p>';
            } else {
                contenidoModal.innerHTML = `<p>Error: ${data.message || 'Error desconocido'}</p>`;
            }
        })
        .catch(() => {
            contenidoModal.innerHTML = '<p>Error al realizar la búsqueda.</p>';
        });
    });
});
</script>
</body>
</html>
