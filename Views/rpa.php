<?php
include("../Controllers/bd.php");

// Obtener valores actuales
$query = "SELECT * FROM configuracion_busqueda LIMIT 1";
$result = $conn->query($query);
$config = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Configuración RPA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Configurador de RPA</h5>
        </div>

        <div class="card-body">

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">Configuración actualizada correctamente.</div>
            <?php endif; ?>

            <form method="POST" action="../Controllers/configurador_rpa.php">

                <div class="mb-3">
                    <label class="form-label">Periodo de búsqueda</label>
                    <select name="periodo_busqueda" class="form-select" required>
                        <?php
                        $periodos = ["1 Día", "1 Semana", "2 Semanas", "1 Mes", "3 Meses", "6 Meses"];
                        foreach ($periodos as $p) {
                            $sel = ($config['periodo_busqueda'] == $p) ? "selected" : "";
                            echo "<option value='$p' $sel>$p</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Hora de ejecución</label>
                    <input type="time" name="hora_ejecucion" class="form-control"
                           value="<?= $config['hora_ejecucion']; ?>" required>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" name="ejecucion_activa"
                        <?= ($config['ejecucion_activa'] ? "checked" : "") ?>>
                    <label class="form-check-label">Activar ejecución</label>
                </div>

                <button class="btn btn-primary">Guardar configuración</button>

            </form>

        </div>
    </div>

</div>

</body>
</html>
