<?php

require_once __DIR__ . '/../../user/user_get.php';
require_once __DIR__ . '/../../scrapper/write_mail.php';
require_once __DIR__ . '/../../services/accent_markers.php';

function sendMailLastMove($conexion, $id_user, $expedient, $lastMovement)
{
    // Verifica que los datos llegan correctamente 
    if (!isset($id_user) || !isset($expedient)) {
        http_response_code(400);
        echo json_encode("Error: faltan datos.");
        return;
    }
    // obtengo la informacion del usuario usando user/user_get.php
    $userGet = new user_get($conexion, $id_user);
    $userJson = $userGet->getUsers(); // devuelve un json_encode
    // Decodifico el JSON a un array asociativo
    $user = json_decode($userJson, true);
    // Verifica que $user no sea null
    if ($user === null) {
        http_response_code(400);
        echo json_decode("Error: el usuario no fue encontrado.");
        return;
    }

    // echo json_encode($user);

    // Verifica que los índices existan en el array $user
    if (!isset($user['firstName']) || !isset($user['lastName']) || !isset($user['email'])) {
        http_response_code(400);
        echo json_decode("Error: faltan datos del usuario.");
        return;
    }

    // Asegúrate de que los índices en $expedient y $lastMovement existan
    if (
        !isset($expedient['numero_expediente']) || !isset($expedient['anio_expediente']) || !isset($expedient['caratula']) || !isset($expedient['reservado']) || !isset($expedient['dependencia']) || !isset($expedient['tipo_lista'])
    ) {
        http_response_code(400);
        echo json_decode("Error: faltan datos del expediente o del último movimiento.");
        return;
    }

    // $newsBy es un array con id_user, name, email, un array de expedients que tiene un array de movimientos
    $newsBy = [
        [
            'id_user' => $id_user,
            'name' => $user['firstName'] . ' ' . $user['lastName'],
            'email' => $user['email'],
            'expedients' => [
                [
                    'id_exp' => $expedient['id_expediente'], // Asegúrate de tener este valor en $expedient
                    'numero_exp' => $expedient['numero_expediente'],
                    'anio_exp' => $expedient['anio_expediente'],
                    'caratula' => $expedient['caratula'],
                    'reservado' => $expedient['reservado'],
                    'dependencia' => $expedient['dependencia'],
                    'tipo_lista' => $expedient['tipo_lista'],
                    'movimientos' => []
                ]
            ]
        ]
    ];
    // si $lasMovement es distinto de null lo agrego al $newsBy[expedients][movements]
    if ($lastMovement !== null) {
        // Convierto los caracteres especiales con revertAccentMarkers
        $lastMovement['texto'] = revertAccentMarkers($lastMovement['texto']);
        $lastMovement['titulo'] = revertAccentMarkers($lastMovement['titulo']);
        $lastMovement['estado'] = revertAccentMarkers($lastMovement['estado']);

        $newsBy[0]['expedients'][0]['movimientos'] = [
            [
                'fecha_movimiento' => $lastMovement['fecha_movimiento'],
                'estado' => $lastMovement['estado'],
                'texto' => $lastMovement['texto'],
                'titulo' => $lastMovement['titulo'],
                'despacho' => $lastMovement['despacho']
            ]
        ];
    }
    // echo json_encode($newsBy[0]['expedients'][0]['movements']);

    //valido que $newsBy sea un objeto
    if (!is_array($newsBy)) {
        http_response_code(400);
        echo json_encode("Error: no se pudo crear el objeto newsBy.");
        return;
    }
    // envio el la informacion al correo
    // valido que se importo correctamente
    if (!class_exists('write_mail')) {
        echo json_encode("Error: no se pudo importar la clase write_mail.");
        return;
    }
    $writeMail = new write_mail($conexion, $newsBy);
    $writeMail->write();
}
