<?php
// 	id_move 	fecha_movimiento 	estado 	texto 	titulo 	despacho 	id_exp
function newMove($expMoving, $idExp, $conexion)
{
  //itero por el array $expMoving y en cada iteracion agrego el movimiento en la tabla user_exp_move
  foreach ($expMoving as $move) {
    // echo "move -> <pre>" . var_dump($move) . "</pre>";

    //valido que el movimiento no exista en la tabla user_exp_move
    try {
      $query = $conexion->prepare('SELECT * FROM user_exp_move WHERE fecha_movimiento = :fecha_movimiento AND estado = :estado AND texto = :texto AND titulo = :titulo AND despacho = :despacho AND id_exp = :id_exp');
      $query->execute([
        ':fecha_movimiento' => $move['fecha_movimiento'],
        ':estado' => $move['estado'],
        ':texto' => $move['texto'],
        ':titulo' => $move['titulo'],
        ':despacho' => $move['despacho'],
        ':id_exp' => $idExp
      ]);
      $moveExist = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      echo "Error al buscar el movimiento en la tabla user_exp_move: " . $e->getMessage();
    }
    // imprimo el valor de $moveExist
    echo "moveExist -> " . $moveExist . "</pre>";
    //si el movimiento no existe en la tabla user_exp_move, lo agrego
    if ($moveExist !== false && $moveExist !== null && empty($moveExist)) {
      echo "El movimiento ya existe en la tabla user_exp_move" . $moveExist;
      continue;
    }

    //valido que no sea un array vacio
    if (empty($move)) {
      echo "El movimiento esta vacio";
      continue;
    }


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
