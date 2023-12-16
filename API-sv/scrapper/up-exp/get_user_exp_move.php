<?php


// Obtener los movimientos que concidan con el id_expediente en la tabla movimientos
function getMovimientos($id_exp, $conexion)
{
    // obtengo los movimientos del expediente en la tabla user_exp_move, buscando por el $expedient['id_exp']
    try {
        $query = $conexion->prepare('SELECT * FROM user_exp_move WHERE id_exp = :id_exp');
        $query->execute([':id_exp' => $id_exp]);
        $userExpMoving = $query->fetchAll(PDO::FETCH_ASSOC);

        // var_dump($userExpMoving);
        return $userExpMoving;
    } catch (PDOException $e) {
        echo "Error al obtener los movimientos del expediente: " . $e->getMessage();
    }
}
