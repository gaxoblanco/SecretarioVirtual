<?php

function token_expiration_state_secretary($conexion, $secretary)
{
    //valido que sea escalar
    if (!is_scalar($secretary)) {
        http_response_code(500);
        echo json_encode(['message' => 'Error en el token_expiration: secreataryId no es válido']);
        return false;
    }
    //obtengo la valor de token_expiration con ese secreataryId
    try {
        $sql = "SELECT token_expiration FROM secretaries WHERE secreataryId = :secreataryId";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':secreataryId', $secretary);
        $stmt->execute();
        $token_expiration = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Error en el token_expiration']);
    }

    //obtenemos la fecha actual
    $date = date('Y-m-d H:i:s');

    //comparamos la fecha actual con la fecha de expiracion del token
    if ($date > $token_expiration) {
        echo json_encode(['message' => 'El token ha expirado']);
    }

    return true;
}
