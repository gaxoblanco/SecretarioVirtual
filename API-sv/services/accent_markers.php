<?php

// Función para agregar marcadores de acento a los caracteres especiales
function addAccentMarkers($text)
{
    $map = [
        'á' => '?a', 'à' => '?a', 'â' => '?a', 'ã' => '?a', 'ä' => '?a', 'å' => '?a', 'æ' => '?ae',
        'é' => '?e', 'è' => '?e', 'ê' => '?e', 'ë' => '?e',
        'í' => '?i', 'ì' => '?i', 'î' => '?i', 'ï' => '?i',
        'ó' => '?o', 'ò' => '?o', 'ô' => '?o', 'õ' => '?o', 'ö' => '?o', 'ø' => '?o',
        'ú' => '?u', 'ù' => '?u', 'û' => '?u', 'ü' => '?u',
        'ñ' => '?n', 'ç' => '?c',
        'Á' => '?A', 'À' => '?A', 'Â' => '?A', 'Ã' => '?A', 'Ä' => '?A', 'Å' => '?A', 'Æ' => '?AE',
        'É' => '?E', 'È' => '?E', 'Ê' => '?E', 'Ë' => '?E',
        'Í' => '?I', 'Ì' => '?I', 'Î' => '?I', 'Ï' => '?I',
        'Ó' => '?O', 'Ò' => '?O', 'Ô' => '?O', 'Õ' => '?O', 'Ö' => '?O', 'Ø' => '?O',
        'Ú' => '?U', 'Ù' => '?U', 'Û' => '?U', 'Ü' => '?U',
        'Ñ' => '?N', 'Ç' => '?C',
    ];

    $originalText = $text;
    $modifiedText = strtr($text, $map);

    if ($originalText === $modifiedText) {
        echo "Advertencia: No se realizaron cambios en el texto durante addAccentMarkers\n";
        echo "Texto original: " . $originalText . "\n";
        echo "Codificación del texto: " . mb_detect_encoding($originalText) . "\n";
    } else {
        echo "Se realizaron cambios en el texto durante addAccentMarkers\n";
        echo "Texto original: " . $originalText . "\n";
        echo "Texto modificado: " . $modifiedText . "\n";
    }

    return $modifiedText;
}

// Función para revertir los marcadores de acento a los caracteres originales
function revertAccentMarkers($text)
{
    $map = [
        '?a' => 'á', '?e' => 'é', '?i' => 'í', '?o' => 'ó', '?u' => 'ú',
        '?A' => 'Á', '?E' => 'É', '?I' => 'Í', '?O' => 'Ó', '?U' => 'Ú',
        '?n' => 'ñ', '?N' => 'Ñ', '?c' => 'ç', '?C' => 'Ç',
        'N_o' => 'N°',
        '?ae' => 'æ', '?AE' => 'Æ', '?oe' => 'ø', '?OE' => 'Œ',
        '?ss' => 'ß', '?i' => '¿', '?!' => '¡'
    ];

    foreach ($map as $replacement => $accentedChar) {
        $text = str_replace($replacement, $accentedChar, $text);
    }

    return $text;
}
