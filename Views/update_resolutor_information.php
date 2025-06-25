<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Subir CSV de Empleados</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-light">

    <div class="container-sm mt-5">
        <div class="card shadow mx-auto" style="max-width: 480px;">
            <div class="card-header bg-primary text-white text-center">
                <h4 class="mb-0">Subir archivo CSV de resoluciones</h4>
            </div>
            <div class="card-body">
                <form method="post" action="../Controllers/cargarDatosResoluciones.php" enctype="multipart/form-data" id="formulario">
                    <div class="mb-3">
                        <label for="archivo_csv" class="form-label">Seleccionar archivo CSV</label>
                        <input type="file" class="form-control" id="archivo_csv" name="archivo_csv" accept=".csv" required />
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Cargar</button>
                        <a href="../docs/incidentes.csv" download class="btn btn-outline-primary">Descargar plantilla</a>
                        <a href="../Views/menu.php" class="btn btn-secondary">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('formulario').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevenir el envío automático del formulario

        Swal.fire({
            title: '¿Está seguro que desea actualizar la información?',
            text: 'Se eliminaran todos los datos anteriores',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, actualizar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar alerta de carga después de la confirmación
                Swal.fire({
                    title: 'Cargando datos...',
                    html: `
                        <div class="text-center mt-3">
                            <p><strong>Espere mientras se cargan los datos</strong></p>
                            <div class="progress" style="height: 20px;">
                                <div id="barra" class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    `,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();

                        // Simulación de barra durante 20 segundos
                        let progreso = 0;
                        const barra = document.getElementById('barra');
                        const duracion = 3000;
                        const interval = 200;
                        const paso = 100 / (duracion / interval);

                        const timer = setInterval(() => {
                            progreso += paso;
                            barra.style.width = Math.min(progreso, 100) + '%';
                            barra.setAttribute('aria-valuenow', Math.min(progreso, 100));

                            if (progreso >= 100) {
                                clearInterval(timer);
                            }
                        }, interval);
                    }
                });

                // Finalmente enviar el formulario
                e.target.submit();
            }
        });
    });
</script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
