
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
<div class="container mt-5" style="background-color: #f5eeb0;  padding-right: 10px; padding-left: 10px; padding-bottom: 10px; padding-top: 10px;" >
    <h2 class="mb-4">Resolutores en Vacaciones y Gurdias</h2>

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
        // Opciones aquí si quieres
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        }
    });
});

</script>
</body>
</html>
