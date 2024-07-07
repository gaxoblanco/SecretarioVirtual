<?php

include_once __DIR__ . '/../db.php';
include_once __DIR__ . '/step2.php';

require_once __DIR__ . '/../../pjf-listas-despacho/PJF_Listas_Despacho.php';

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
    define('DIAS_ATRAS', 5);
    $fecha_fin = date('Y-m-d');
    $fecha_inicio = date('Y-m-d', strtotime("-" . DIAS_ATRAS . " days"));
    // Construir la ruta absoluta al archivo JSON
    $json_file_path = __DIR__ . '/../tipos_listas_y_dependencias.json';

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
    $this->sthValue($id_dependencia, $sth);
    echo "Processing URL: " . $id_dependencia . " - " . $sth . " - " . $id_tipo_lista . "\n";

    try {
      $listas_despacho = $this->pjf->getListasDespachoPorRangoFechaYTipo($fecha_inicio, $fecha_fin, $id_dependencia, $id_tipo_lista, $sth);
      $total_listas = count($listas_despacho);
      echo "Listas de despacho obtenidas: " . $total_listas . "\n";

      if (empty($listas_despacho)) {
        echo "No se obtuvieron listas de despacho.\n";
        return;
      }

      $listaDespachoProcessor = new ListaDespachoProcessor($this->conexion);
      $block_size = 10; // Ajusta este valor según tus necesidades
      $total_procesado = 0;

      for ($i = 0; $i < $total_listas; $i += $block_size) {
        $block = array_slice($listas_despacho, $i, $block_size);
        $block_count = count($block);
        $total_procesado += $block_count;

        echo "Procesando bloque " . (floor($i / $block_size) + 1) . " de " . ceil($total_listas / $block_size) .
          " (tamaño: $block_count). Total procesado: $total_procesado / $total_listas\n";

        $listaDespachoProcessor->procesarListasDespacho($block);

        // Liberamos memoria explícitamente
        unset($block);
        if (function_exists('gc_collect_cycles')) {
          gc_collect_cycles();
        }
      }

      echo "Procesamiento completado. Total de listas de despacho procesadas: " . $total_procesado . "\n";
    } catch (\Throwable $th) {
      echo "Error al procesar las listas de despacho: " . $th->getMessage() . "\n";
    } finally {
      // Liberamos la memoria del array principal
      unset($listas_despacho);
      if (function_exists('gc_collect_cycles')) {
        gc_collect_cycles();
      }
    }
  }


  // valido rutas especiales
  private function sthValue($dependenciaId, &$sth)
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
