<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Menú Principal</title>
    <!-- Cargar Bootstrap desde CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Admin SQL Server</a>
  </div>
</nav>

<div class="container my-4">
    <h1 class="mb-4">Menú Principal</h1>
    <div class="row g-4">

        <!-- Card: Crear Base de Datos -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Crear Base de Datos</h5>
                    <p class="card-text">Genera una nueva base de datos, con la opción de agregar un filegroup secundario.</p>
                    <a href="create_database.php" class="btn btn-primary">Ir &raquo;</a>
                </div>
            </div>
        </div>

        <!-- Card: Ver Bases de Datos -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ver Bases de Datos</h5>
                    <p class="card-text">Consulta las bases de datos existentes en el servidor.</p>
                    <a href="view_databases.php" class="btn btn-success">Ir &raquo;</a>
                </div>
            </div>
        </div>

        <!-- Card: Crear Login/Usuario -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Crear Login y Usuario</h5>
                    <p class="card-text">Crea logins y usuarios, asignando roles y permisos opcionales.</p>
                    <a href="create_login_user.php" class="btn btn-warning">Ir &raquo;</a>
                </div>
            </div>
        </div>

        <!-- Card: Ver Logins Existentes -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ver Logins</h5>
                    <p class="card-text">Lista los logins existentes en el servidor SQL.</p>
                    <a href="view_logins.php" class="btn btn-info">Ir &raquo;</a>
                </div>
            </div>
        </div>
        
        <!-- Card: Ejecutar Backup -->
        <div class="col-md-6 col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ejecutar Backup</h5>
                    <p class="card-text">Realiza backups completos, diferenciales o de logs de transacciones.</p>
                    <a href="backup.php" class="btn btn-secondary">Ir &raquo;</a>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
