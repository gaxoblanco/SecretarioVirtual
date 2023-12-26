<?php
//consulto si el exp ya existe en la base de datos expedientes
// si existe actualizo con dichos datos el exp en la tabal user_expedients
function existsExp($numero_exp, $anio_exp, $id_user, $id_exp, $dependencia, $conexion)
{
  try {
    // consulto en la tabla expedientes, por un expediente que tenga numero_expediente == numero_exp y anio_expediente == anio_exp
    $query = $conexion->prepare('SELECT * FROM expedientes WHERE numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente AND dependencia = :dependencia');
    $query->execute([':numero_expediente' => $numero_exp, ':anio_expediente' => $anio_exp, ':dependencia' => $dependencia]);
    $expedient = $query->fetchAll(PDO::FETCH_ASSOC);

    // si no existe, devuelve mensaje "expediente aun no se cargo"
    if (!$expedient) {
      echo json_encode('expediente aun no se cargo <br>');
      return;
    }

    // si existe ( tipo_lista != null)
    // llamar a la funcion expedientUp que recibe el numero_exp, anio_exp, id_user y $expedient
    require_once 'expedient_up.php';
    expedientUp($id_exp, $id_user, $expedient, $conexion); // $expedient = todos los datos del expediente

    // si existe, devuelve los datos obtenidos
    // echo json_encode($expedient);
    return $expedient; // retorono los datos del expediente obtenidos en la tabla expedients
  } catch (PDOException $e) {
    // Devolver mensaje de error en json
    echo json_encode([
      'status' => 500,
      'message' => 'Error al consultar el expediente en la tabla expedientes para actualizarlo en la tabla user_expedients',
    ]);
  }
}
