<?php
require_once 'config.php';

// Variables para mensajes
$message = "";
$error = "";

// Rutas para respaldos según el entorno
// En tu script, está fijo en C:\Backups. Puedes parametrizarlo aquí.
$backupRootLocal = 'C:\\Backups';   // Windows físico
$backupRootDocker = '/var/opt/mssql/backups'; // Ejemplo en contenedor Docker (ajusta según tu configuración)

// Determinar qué ruta usar (ejemplo: según variable de entorno o manual)
$currentBackupPath = $backupRootLocal; // O $backupRootDocker

// Procesar el formulario al hacer POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbName = trim($_POST['DatabaseName'] ?? '');
    $backupType = trim($_POST['BackupType'] ?? '');

    // Validar datos básicos
    if ($dbName === '' || $backupType === '') {
        $error = "Debes especificar el nombre de la base de datos y el tipo de backup.";
    } else {
        // Llamar al procedimiento almacenado sp_BackupDatabase
        $sql = "{CALL dbo.sp_BackupDatabase(?,?)}";
        $params = [
            [$dbName, SQLSRV_PARAM_IN],
            [$backupType, SQLSRV_PARAM_IN]
        ];

        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            $errorInfo = print_r(sqlsrv_errors(), true);
            $error = "Error al ejecutar el backup: $errorInfo";
        } else {
            // Si no hay errores en la ejecución del SP, asumimos que fue exitoso
            $message = "Backup de tipo '$backupType' para la base de datos '$dbName' ejecutado correctamente.";
            sqlsrv_free_stmt($stmt);
        }
    }
}

// --- Lógica para listar archivos de backup ---
$backupsList = [];
// Recorre la carpeta raíz de backups (ej. C:\Backups) para mostrar los archivos
// Ten en cuenta que se listarán TODOS los backups de TODAS las bases. 
// Si quieres filtrar por base de datos, recorre la subcarpeta \Backups\<dbName>.

if (is_dir($currentBackupPath)) {
    // Escanea subdirectorios (nombres de bases)
    $databasesDirs = scandir($currentBackupPath);
    foreach ($databasesDirs as $dbDir) {
        if ($dbDir !== '.' && $dbDir !== '..') {
            $dbPath = $currentBackupPath . DIRECTORY_SEPARATOR . $dbDir;
            if (is_dir($dbPath)) {
                // Dentro de cada base, habrá subcarpetas: Full, Differential, Log
                $subDirs = scandir($dbPath);
                foreach ($subDirs as $subDir) {
                    if ($subDir !== '.' && $subDir !== '..') {
                        $backupTypePath = $dbPath . DIRECTORY_SEPARATOR . $subDir;
                        if (is_dir($backupTypePath)) {
                            // Listar archivos de backup en esa subcarpeta
                            $files = scandir($backupTypePath);
                            foreach ($files as $file) {
                                if ($file !== '.' && $file !== '..') {
                                    $fullPath = $backupTypePath . DIRECTORY_SEPARATOR . $file;
                                    $backupsList[] = [
                                        'dbName' => $dbDir,
                                        'backupType' => $subDir,
                                        'fileName' => $file,
                                        'fullPath' => $fullPath
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Backups</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Admin SQL Server</a>
  </div>
</nav>

<div class="container my-4">
    <h1>Gestión de Backups</h1>
    <p class="text-muted">Ejecuta un backup manualmente y revisa los archivos generados.</p>

    <!-- Mensajes de éxito o error -->
    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Formulario para ejecutar un backup -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-6">
                    <label for="DatabaseName" class="form-label">Nombre de la Base de Datos</label>
                    <input type="text" class="form-control" name="DatabaseName" id="DatabaseName" placeholder="Ej: MiBaseDeDatos" required>
                </div>
                <div class="col-md-6">
                    <label for="BackupType" class="form-label">Tipo de Backup</label>
                    <select name="BackupType" id="BackupType" class="form-select" required>
                        <option value="">-- Selecciona --</option>
                        <option value="FULL">FULL</option>
                        <option value="DIFFERENTIAL">DIFFERENTIAL</option>
                        <option value="LOG">LOG</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Ejecutar Backup</button>
                    <a href="index.php" class="btn btn-secondary">Volver al Menú</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de backups disponibles -->
    <h3>Backups Generados</h3>
    <?php if (!empty($backupsList)): ?>
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Base de Datos</th>
                    <th>Tipo de Backup</th>
                    <th>Nombre de Archivo</th>
                    <th>Descargar</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($backupsList as $bk): ?>
                <tr>
                    <td><?= htmlspecialchars($bk['dbName']) ?></td>
                    <td><?= htmlspecialchars($bk['backupType']) ?></td>
                    <td><?= htmlspecialchars($bk['fileName']) ?></td>
                    <td>
                        <!-- Link para descargar el archivo -->
                        <a class="btn btn-sm btn-success" 
                           href="download_backup.php?file=<?= urlencode($bk['fullPath']) ?>"
                           target="_blank">
                           Descargar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se han encontrado archivos de backup en <code><?= $currentBackupPath ?></code>.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
