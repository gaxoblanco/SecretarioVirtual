<?php

class ExpedienteInsertor
{
  private $conexion;

  public function __construct($conexion)
  {
    $this->conexion = $conexion;
  }
  // Insertar expediente 02-D
  public function insertarExpediente($id_lista_despacho, $dependencia, $tipo_lista, $numero_expediente, $anio_expediente, $caratula, $reservado, $movimientos)
  {
    try {
      // Limpio la caratula para que no contenga html ni caracteres especiales
      $caratula = strip_tags($caratula);
      $caratula = htmlspecialchars($caratula, ENT_QUOTES);
      // Limpiar saltos de línea y otros caracteres especiales
      $search = array("\r\n", "\n", "\r", "\t");
      $replace = ' '; // Reemplazar por un espacio en blanco
      $caratula = str_replace($search, $replace, $caratula);
      // Insertar el expediente en la base de datos

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
      // echo "Expediente insertado: {$numero_expediente}/{$anio_expediente}\n";
    } catch (\Throwable $th) {
      echo "Error al insertar expediente: " . $th->getMessage() . "\n";
    }
  }
}
