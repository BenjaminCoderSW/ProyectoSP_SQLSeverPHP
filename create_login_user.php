<?php
require_once 'config.php';

$message = "";

// Procesar el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar los datos del formulario
    $loginName    = trim($_POST['LoginName'] ?? '');
    $userName     = trim($_POST['UserName'] ?? '');
    $databaseName = trim($_POST['DatabaseName'] ?? '');
    $password     = trim($_POST['Password'] ?? '');
    $roleName     = trim($_POST['RoleName'] ?? '');
    $schemaName   = trim($_POST['SchemaName'] ?? 'dbo');
    $tableName    = trim($_POST['TableName'] ?? '');
    $permission   = trim($_POST['Permission'] ?? '');

    // Validación básica
    if ($loginName === '' || $userName === '' || $databaseName === '' || $password === '') {
        $message = "❌ Debes ingresar al menos LoginName, UserName, DatabaseName y Password.";
    } else {
        // Llamada al stored procedure sp_CrearLoginUsuario
        $sql = "{CALL sp_CrearLoginUsuario(?,?,?,?,?,?,?,?)}";
        $params = [
            [$loginName,    SQLSRV_PARAM_IN],
            [$userName,     SQLSRV_PARAM_IN],
            [$databaseName, SQLSRV_PARAM_IN],
            [$password,     SQLSRV_PARAM_IN],
            [$roleName ?: null,   SQLSRV_PARAM_IN],
            [$schemaName ?: 'dbo',SQLSRV_PARAM_IN],
            [$tableName ?: null,  SQLSRV_PARAM_IN],
            [$permission ?: null, SQLSRV_PARAM_IN],
        ];

        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            $message = "✅ Todo salió correctamente.";
        } else {
            // Todo se creó correctamente
            $message = "✅ Todo salió correctamente.";
            sqlsrv_free_stmt($stmt);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Login y Usuario</title>
    <!-- Bootstrap CSS (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Admin SQL Server</a>
  </div>
</nav>

<div class="container my-5">
    <h1 class="mb-4">Crear Login y Usuario</h1>

    <?php if ($message): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="LoginName" class="form-label">Login Name</label>
                    <input type="text" class="form-control" name="LoginName" id="LoginName" required>
                </div>

                <div class="mb-3">
                    <label for="UserName" class="form-label">User Name</label>
                    <input type="text" class="form-control" name="UserName" id="UserName" required>
                </div>

                <div class="mb-3">
                    <label for="DatabaseName" class="form-label">Base de Datos</label>
                    <input type="text" class="form-control" name="DatabaseName" id="DatabaseName" required>
                </div>

                <div class="mb-3">
                    <label for="Password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" name="Password" id="Password" required>
                </div>

                <div class="mb-3">
                    <label for="RoleName" class="form-label">Rol (opcional)</label>
                    <input type="text" class="form-control" name="RoleName" id="RoleName" placeholder="db_datareader">
                </div>

                <div class="mb-3">
                    <label for="SchemaName" class="form-label">Esquema (opcional)</label>
                    <input type="text" class="form-control" name="SchemaName" id="SchemaName" value="dbo">
                </div>

                <div class="mb-3">
                    <label for="TableName" class="form-label">Tabla (opcional)</label>
                    <input type="text" class="form-control" name="TableName" id="TableName" placeholder="Clientes">
                </div>

                <div class="mb-3">
                    <label for="Permission" class="form-label">Permiso (opcional)</label>
                    <input type="text" class="form-control" name="Permission" id="Permission" placeholder="SELECT">
                </div>

                <button type="submit" class="btn btn-primary">Crear Login/Usuario</button>
                <a href="index.php" class="btn btn-secondary">Volver al Menú</a>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS (CDN) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
