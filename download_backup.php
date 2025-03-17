<?php
// Este script se encarga de enviar el archivo .bak o .trn al cliente
// de forma que el navegador inicie la descarga.

if (!isset($_GET['file'])) {
    die("No se especificó ningún archivo.");
}

$filePath = $_GET['file'];

// Seguridad básica: evita subir niveles de carpeta
// (podrías implementar un filtrado más estricto si deseas)
if (strpos($filePath, '..') !== false) {
    die("Ruta de archivo no válida.");
}

// Verificar que el archivo exista
if (!file_exists($filePath)) {
    die("El archivo no existe o la ruta es incorrecta.");
}

// Forzar la descarga
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($filePath));

// Leer el archivo y enviarlo
readfile($filePath);
exit;
