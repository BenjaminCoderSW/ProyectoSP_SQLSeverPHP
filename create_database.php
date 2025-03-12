<?php
require_once 'config.php';

// Variable para mensajes
$messageDB = "";

// Si el formulario se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbName            = trim($_POST['DBName'] ?? '');
    $dataFilePath      = trim($_POST['DataFilePath'] ?? '');
    $logFilePath       = trim($_POST['LogFilePath'] ?? '');
    $dataFileSizeMB    = intval($_POST['DataFileSizeMB'] ?? 50);
    $logFileSizeMB     = intval($_POST['LogFileSizeMB'] ?? 25);
    $dataFileGrowth    = trim($_POST['DataFileGrowth'] ?? '25%');
    $logFileGrowth     = trim($_POST['LogFileGrowth'] ?? '25%');
    $dataFileMaxSize   = intval($_POST['DataFileMaxSize'] ?? 400);
    $secFGName         = trim($_POST['SecFGName'] ?? '');
    $secDataFilePath   = trim($_POST['SecDataFilePath'] ?? '');
    $secDataFileSizeMB = intval($_POST['SecDataFileSizeMB'] ?? 50);
    $secDataFileGrowth = trim($_POST['SecDataFileGrowth'] ?? '25%');
    $secDataFileMaxSize= intval($_POST['SecDataFileMaxSize'] ?? 400);

    // Validación básica
    if ($dbName === '' || $dataFilePath === '' || $logFilePath === '') {
        $messageDB = "❌ Debes especificar al menos el nombre de la BD, ruta de data y ruta de log.";
    } else {
        // Llamar al SP sp_CrearBaseDeDatos
        $sql = "{CALL dbo.sp_CrearBaseDeDatos(?,?,?,?,?,?,?,?,?,?,?,?,?)}";
        $params = [
            [$dbName,               SQLSRV_PARAM_IN],
            [$dataFilePath,         SQLSRV_PARAM_IN],
            [$logFilePath,          SQLSRV_PARAM_IN],
            [$dataFileSizeMB,       SQLSRV_PARAM_IN],
            [$logFileSizeMB,        SQLSRV_PARAM_IN],
            [$dataFileGrowth,       SQLSRV_PARAM_IN],
            [$logFileGrowth,        SQLSRV_PARAM_IN],
            [$dataFileMaxSize,      SQLSRV_PARAM_IN],
            // Parámetros para filegroup secundario
            [$secFGName ?: null,    SQLSRV_PARAM_IN],
            [$secDataFilePath ?: null, SQLSRV_PARAM_IN],
            [$secDataFileSizeMB,    SQLSRV_PARAM_IN],
            [$secDataFileGrowth,    SQLSRV_PARAM_IN],
            [$secDataFileMaxSize,   SQLSRV_PARAM_IN],
        ];

        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            $errors = print_r(sqlsrv_errors(), true);
            $messageDB = "❌ Error al crear la base de datos: $errors";
        } else {
            $messageDB = "✅ Base de datos creada correctamente (o intento realizado).";
            sqlsrv_free_stmt($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Base de Datos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Admin SQL Server</a>
  </div>
</nav>

<div class="container my-4">
    <h1>Crear Base de Datos</h1>

    <?php if ($messageDB): ?>
        <div class="alert alert-info"><?= htmlspecialchars($messageDB) ?></div>
    <?php endif; ?>

    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <label for="DBName" class="form-label">Nombre de la BD</label>
            <input type="text" name="DBName" id="DBName" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label for="DataFilePath" class="form-label">Ruta del archivo de datos (mdf)</label>
            <input type="text" name="DataFilePath" id="DataFilePath" class="form-control" placeholder="C:\SQLData\MiBD.mdf" required>
        </div>

        <div class="col-md-6">
            <label for="LogFilePath" class="form-label">Ruta del archivo de log (ldf)</label>
            <input type="text" name="LogFilePath" id="LogFilePath" class="form-control" placeholder="C:\SQLData\MiBD.ldf" required>
        </div>

        <div class="col-md-3">
            <label for="DataFileSizeMB" class="form-label">Tamaño inicial (MB)</label>
            <input type="number" name="DataFileSizeMB" id="DataFileSizeMB" class="form-control" value="50">
        </div>

        <div class="col-md-3">
            <label for="LogFileSizeMB" class="form-label">Tamaño log inicial (MB)</label>
            <input type="number" name="LogFileSizeMB" id="LogFileSizeMB" class="form-control" value="25">
        </div>

        <div class="col-md-3">
            <label for="DataFileGrowth" class="form-label">Crecimiento data</label>
            <input type="text" name="DataFileGrowth" id="DataFileGrowth" class="form-control" value="25%">
        </div>

        <div class="col-md-3">
            <label for="LogFileGrowth" class="form-label">Crecimiento log</label>
            <input type="text" name="LogFileGrowth" id="LogFileGrowth" class="form-control" value="25%">
        </div>

        <div class="col-md-3">
            <label for="DataFileMaxSize" class="form-label">Tamaño máx data (MB)</label>
            <input type="number" name="DataFileMaxSize" id="DataFileMaxSize" class="form-control" value="400">
        </div>

        <hr class="my-4">

        <h4>Opcional: Filegroup Secundario</h4>
        <div class="col-md-4">
            <label for="SecFGName" class="form-label">Nombre Filegroup</label>
            <input type="text" name="SecFGName" id="SecFGName" class="form-control" placeholder="Secundario">
        </div>

        <div class="col-md-4">
            <label for="SecDataFilePath" class="form-label">Ruta archivo .ndf</label>
            <input type="text" name="SecDataFilePath" id="SecDataFilePath" class="form-control" placeholder="C:\SQLData\MiBD_Sec.ndf">
        </div>

        <div class="col-md-4">
            <label for="SecDataFileSizeMB" class="form-label">Tamaño inicial FG (MB)</label>
            <input type="number" name="SecDataFileSizeMB" id="SecDataFileSizeMB" class="form-control" value="50">
        </div>

        <div class="col-md-4">
            <label for="SecDataFileGrowth" class="form-label">Crecimiento FG</label>
            <input type="text" name="SecDataFileGrowth" id="SecDataFileGrowth" class="form-control" value="25%">
        </div>

        <div class="col-md-4">
            <label for="SecDataFileMaxSize" class="form-label">Tamaño máx FG (MB)</label>
            <input type="number" name="SecDataFileMaxSize" id="SecDataFileMaxSize" class="form-control" value="400">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Crear Base de Datos</button>
            <a href="index.php" class="btn btn-secondary">Volver al Menú</a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
