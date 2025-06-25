<?php
session_start();
if (!$_SESSION['usuario']) {
    header("Location: ../index.php"); 
    exit;
}

include("../Controllers/bd.php");

$localidad = isset($_GET['localidad']) ? $_GET['localidad'] : '';

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
    <title>Grupos de Soporte</title>
</head>
<body>
    <h2>Filtrar por Localidad</h2>
    <form method="GET" action="ver_grupos.php">
        <button type="submit" name="localidad" value="México">Ver México</button>
        <button type="submit" name="localidad" value="Europa">Ver Europa</button>
    </form>

    <h3>Resultados para: <?= htmlspecialchars($localidad ?: 'Todas') ?></h3>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Localidad</th>
                <th>Grupo Torre</th>
                <th>Resolutores</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Grupo Distribución</th>
                <th>Gerente Responsable</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($fila['localidad']) ?></td>
                    <td><?= htmlspecialchars($fila['grupo_torre']) ?></td>
                    <td><?= htmlspecialchars($fila['resolutores']) ?></td>
                    <td><?= htmlspecialchars($fila['correo']) ?></td>
                    <td><?= htmlspecialchars($fila['telefono']) ?></td>
                    <td><?= htmlspecialchars($fila['grupo_distribucion']) ?></td>
                    <td><?= htmlspecialchars($fila['gerente_responsable']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php
    if (isset($stmt)) $stmt->close();
    $conn->close();
    ?>
</body>
</html>