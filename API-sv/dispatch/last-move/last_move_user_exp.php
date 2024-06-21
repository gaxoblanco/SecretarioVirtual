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
            http_response_code(200);
            echo json_encode('El expediente aun no tiene movimientos, no se envia correo con la ultima actualizacion');
            return;
        }

        // retorno todos los datos del ultimo movimiento
        return $lastMove;
    } catch (\Throwable $th) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al consultar la ultima actualizacion de los movimientos para el expediente');
    }
}
