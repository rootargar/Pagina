<?php
/**
 * Configuración de rutas para documentos
 * 
 * Este archivo centraliza la configuración de rutas para facilitar el mantenimiento
 * Si necesitas cambiar la ubicación de los archivos, solo modifica este archivo
 */

// Ruta base donde están almacenados los documentos
// En producción: cambiar según la ubicación del servidor
define('RUTA_BASE_DOCUMENTOS', 'C:/xampp/htdocs/');

// Otras configuraciones relacionadas con documentos
define('PERMITIR_DESCARGAS', true);
define('EXTENSIONES_PERMITIDAS', ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx']);

/**
 * Función auxiliar para construir rutas de archivos
 * 
 * @param string $rutaRelativa Ruta desde la base de datos
 * @param string $nombreArchivo Nombre del archivo
 * @return string Ruta completa del archivo
 */
function construirRutaArchivo($rutaRelativa, $nombreArchivo) {
    // Convertir barras invertidas a barras normales
    $rutaRelativa = str_replace('\\', '/', $rutaRelativa);
    
    // Construir la ruta completa
    return RUTA_BASE_DOCUMENTOS . $rutaRelativa . $nombreArchivo;
}

/**
 * Verificar si un archivo existe
 * 
 * @param string $rutaRelativa Ruta desde la base de datos
 * @param string $nombreArchivo Nombre del archivo
 * @return bool True si existe, False si no
 */
function archivoExiste($rutaRelativa, $nombreArchivo) {
    $rutaCompleta = construirRutaArchivo($rutaRelativa, $nombreArchivo);
    return file_exists($rutaCompleta);
}
?>
