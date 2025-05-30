<?php

function token_expiration_state($conexion, $user)
{
    //valido que sea escalar
    if (!is_scalar($user)) {
        http_response_code(500);
        echo json_encode(['message' => 'Error en el token_expiration: id_user no es válido']);
        return false;
    }
    //obtengo la valor de token_expiration con ese id_user
    try {
        $sql = "SELECT token_expiration FROM users WHERE id_user = :id_user";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':id_user', $user);
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
