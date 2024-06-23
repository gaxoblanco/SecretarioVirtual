<?php

function lastMoveUserExpediente($conexion, $lastMove, $id_expediente)
{
    try {
        // consulto en la tabla movimientos por todos los movimientos del expediente
        $query = $conexion->prepare('SELECT * FROM movimientos WHERE id_expediente = :id_expediente ORDER BY fecha_movimiento DESC');
        $query->execute([':id_expediente' => $id_expediente]);
        $lastMove = $query->fetch();

        // si el expediente no tiene movimientos
        if ($lastMove == null) {
            // Devolver mensaje de "error" en json
            // http_response_code(200);
            echo json_encode('El expediente aun no tiene movimientos, para sumar al correo de actualizacion');
            return;
        }

        // quito el id_movimiento y id_expediente
        unset($lastMove['id_movimiento']);
        unset($lastMove['id_expediente']);

        // Retorno todos los datos del ultimo movimiento sin índices numéricos
        $filteredLastMove = array_filter($lastMove, function ($key) {
            return !is_numeric($key);
        }, ARRAY_FILTER_USE_KEY);

        return $filteredLastMove;
    } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al consultar la ultima actualizacion de los movimientos para el expediente');
    }
}
