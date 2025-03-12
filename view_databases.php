<?php
require_once 'config.php';

// Consulta de bases de datos
$databases = [];
$query = "SELECT name FROM sys.databases where name not in ('master', 'model','msdb','tempdb') ORDER BY name";
$stmt = sqlsrv_query($conn, $query);

if ($stmt !== false) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $databases[] = $row['name'];
    }
    sqlsrv_free_stmt($stmt);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Bases de Datos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Admin SQL Server</a>
  </div>
</nav>

<div class="container my-4">
    <h1>Bases de Datos Existentes</h1>
    <table class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th>Nombre</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($databases as $db): ?>
            <tr>
                <td><?= htmlspecialchars($db) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Volver al Menú</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
