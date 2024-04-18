<?php

// importo el paso 2
require_once 'step2.php';

// require_once __DIR__ . '/../pjf-listas-despacho/PJF_Listas_Despacho.php';
// Crear instancia de la clase Listas_Despacho
// $pjf = new PJF_Listas_Despacho();

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
    define('DIAS_ATRAS', 2);
    $fecha_fin = date('Y-m-d');
    $fecha_inicio = date('Y-m-d', strtotime("-" . DIAS_ATRAS . " days"));

    // Construir la ruta al archivo JSON
    $json_file_path = __DIR__ . '/../tipos_listas_y_dependencias.json';
    // valido que tipos_listas_y_dependencias.json existe y es un json
    if (!file_exists($json_file_path)) {
      echo "El archivo tipos_listas_y_dependencias.json no existe\n";
      return;
    }
    // Leer el archivo tipos_listas_y_dependencias.json y convertirlo a array
    $tipos_listas_y_dependencias = json_decode(file_get_contents($json_file_path), true);
    // Separar los tipos de lista y dependencias en variables
    $tipos_listas = $tipos_listas_y_dependencias['tipos_listas'];
    $dependencias = $tipos_listas_y_dependencias['dependencias'];

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
    }
  }
  // Funcion obtiene los movimientos de cada expediente - 02
  private function procesarListaDespacho($id_tipo_lista, $fecha_inicio, $fecha_fin, $id_dependencia)
  {
    //no estoy seguro de donde sale getListasDespachoPorRangoFechaYTipo
    $listas_despacho = $this->pjf->getListasDespachoPorRangoFechaYTipo($fecha_inicio, $fecha_fin, $id_dependencia, $id_tipo_lista);
    echo "Listas de Despacho-> " . "\n";
    var_dump($listas_despacho);
    // *************** La lista de Despacho esta llegando vacia ***************
    try {
      $listaDespachoProcessor = new ListaDespachoProcessor($this->conexion);
      $listaDespachoProcessor->procesarListasDespacho($listas_despacho);
    } catch (\Throwable $th) {
      echo "Error al procesar listas de despacho\n";
    }
  }
}
