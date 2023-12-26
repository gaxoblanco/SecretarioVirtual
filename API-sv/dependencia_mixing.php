<?php

// tomo el json tipos_listas_y_dependencias y con una funcion con el id devuelvo el valor
function textDependencia($id_dependencia)
{
    // Obtengo el json
    $json = file_get_contents('./tipos_listas_y_dependencias.json');
    // Lo convierto en un array asociativo
    $data = json_decode($json, true);

    // Verifico si el JSON no se decodificó correctamente o no tiene la estructura esperada
    if (!$data || !isset($data['dependencias']) || !is_array($data['dependencias'])) {
        // Manejo del caso cuando el JSON no tiene la estructura esperada
        return 'error en la estructura del JSON';
    }

    // Verifico si $id_dependencia existe en el array de dependencias
    if (array_key_exists($id_dependencia, $data['dependencias'])) {
        return $data['dependencias'][$id_dependencia];
    } else {
        return 'dependencia';
    }
}

// funciuon que recive el textDependencia y obtiene su id para devolverlo
function idDependencia($dependencia)
{
    // Obtengo el json
    $json = file_get_contents('./tipos_listas_y_dependencias.json');
    // Lo convierto en un array asociativo
    $data = json_decode($json, true);

    // Verifico si el JSON no se decodificó correctamente o no tiene la estructura esperada
    if (!$data || !isset($data['dependencias']) || !is_array($data['dependencias'])) {
        // Manejo del caso cuando el JSON no tiene la estructura esperada
        return 'error en la estructura del JSON';
    }

    // Busco la dependencia en el array y devuelvo su ID si la encuentro
    foreach ($data['dependencias'] as $id => $nombre) {
        if ($nombre === $dependencia) {
            return $id;
        }
    }

    // Manejo del caso cuando la dependencia no se encuentra
    return 'dependencia no encontrada';
}
