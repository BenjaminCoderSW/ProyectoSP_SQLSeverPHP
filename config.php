<?php
// Datos de conexión a SQL Server
$serverName = "localhost"; // O el nombre de tu servidor SQL Server
$connectionOptions = [
    "Database" => "AdminDB",            // Usar AdminDB en lugar de master
    "UID"      => "SuperAdmin",          // Nuevo usuario creado
    "PWD"      => "P@ssw0rd", // La contraseña que definiste
    "CharacterSet" => "UTF-8",
    "TrustServerCertificate" => true
];

// Conectar con SQL Server
$conn = sqlsrv_connect($serverName, $connectionOptions);

if ($conn === false) {
    die("❌ Error en la conexión: " . print_r(sqlsrv_errors(), true));
}
?>
