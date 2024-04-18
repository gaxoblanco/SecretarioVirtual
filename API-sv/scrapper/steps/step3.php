<?php

//----- 3
class ExpedienteInsertor
{
  private $conexion;

  public function __construct($conexion)
  {
    $this->conexion = $conexion;
  }
  // Insertar expediente
  public function insertarExpediente($id_lista_despacho, $dependencia, $tipo_lista, $numero_expediente, $anio_expediente, $caratula, $reservado, $movimientos)
  {
    try {
      $sql = "INSERT INTO expedientes (id_lista_despacho, dependencia, tipo_lista, numero_expediente, anio_expediente, caratula, reservado) VALUES (:id_lista_despacho, :dependencia, :tipo_lista, :numero_expediente, :anio_expediente, :caratula, :reservado)";
      $stmt = $this->conexion->prepare($sql);
      $stmt->bindParam(':id_lista_despacho', $id_lista_despacho, PDO::PARAM_INT);
      $stmt->bindParam(':dependencia', $dependencia, PDO::PARAM_INT);
      $stmt->bindParam(':tipo_lista', $tipo_lista, PDO::PARAM_INT);
      $stmt->bindParam(':numero_expediente', $numero_expediente, PDO::PARAM_INT);
      $stmt->bindParam(':anio_expediente', $anio_expediente, PDO::PARAM_INT);
      $stmt->bindParam(':caratula', $caratula, PDO::PARAM_STR);
      $stmt->bindParam(':reservado', $reservado, PDO::PARAM_BOOL);
      $stmt->execute();
    } catch (\Throwable $th) {
      echo "Error al insertar expediente: {$numero_expediente}/{$anio_expediente}\n";
    }
    // echo "Expediente insertado: {$numero_expediente}/{$anio_expediente}\n";
  }
}
