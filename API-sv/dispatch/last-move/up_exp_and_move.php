<?php

function upExpAndMove($conexion, $id_user, $expediente)
{
    try {
        // obtengo el id_exp de la tabla user_expedientes que tenga el $id_user, $caseNumber, $caseYear, $dispatch
        $query = $conexion->prepare('SELECT * FROM user_expedients WHERE id_user = :id_user AND numero_exp = :numero_exp AND anio_exp = :anio_exp AND dependencia = :dependencia');
        $query->execute([':id_user' => $id_user, ':numero_exp' => $expediente['numero_expediente'], ':anio_exp' => $expediente['anio_expediente'], ':dependencia' => $expediente['dependencia']]);
        $exp = $query->fetch();
    } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al consultar el expediente del usuario');
    }

    // valido que 'caratula' y 'reservado' existan en $expediente
    if (!isset($expediente['caratula']) || !isset($expediente['reservado'])) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error: faltan datos del expediente.');
        return;
    }
    // limpio las cadenas de textos antes de acutlizar el exp por si alguno esta mal cargado en la tabla anterior, limpio caratula y reservado
    $expediente['caratula'] = cleanAndEncode($expediente['caratula']);
    $expediente['reservado'] = cleanAndEncode($expediente['reservado']);
    // echo json_encode($expediente['caratula']);

    try {
        //actualizo el expediente en la tabla user_expedientes id_exp	id_lista_despacho	numero_exp	anio_exp	caratula	reservado	dependencia	tipo_lista id_user, con la informacion de $expediente
        $query = $conexion->prepare('UPDATE user_expedients SET id_lista_despacho = :id_lista_despacho, caratula = :caratula, reservado = :reservado, tipo_lista = :tipo_lista WHERE id_exp = :id_exp');
        $query->execute([':id_lista_despacho' => $expediente['id_lista_despacho'], ':caratula' => $expediente['caratula'], ':reservado' => $expediente['reservado'], ':tipo_lista' => $expediente['tipo_lista'], ':id_exp' => $exp['id_exp']]);
    } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al actualizar el expediente del usuario');
    }

    try {
        // echo json_encode($exp);

        // con el id_expediente obtengo todos los movimientos del expediente y los actualizo en la tabla user_movimientos
        $query = $conexion->prepare('SELECT * FROM movimientos WHERE id_expediente = :id_expediente');
        $query->execute([':id_expediente' => $expediente['id_expediente']]);
        $movements = $query->fetchAll();
    } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al consultar los movimientos del expediente');
    }
    // echo json_encode($exp);
    foreach ($movements as $movement) {
        // limpio texto, titutlo y despacho de los movimientos
        $movement['texto'] = cleanAndEncode($movement['texto']);
        $movement['titulo'] = cleanAndEncode($movement['titulo']);
        $movement['despacho'] = cleanAndEncode($movement['despacho']);
        try {
            $query = $conexion->prepare('INSERT INTO user_exp_move (id_exp, fecha_movimiento, estado, texto, titulo, despacho) VALUES (:id_exp, :fecha_movimiento, :estado, :texto, :titulo, :despacho)');
            $query->execute([
                ':id_exp' => $exp['id_exp'],
                ':fecha_movimiento' => $movement['fecha_movimiento'],
                ':estado' => $movement['estado'],
                ':texto' => $movement['texto'],
                ':titulo' => $movement['titulo'],
                ':despacho' => $movement['despacho']
            ]);
        } catch (\Throwable $th) {
            echo json_encode('error al actualizar los movimientos del expediente del usuario');
        }
    }
}
