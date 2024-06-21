<?php

require_once __DIR__ . '/../../user/user_get.php';

function sendMailLastMove($conexion, $id_user, $expedient, $lastMovement)
{
    // obtengo la informacion del usuario usando user/user_get.php
    $userGet = new user_get($conexion, $id_user);
    $user = $userGet->getUsers();
    // Verifica que $user no sea null
    if ($user === null) {
        echo json_decode("Error: el usuario no fue encontrado.");
        return;
    }

    // echo json_encode($user);

    // Verifica que los índices existan en el array $user
    if (!isset($user['firstName']) || !isset($user['lastName']) || !isset($user['email'])) {
        echo json_decode("Error: faltan datos del usuario.");
        return;
    }

    // Asegúrate de que los índices en $expedient y $lastMovement existan
    if (
        !isset($expedient['numero_expediente']) || !isset($expedient['anio_expediente']) || !isset($expedient['caratula']) || !isset($expedient['reservado']) || !isset($expedient['dependencia']) || !isset($expedient['tipo_lista']) ||
        !isset($lastMovement['fecha_movimiento']) || !isset($lastMovement['estado']) || !isset($lastMovement['texto']) || !isset($lastMovement['titulo']) || !isset($lastMovement['despacho'])
    ) {
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
                    'movimientos' => [
                        [
                            'id_movimiento' => $lastMovement['id_movimiento'], // Asegúrate de tener este valor en $lastMovement
                            'id_expediente' => $lastMovement['id_expediente'], // Asegúrate de tener este valor en $lastMovement
                            'fecha_movimiento' => $lastMovement['fecha_movimiento'],
                            'estado' => $lastMovement['estado'],
                            'texto' => $lastMovement['texto'],
                            'titulo' => $lastMovement['titulo'],
                            'despacho' => $lastMovement['despacho']
                        ]
                    ]
                ]
            ]
        ]
    ];


    // Imprime el array $newsBy para depuración
    // echo json_encode($newsBy);
    // envio el la informacion al correo
    require_once __DIR__ . '/../scrapper/write_mail.php';
    $writeMail = new write_mail($conexion, $newsBy);
    $writeMail->write();
}
