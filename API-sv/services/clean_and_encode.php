<?php
include_once __DIR__ . '/accent_markers.php';
// Función para limpiar y codificar las cadenas de texto
function cleanAndEncode($text)
{
    // Eliminar etiquetas HTML
    $text = strip_tags($text);
    // Reemplazar saltos de línea y tabulaciones con espacios
    $search = array("\r\n", "\n", "\r", "\t");
    $replace = ' ';
    $text = str_replace($search, $replace, $text);
    // Eliminar cualquier entidad HTML restante
    $text = preg_replace('/&[^;]+;/', '', $text);
    // valido que se importo bien la funcion addAccentMarkers
    if (!function_exists('addAccentMarkers')) {
        include_once __DIR__ . '/accent_markers.php';
        // mensaje de error
        echo json_encode('Error al importar la función addAccentMarkers');
    }
    // Cambio los caracteres con acento a su versión sin acento con un marcado para revertirlo después
    $text = addAccentMarkers($text);
    return $text;
}
