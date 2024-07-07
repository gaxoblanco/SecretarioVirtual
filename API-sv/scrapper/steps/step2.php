<?php

include_once __DIR__ . '/../../services/clean_and_encode.php';
include_once __DIR__ . '/step3.php';


class ListaDespachoProcessor
{
  private $conexion;

  public function __construct($conexion)
  {
    $this->conexion = $conexion;
  }
  // Procesar lista de despacho - 03/02
  public function procesarListasDespacho($listas_despacho)
  {
    try {
      // Tamaño del bloque
      $block_size = 5;
      $total = count($listas_despacho);

      for ($i = 0; $i < $total; $i += $block_size) {
        // Obtener un bloque de listas de despacho
        $block = array_slice($listas_despacho, $i, $block_size);

        foreach ($block as $lista_despacho) {
          // Verificar existencia y formato de los índices
          if (!isset($lista_despacho['id']) || !isset($lista_despacho['dependencia']) || !isset($lista_despacho['tipo_lista']) || !isset($lista_despacho['expedientes'])) {
            echo "Error: Lista de despacho con formato incorrecto en índice $i.\n";
            print_r($lista_despacho);
            continue; // Saltar este elemento
          }

          // Obtener el número de expediente
          $id_lista_despacho = $lista_despacho['id'];
          $dependencia = $lista_despacho['dependencia'];
          $tipo_lista = $lista_despacho['tipo_lista'];
          echo "Procesando Lista de Despacho: {$id_lista_despacho} Dependencia: {$dependencia} Tipo de Lista: {$tipo_lista}\n";

          // Llamo a la funcion procesarExpedienteToSave y le paso los parametros
          $this->procesarExpedienteToSave($lista_despacho, $id_lista_despacho, $dependencia, $tipo_lista);

          // Llamo a la public function procesar movimientos
          $this->procesarMovimientos($dependencia, $lista_despacho);
        }
        echo "Procesamiento de todas las listas de despacho completado.\n";
      }
    } catch (\Throwable $th) {
      echo "Error al procesar listas de despacho: " . $th->getMessage() . "\n";
    }
  }
  // Procesar un expediente 02-C
  private function procesarExpediente($expedienteInsertor, $id_lista_despacho, $dependencia, $tipo_lista, $numero_expediente, $anio_expediente, $caratula, $reservado, $movimientos)
  {
    echo "Procesando expediente: {$numero_expediente}/{$anio_expediente}\n";
    try {
      // Verificar si el expediente ya existe en la base de datos
      $sql_verificar = "SELECT COUNT(*) FROM expedientes WHERE numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente AND dependencia = :dependencia";
      $stmt_verificar = $this->conexion->prepare($sql_verificar);
      $stmt_verificar->bindParam(':numero_expediente', $numero_expediente, PDO::PARAM_INT);
      $stmt_verificar->bindParam(':anio_expediente', $anio_expediente, PDO::PARAM_INT);
      $stmt_verificar->bindParam(':dependencia', $dependencia, PDO::PARAM_STR);
      $stmt_verificar->execute();

      if ($stmt_verificar->fetchColumn() > 0) {
        // El expediente ya existe, no hacemos nada
        echo "El expediente {$numero_expediente}/{$anio_expediente} ya está cargado.\n";
        return;
      }
    } catch (\Throwable $th) {
      echo "Error al verificar si el expediente ya existe: " . $th->getMessage() . "\n";
    }

    // Si no existe, procedemos a insertarlo
    $expedienteInsertor->insertarExpediente($id_lista_despacho, $dependencia, $tipo_lista, $numero_expediente, $anio_expediente, $caratula, $reservado, $movimientos);
    echo "Expediente insertado: {$numero_expediente}/{$anio_expediente}\n";
  }
  //02-B
  private function procesarExpedienteToSave($lista_despacho, $id_lista_despacho, $dependencia, $tipo_lista)
  {
    try {
      $tamano_bloque = 50; // Definimos un tamaño de bloque, ajustable según necesidades
      $total_expedientes = count($lista_despacho['expedientes']);

      for ($i = 0; $i < $total_expedientes; $i += $tamano_bloque) {
        $bloque_actual = array_slice($lista_despacho['expedientes'], $i, $tamano_bloque);

        $this->procesarBloqueExpedientes($bloque_actual, $id_lista_despacho, $dependencia, $tipo_lista);

        // Liberar memoria
        unset($bloque_actual);
        gc_collect_cycles();
      }
    } catch (\Throwable $th) {
      echo "Error al procesar expedientes para guardarlos " . $th->getMessage() . "\n";
    }
  }

  private function procesarBloqueExpedientes($bloque, $id_lista_despacho, $dependencia, $tipo_lista)
  {
    $expedienteInsertor = new ExpedienteInsertor($this->conexion);

    foreach ($bloque as $expediente) {
      if (!$this->validarExpediente($expediente)) {
        echo "Error: Expediente con formato incorrecto.\n";
        continue;
      }

      $expediente = $this->prepararExpediente($expediente);
      $this->procesarExpediente(
        $expedienteInsertor,
        $id_lista_despacho,
        $dependencia,
        $tipo_lista,
        $expediente['numero'],
        $expediente['anio'],
        $expediente['caratula'],
        $expediente['reservado'],
        $expediente['movimientos']
      );
    }

    // Liberar el insertor después de procesar el bloque
    unset($expedienteInsertor);
  }

  private function validarExpediente($expediente)
  {
    $campos_requeridos = ['numero', 'anio', 'caratula', 'reservado', 'movimientos'];
    foreach ($campos_requeridos as $campo) {
      if (!isset($expediente[$campo])) {
        return false;
      }
    }
    return true;
  }

  private function prepararExpediente($expediente)
  {
    $expediente['caratula'] = $this->limpiarYCodificar($expediente['caratula'], 255);
    $expediente['reservado'] = $this->limpiarYCodificar($expediente['reservado']);
    return $expediente;
  }

  private function limpiarYCodificar($texto, $longitud_maxima = null)
  {
    $texto_limpio = cleanAndEncode($texto);
    if ($longitud_maxima !== null) {
      $texto_limpio = substr($texto_limpio, 0, $longitud_maxima);
    }
    return $texto_limpio;
  }
  //------ moving ----------------------------------------------------

  // Procesar movimientos
  // Funcion procesarMovimientos recive el listado y lo procesa
  public function procesarMovimientos($dependencia, $lista_despacho)
  {
    try {
      // itero por despacho y obtengo los expedientes
      foreach ($lista_despacho['expedientes'] as $expediente) {
        if (!isset($expediente['numero']) || !isset($expediente['anio']) || !isset($expediente['movimientos'])) {
          echo "Error: Expediente con formato incorrecto para movimientos.\n";
          continue; // Saltar este expediente
        }

        $numero_expediente = $expediente['numero'];
        $anio_expediente = $expediente['anio'];
        $movimientos = $expediente['movimientos'];

        // Obtener el id_expediente
        $id_expediente = $this->obtenerIdExpediente($numero_expediente, $anio_expediente);
        // echo "ID Expediente obtenido: {$id_expediente}\n";

        // Validar si el array de movimientos no está vacío
        if (!empty($movimientos[0])) {
          // echo "Valide que movimientos sea mayor a un array []: " . (!empty($movimientos[0]) ? "true" : "false") . "\n";
          // Funcion iterar por cada movimiento y comparar si existe un movimiento con el mismo id_expediente y fecha_movimiento
          foreach ($movimientos as $movimiento) {
            $fecha_movimiento = $movimiento['fecha'];
            $estado = $movimiento['estado'];
            $texto = $movimiento['texto'];
            $titulo = $movimiento['titulo'];
            $despacho = $movimiento['despacho'];

            echo "Tipo de texto original: " . gettype($texto) . "\n";
            // echo "Texto original: " . print_r($texto, true) . "\n";
            // Asegurarse de que $texto sea una cadena
            if (!is_string($texto)) {
              $texto = $this->convertirATexto($texto);
              echo "Texto convertido a string: " . $texto . "\n";
            }

            $texto = cleanAndEncode($texto);
            echo "Texto limpio y codificado: " . $texto . "\n";
            $titulo = cleanAndEncode($titulo);
            $despacho = cleanAndEncode($despacho);
            $estado = cleanAndEncode($estado);

            $this->procesarMovimiento($id_expediente, $estado, $texto, $titulo, $despacho, $fecha_movimiento);
          }
        }
      }
    } catch (\Throwable $th) {
      echo "Error al procesar movimientos: " . $th->getMessage() . "\n";
    }
  }
  private function convertirATexto($valor)
  {
    if (is_array($valor)) {
      return json_encode($valor, JSON_UNESCAPED_UNICODE);
    } elseif (is_object($valor)) {
      return serialize($valor);
    } else {
      return (string)$valor;
    }
  }

  // Funcion obtenerIdExpediente obtiene el id_expediente de la tabla expedientes buscando por numero_expediente y anio_expediente
  public function obtenerIdExpediente($numero_expediente, $anio_expediente)
  {
    $sql = "SELECT id_expediente FROM expedientes WHERE numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindParam(':numero_expediente', $numero_expediente, PDO::PARAM_INT);
    $stmt->bindParam(':anio_expediente', $anio_expediente, PDO::PARAM_INT);
    $stmt->execute();
    $id_expediente = $stmt->fetch(PDO::FETCH_ASSOC);
    return $id_expediente['id_expediente'];
  }

  // Funcion procesarMovimiento consulta en la DB si exsite un expediente con el mismo id_expediente y fecha_movimiento
  // si no existe lo inserta
  public function procesarMovimiento($expediente, $estado, $texto, $titulo, $despacho, $fecha_movimiento)
  {
    try {
      // Intentar convertir la fecha al formato YYYY-MM-DD
      $fecha_movimiento_formatted = date('Y-m-d', strtotime(str_replace('/', '-', $fecha_movimiento)));
      $sql = "SELECT id_movimiento FROM movimientos WHERE id_expediente = :id_expediente AND fecha_movimiento = :fecha_movimiento";
      $stmt = $this->conexion->prepare($sql);
      $stmt->bindParam(':id_expediente', $expediente, PDO::PARAM_INT);
      $stmt->bindParam(':fecha_movimiento', $fecha_movimiento_formatted, PDO::PARAM_STR);
      $stmt->execute();
      $id_movimiento = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (\Throwable $th) {
      echo "Error al procesar movimiento: " . $th->getMessage() . "\n";
    }
    // Si no existe el movimiento, lo insertamos
    if (!$id_movimiento) {
      echo "Movimiento no existe, insertando...\n";
      $this->insertarMovimiento($expediente, $estado, $texto, $titulo, $despacho, $fecha_movimiento_formatted);
    } else {
      // echo "Movimiento ya existe para el expediente {$expediente} y fecha {$fecha_movimiento_formatted}\n";
    }
  }
  // Funcion para insertar el nuevo movimiento
  public function insertarMovimiento($expediente, $estado, $texto, $titulo, $despacho, $fecha_movimiento_formatted)
  {
    // Limpio los datos antes de guardarlos
    $texto = strip_tags($texto);
    $texto = htmlspecialchars($texto, ENT_QUOTES);
    $titulo = strip_tags($titulo);
    $titulo = htmlspecialchars($titulo, ENT_QUOTES);
    $despacho = strip_tags($despacho);
    $despacho = htmlspecialchars($despacho, ENT_QUOTES);

    try {
      $sql = "INSERT INTO movimientos (id_expediente, estado, texto, titulo, despacho, fecha_movimiento) VALUES (:id_expediente, :estado, :texto, :titulo, :despacho, :fecha_movimiento)";
      $stmt = $this->conexion->prepare($sql);
      $stmt->bindParam(':id_expediente', $expediente, PDO::PARAM_INT);
      $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
      $stmt->bindParam(':texto', $texto, PDO::PARAM_STR);
      $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
      $stmt->bindParam(':despacho', $despacho, PDO::PARAM_STR);
      $stmt->bindParam(':fecha_movimiento', $fecha_movimiento_formatted, PDO::PARAM_STR);
      $stmt->execute();
    } catch (\Throwable $th) {
      echo "Error al insertar movimiento: " . $th->getMessage() . "\n";
    }
  }
}
