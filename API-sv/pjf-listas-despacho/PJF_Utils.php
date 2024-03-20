
<?php
class PJF_Utils
{
  /**
   * Enviar peticion POST utilizando curl
   * @param string $url
   * @param array $post
   * @return string
   */
  protected function curl_post($url, $post)
  {
    // Establecer un límite de tiempo más alto (en este caso, 300 segundos)
    set_time_limit(300);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    print("Ejecutando consulta POST a $url => " . json_encode($post));
    $time = microtime(true);
    $server_output = curl_exec($ch);
    $time = microtime(true) - $time;
    print("OK en {$time} s");
    curl_close($ch);
    return $server_output;
  }

  /**
   * Enviar peticion GET utilizando curl
   * @param string $url
   * @return string
   */
  protected function curl_get($url)
  {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    print("Ejecutando consulta POST a $url");
    $time = microtime(true);
    $server_output = curl_exec($ch);
    $time = microtime(true) - $time;
    print("OK en $time s");
    curl_close($ch);
    return $server_output;
  }

  /**
   * Armar array con los pares clave => valor
   * mientras la clave sea distinta de nulo
   * @param DOMNodeList $array
   * @return array
   */
  protected function armarArray($array)
  {
    $array_nuevo = array();
    foreach ($array as $option) {
      $value = $option->getAttribute('value');
      if ($value) {
        $array_nuevo[$value] = $option->nodeValue;
      }
    }
    return $array_nuevo;
  }

  /**
   * Obtener las tablas de expedientes y listas de despacho
   * desde consulta de API
   * @param string $html HTML de la consulta
   * @return array Cantidad de expedientes, tablas de expedientes y listas de despacho
   */
  protected function getTablas($html)
  {
    // Buscar el texto "Cantidad de Expedientes: "
    $explodeCantidad = explode('Cantidad de Expedientes: ', $html);
    // Hasta la etiqueta </p>
    $explodeCantidad = explode('</p>', $explodeCantidad[1]);
    // Obtener la cantidad de expedientes
    $cantidad = intval($explodeCantidad[0]);
    // Si la cantidad es 0, no hay lista de despacho
    if ($cantidad == 0) {
      return array();
    }

    $tablas = $this->extraerTablas($html);

    // La primer tabla es:
    // Expedientes
    // De aca vamos a extraer:
    // Tipo Medio
    // Caratula
    // Dependencia
    // Nro/Anio
    // Codigo Unico
    $expedientes = $this->extraerDatosTabla(
      $tablas[0],
      array(
        "tipo_medio",
        "caratula",
        "",
        "dependencia",
        "nro_anio",
        "codigo_unico",
        "veces_lista_despacho"
      )
    );

    // La segunda tabla son las listas de despacho:
    // De aca vamos a extraer:
    // Orden #
    // Fecha y Hora con Tipo de Lista
    // Impresion en Lista
    $listas_despacho = $this->extraerDatosTabla(
      $tablas[1],
      array(
        "id",
        "fecha_hora_tipo_lista",
        "impresion_en_lista"
      )
    );

    return array(
      'expedientes' => $expedientes,
      'listas_despacho' => $listas_despacho
    );
  }

  /**
   * Extraer tablas del HTML
   * @param string $html HTML
   * @return array
   */
  protected function extraerTablas($html)
  {
    // Buscar la etiqueta <table
    $explodeTables = explode('<table', $html);
    // Descartar el primer elemento
    array_shift($explodeTables);

    $tables = array();

    // Iterar cada tabla
    foreach ($explodeTables as $table) {
      // Extraer la tabla
      $table = explode('</table>', $table);
      $table = $table[0];

      // Sacar el tbody
      $table = explode('<tbody>', $table);
      $table = explode('</tbody>', $table[1]);
      $table = $table[0];

      // Agregar al array de tablas
      array_push($tables, $table);
    }
    return $tables;
  }

  /**
   * Extraer de una tabla de HTML los datos
   * @param string $html HTML de la tabla
   * @param array $campos Campos a extraer
   * @return array
   */
  protected function extraerDatosTabla($html, $campos)
  {
    // Extraer cada elemento de tipo td
    $items = explode('<tr', $html);
    // Descartar el primer elemento
    array_shift($items);

    $datos = array();

    foreach ($items as $index => $item) {
      // Extraer el item
      $item = explode('</tr>', $item);
      $item = $item[0];

      // Extraer cada td
      $item = explode('<td', $item);
      // Descartar el primer elemento
      array_shift($item);

      $dato = array();

      // Extraer cada dato del td
      foreach ($item as $index => $data) {
        // Extraer los datos del item
        $data = explode('>', $data);
        // Descartar solo el primer elemento, y concatenar los demas
        array_shift($data);
        $data = implode('>', $data);
        // Extraer tag de cierre td
        $data = explode('</td', $data);
        $data = $data[0];

        // Si la data esta vacia, ignorar y continuar
        if (empty($data)) {
          continue;
        }

        // Obtener el nombre del atributo a partir del $index
        // Antes, verificar que el indice no sea mayor al numero de campos
        if ($index > count($campos)) {
          break; // Si no, salir del foreach
        }
        $campo = $campos[$index];

        // Agregar al array de datos
        $dato[$campo] = $data;
      }

      // Agregar dato (solo si tiene datos efectivamente)
      if (!empty($dato)) {
        array_push($datos, $dato);
      }
    }

    return $datos;
  }

  /**
   * Funcion para extraer el ID de la Lista de Despacho
   * @param string $accordion HTML del accordion
   * @return string
   */
  protected function getIdListaDespacho($accordion)
  {
    // Buscamos la palabra cargarTabla(
    $explodeCargarTabla = explode('cargarTabla(', $accordion);
    // Buscamos la coma que separa el ID de la lista de despacho
    $explodeComa = explode(',', $explodeCargarTabla[1]);
    // Extraemos el ID de la lista de despacho
    $id = $explodeComa[0];
    return $id;
  }

  /**
   * Funcion para extraer la fecha de la Lista de Despacho
   * @param string $accordion HTML del accordion
   * @return string
   */
  protected function getFechaLista($accordion)
  {
    // Para eso buscamos el tag <h5
    $explodeH5 = explode('<h5', $accordion);
    $h5 = $explodeH5[1];
    // Extraer el tag p del h5 para sacar el titulo
    $explodeP = explode('<p>', $h5);
    $p = $explodeP[1];
    // Buscar el tag <b> para extraer la fecha
    $explodeB = explode('<b>', $p);
    $b = $explodeB[2];
    // Quitar el cierre de tag </b> de $b para obtener la fecha
    $explodeB = explode(')</b>', $b);
    $fecha = $explodeB[0];
    return $fecha;
  }
}
?>
