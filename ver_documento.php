<?php
// Archivo para visualizar documentos de forma segura
require_once 'conexion.php';
require_once 'config_documentos.php';

// Verificar que se envió el parámetro
if (!isset($_GET['ruta']) || !isset($_GET['archivo'])) {
    die('Parámetros inválidos');
}

$ruta = $_GET['ruta'];
$archivo = $_GET['archivo'];

// Construir la ruta completa usando la función del config
$rutaCompleta = construirRutaArchivo($ruta, $archivo);

// Verificar que el archivo existe
if (!file_exists($rutaCompleta)) {
    die('Archivo no encontrado: ' . htmlspecialchars($rutaCompleta));
}

// Obtener la extensión del archivo
$extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

// Determinar el tipo MIME
$mimeTypes = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

// Establecer headers apropiados
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . basename($archivo) . '"');
header('Content-Length: ' . filesize($rutaCompleta));
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: 0');

// Leer y enviar el archivo
readfile($rutaCompleta);
exit;
?>
