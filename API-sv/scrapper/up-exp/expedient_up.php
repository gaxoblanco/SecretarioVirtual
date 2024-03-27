<?php
// Update expedient in user_expedients table by id_exp and id_user
// Clean the data coming

function expedientUp($id_exp, $id_user, $expedient, $conexion)
{
  try {

    // formateo la caratula para limpair saltos de linea y codigo html
    $expedient[0]['caratula'] = htmlspecialchars(strip_tags($expedient[0]['caratula']));
    $search = array("\r\n", "\n", "\r", "\t");
    $replace = ' '; // Reemplazar por un espacio en blanco
    $expedient[0]['caratula'] = str_replace($search, $replace, $expedient[0]['caratula']);
    $expedient[0]['caratula'] = html_entity_decode($expedient[0]['caratula']);
    // paso el texto a utf-8
    $expedient[0]['caratula'] = utf8_encode($expedient[0]['caratula']);

    //formateo reservado para que no contenga codigo html
    $expedient[0]['reservado'] = htmlspecialchars(strip_tags($expedient[0]['reservado']));
    $expedient[0]['reservado'] = utf8_encode($expedient[0]['reservado']);


    // en la tabla user_expedients, actualiza el expediente que coincida con id_exp y id_user con los datos que corresponden a la tabla expedientes
    $query = $conexion->prepare('UPDATE user_expedients SET numero_exp = :numero_exp, anio_exp = :anio_exp, dependencia = :dependencia, id_lista_despacho = :id_lista_despacho, caratula = :caratula, tipo_lista = :tipo_lista,  reservado = :reservado WHERE id_exp = :id_exp and id_user = :id_user');

    $query->execute([
      ':numero_exp' => $expedient[0]['numero_expediente'],
      ':anio_exp' => $expedient[0]['anio_expediente'],
      ':dependencia' => $expedient[0]['dependencia'],
      ':id_lista_despacho' => $expedient[0]['id_lista_despacho'],
      ':caratula' => $expedient[0]['caratula'],
      ':reservado' => $expedient[0]['reservado'],
      ':tipo_lista' => $expedient[0]['tipo_lista'],
      ':id_user' => $id_user,
      ':id_exp' => $id_exp
    ]);

    echo json_encode('expediente actualizado');
    return;
  } catch (PDOException $e) {
    // Devolver mensaje de error en json
    echo json_encode([
      'status' => 500,
      'message' => 'Error al actualizar el expediente en la tabla user_expedients, id_exp: ' . $id_exp,
    ]);
  }
}
