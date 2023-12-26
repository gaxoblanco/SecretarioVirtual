<?php

function getExpedientsMoves($id_expediente, $conexion)
{
  try {
    // Obtener los movimientos que tengan el id_expediente en la tabla movimientos
    $query = $conexion->prepare('SELECT * FROM movimientos WHERE id_expediente = :id_expediente');
    $query->execute([':id_expediente' => $id_expediente]);
    $movimientos = $query->fetchAll(PDO::FETCH_ASSOC);
    // si no existe, devuelve mensaje " expediente sin movimientos"
    if (!$movimientos) {
      // echo json_encode('expediente sin movimientos');
      return null;
    }

    // si existe, devuelve los datos obtenidos
    // echo "tenemos al menos 1 movimiento" . json_encode($movimientos);
    return $movimientos;
  } catch (PDOException $e) {
    echo "Error al obtener los movimientos del expediente: " . $e->getMessage();
  }
}
