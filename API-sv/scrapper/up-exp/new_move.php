<?php
// 	id_move 	fecha_movimiento 	estado 	texto 	titulo 	despacho 	id_exp 
function newMove($expMoving, $idExp, $conexion)
{
    //itero por el array $expMoving y en cada iteracion agrego el movimiento en la tabla user_exp_move
    foreach ($expMoving as $move) {
        // echo "move -> <pre>" . var_dump($move) . "</pre>";
        try {
            $query = $conexion->prepare('INSERT INTO user_exp_move (fecha_movimiento, estado, texto, titulo, despacho, id_exp) VALUES (:fecha_movimiento, :estado, :texto, :titulo, :despacho, :id_exp)');
            $query->execute([
                ':fecha_movimiento' => $move['fecha_movimiento'],
                ':estado' => $move['estado'],
                ':texto' => $move['texto'],
                ':titulo' => $move['titulo'],
                ':despacho' => $move['despacho'],
                ':id_exp' => $idExp
            ]);
        } catch (PDOException $e) {
            echo "Error al insertar el movimiento en la tabla user_exp_move: " . $e->getMessage();
        }
    }
}
