<?php
require_once 'config.php';

// Consulta de logins
$logins = [];
$query = "SELECT name FROM sys.server_principals WHERE type_desc = 'SQL_LOGIN' ORDER BY name";
$stmt = sqlsrv_query($conn, $query);

if ($stmt !== false) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $logins[] = $row['name'];
    }
    sqlsrv_free_stmt($stmt);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Logins Existentes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Admin SQL Server</a>
  </div>
</nav>

<div class="container my-4">
    <h1>Logins Existentes (SQL_LOGIN)</h1>
    <table class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th>Nombre del Login</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logins as $login): ?>
            <tr>
                <td><?= htmlspecialchars($login) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Volver al Menú</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
