<?php
// Importar la clase PJF Lista de Despachos
// Solo procesa los datos obtenidos y los alamcena

// Importar el cliente SQL
require_once 'db.php';
// require_once './moving.php';

// require_once '../pjf-listas-despacho/PJF_Listas_Despacho.php';
// // Crear instancia de la clase Listas_Despacho
// $pjf = new PJF_Listas_Despacho();
<<<<<<< HEAD
=======

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
//------- 2
class ListaDespachoProcessor
{
  private $conexion;

  public function __construct($conexion)
  {
    $this->conexion = $conexion;
  }
  // Procesar lista de despacho - 03
  public function procesarListasDespacho($listas_despacho)
  {
    try {
      foreach ($listas_despacho as $lista_despacho) {
        // Obtener el número de expediente
        $id_lista_despacho = $lista_despacho['id'];
        $dependencia = $lista_despacho['dependencia'];
        $tipo_lista = $lista_despacho['tipo_lista'];
        // echo "Lista de Despacho: {$id_lista_despacho} Dependencia: {$dependencia} Tipo de Lista: {$tipo_lista}\n";

        // Llamo a la funcion procesarExpedienteToSave y le paso los parametros
        $this->procesarExpedienteToSave($lista_despacho, $id_lista_despacho, $dependencia, $tipo_lista);

        // Llamo a la public function procesar movimientos
        $this->procesarMovimientos($dependencia, $lista_despacho);
      }
    } catch (\Throwable $th) {
      echo "Error al procesar listas de despacho: " . $th->getMessage() . "\n";
    }
  }
  // Procesar un expediente
  private function procesarExpediente($expedienteInsertor, $id_lista_despacho, $dependencia, $tipo_lista, $numero_expediente, $anio_expediente, $caratula, $reservado, $movimientos)
  {
    try {
      // Verificar si el expediente ya existe en la base de datos
      $sql_verificar = "SELECT COUNT(*) FROM expedientes WHERE numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente";
      $stmt_verificar = $this->conexion->prepare($sql_verificar);
      $stmt_verificar->bindParam(':numero_expediente', $numero_expediente, PDO::PARAM_INT);
      $stmt_verificar->bindParam(':anio_expediente', $anio_expediente, PDO::PARAM_INT);
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
  private function procesarExpedienteToSave($lista_despacho, $id_lista_despacho, $dependencia, $tipo_lista)
  {
    try {
      // itero por despacho y obtengo los expedientes para guardarlos
      foreach ($lista_despacho['expedientes'] as $expediente) {
        $numero_expediente = $expediente['numero'];
        $anio_expediente = $expediente['anio'];
        $caratula = $expediente['caratula'];
        $reservado = $expediente['reservado'];
        $movimientos = $expediente['movimientos'];
        //----- Proceso cada Expediente -----
        $expedienteInsertor = new ExpedienteInsertor($this->conexion);
        $this->procesarExpediente($expedienteInsertor, $id_lista_despacho, $dependencia, $tipo_lista, $numero_expediente, $anio_expediente, $caratula, $reservado, $movimientos);
      }
    } catch (\Throwable $th) {
      echo "Error al procesar expedientes para guardarlos " . $th->getMessage() . "\n";
    }
  }
  //------ moving ----------------------------------------------------

  // Procesar movimientos
  // Funcion procesarMovimientos recive el listado y lo procesa
  public function procesarMovimientos($dependencia, $lista_despacho)
  {
    try {
      // itero por despacho y obtengo los expedientes
      foreach ($lista_despacho['expedientes'] as $expediente) {
        $numero_expediente = $expediente['numero'];
        $anio_expediente = $expediente['anio'];
        $movimientos = $expediente['movimientos'];

        // Obtener el id_expediente
        $id_expediente = $this->obtenerIdExpediente($numero_expediente, $anio_expediente);
        echo "ID Expediente obtenido: {$id_expediente}\n";

        // Validar si el array de movimientos no está vacío
        if (!empty($movimientos[0])) {
          echo "Valide que movimientos sea mayor a un array []: " . (!empty($movimientos[0]) ? "true" : "false") . "\n";
          // Funcion iterar por cada movimiento y comparar si existe un movimiento con el mismo id_expediente y fecha_movimiento
          foreach ($movimientos as $movimiento) {
            $fecha_movimiento = $movimiento['fecha'];
            $estado = $movimiento['estado'];
            $texto = $movimiento['texto'];
            $titulo = $movimiento['titulo'];
            $despacho = $movimiento['despacho'];

            $this->procesarMovimiento($id_expediente, $estado, $texto, $titulo, $despacho, $fecha_movimiento);
          }
        }
      }
    } catch (\Throwable $th) {
      echo "Error al procesar movimientos: " . $th->getMessage() . "\n";
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
      echo "Movimiento ya existe para el expediente {$expediente} y fecha {$fecha_movimiento_formatted}\n";
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
//------ 1
class TipoListaProcessor
{
  private $pjf;
  private $conexion;

  public function __construct($pjf, $conexion)
  {
    $this->pjf = $pjf;
    $this->conexion = $conexion;
  }

  // Iterar por cada dependencia - 00
  public function startScript()
  {
    // Definir la fecha desde hasta como la fecha de hoy menos DIAS_ATRAS dias
    define('DIAS_ATRAS', 20);
    $fecha_fin = date('Y-m-d');
    $fecha_inicio = date('Y-m-d', strtotime("-" . DIAS_ATRAS . " days"));

    // Definir el directorio base del proyecto
    define('BASE_DIR', __DIR__);
    // Construir la ruta absoluta al archivo JSON
    $json_file_path = BASE_DIR . '/tipos_listas_y_dependencias.json';

    if (!file_exists($json_file_path)) {
      echo "El archivo tipos_listas_y_dependencias.json no existe\n";
      exit;
    }

    // Leer el archivo tipos_listas_y_dependencias.json y convertirlo a array
    $json_content = file_get_contents($json_file_path);
    $tipos_listas_y_dependencias = json_decode($json_content, true);

    // Verificar si la decodificación fue exitosa y si los datos no están vacíos
    if (json_last_error() === JSON_ERROR_NONE && !empty($tipos_listas_y_dependencias)) {
      // Separar los tipos de lista y dependencias en variables
      $tipos_listas = $tipos_listas_y_dependencias['tipos_listas'];
      $dependencias = $tipos_listas_y_dependencias['dependencias'];
    } else {
      echo "El archivo tipos_listas_y_dependencias.json no tiene datos válidos o está vacío\n";
      exit;
    }

    // Crear instancia de TipoListaProcessor
    $tipoListaProcessor = new TipoListaProcessor($this->pjf, $this->conexion);


    // Iterar por cada dependencia
    foreach ($dependencias as $id_dependencia => $nombre_dependencia) {
      // echo "Dependencia: {$nombre_dependencia} ({$id_dependencia})\n";
      $tipoListaProcessor->procesarTiposListas($tipos_listas, $fecha_inicio, $fecha_fin, $id_dependencia);
    }
  }

  // funcion para procesar los tipos de listas - 01
  public function procesarTiposListas($tipos_listas, $fecha_inicio, $fecha_fin, $id_dependencia)
  {
    foreach ($tipos_listas as $id_tipo_lista => $nombre_tipo_lista) {
      // echo "Tipo de Lista: {$nombre_tipo_lista} ({$id_tipo_lista})\n";
      $this->procesarListaDespacho($id_tipo_lista, $fecha_inicio, $fecha_fin, $id_dependencia);
      // echo "Procesando lista de despacho: {$id_tipo_lista} - {$fecha_inicio} - {$fecha_fin} - {$id_dependencia}\n";
    }
  }
  // Funcion obtiene los movimientos de cada expediente - 02
  private function procesarListaDespacho($id_tipo_lista, $fecha_inicio, $fecha_fin, $id_dependencia)
  {
    $sth = "STH1";
    // valido el valor sth con la funcion sthValue
    $this->sthValue($id_dependencia);
    // Funcion para obtener los datos a trabajar
    try {
      $listas_despacho = $this->pjf->getListasDespachoPorRangoFechaYTipo($fecha_inicio, $fecha_fin, $id_dependencia, $id_tipo_lista, $sth);
      // echo "Listas de Despacho: " . count($listas_despacho) . "\n";
      $listaDespachoProcessor = new ListaDespachoProcessor($this->conexion);
      $listaDespachoProcessor->procesarListasDespacho($listas_despacho);
    } catch (\Throwable $th) {
      echo "Error al obtener las listas de despacho: " . $th->getMessage() . "\n";
    }
  }

  // valido rutas especiales
  private function sthValue($dependenciaId)
  {
    switch ($dependenciaId) {
      case "275555824":
      case "273525524":
      case "274543239":
      case "180402548":
      case "270413499":
      case "272435360":
      case "183450390":
      case "276571069":
      case "182422213":
      case "278142604":
      case "196181029":
      case "197192380":
      case "198285350":
      case "565110066":
      case "271424187":
      case "268365655":
      case "181411781":
      case "269392271":
      case "277113567":
        $sth = "SP2";
        break;

      default:
        break;
    }
  }
}
>>>>>>> mercadoPago
