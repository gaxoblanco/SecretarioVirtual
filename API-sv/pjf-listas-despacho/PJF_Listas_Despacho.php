<?php
require_once 'PJF_Utils.php';
/**
 * Clase para obtener Listas de Despacho
 * del Poder Judicial de Formosa
 * @author ivan.diaz
 */
class PJF_Listas_Despacho extends PJF_Utils
{
    /**
     * Obtener Lista de Despacho
     * @param string $id ID de la Lista de Despacho
     * @param string $dependencia Dependencia de la Lista de Despacho
     */
    public function getListaDespachoPorId($id, $dependencia)
    {
        $url = "https://portalservicios.jusformosa.gob.ar/listadespachoV3/consultas/C_consulta/ConsultaExpedienteMovimiento";
        $data = array(
            'id_listaDespacho' => $id,
            'cud' => $dependencia
        );
        print("Obteniendo lista de despacho: {$id} - {$dependencia}");
        $request = $this->curl_post($url, $data);
        $response = json_decode($request, true);
        print("Lista de despacho obtenida: {$id} - {$dependencia}");
        return $response;
    }

    /**
     * Obtener Lista de Despacho por Dependencia, Numero y Año
     * @param string $dependencia Dependencia de la Lista de Despacho
     * @param string $numero Numero de Expediente
     * @param string $anio Año de Expediente
     */
    public function getListaDespachoPorExpediente($dependencia, $numero, $anio)
    {
        $url = "https://portalservicios.jusformosa.gob.ar/listadespachoV3/consultas/C_consulta/ConsultaListaDespachoDepNumAnioCaratuala/{$dependencia}/NULL/{$numero}/{$anio}/NULL/";
        print("Obteniendo lista de despacho por expediente: {$dependencia} - {$numero} - {$anio}");
        $html = $this->curl_post($url, array());
        print("Lista de despacho obtenida por expediente: {$dependencia} - {$numero} - {$anio}");
        return $this->getTablas($html);
    }

    /**
     * Obtener Lista de Despacho por Dependencia y Caratula
     * @param string $dependencia Dependencia de la Lista de Despacho
     * @param string $caratula Caratula de la Lista de Despacho
     * @return array
     */
    public function getListaDespachoPorCaratula($dependencia, $caratula)
    {
        $url = "https://portalservicios.jusformosa.gob.ar/listadespachoV3/consultas/C_consulta/ConsultaListaDespachoDepNumAnioCaratuala/{$dependencia}/NULL/NULL/NULL/{$caratula}/";
        print("Obteniendo lista de despacho por dependencia y caratula: {$dependencia} - {$caratula}");
        $html = $this->curl_post($url, array());
        print("Lista de despacho obtenida por dependencia y caratula: {$dependencia} - {$caratula}");
        return $this->getTablas($html);
    }

    /**
     * Obtener Tipos de Lista
     * y Dependencias
     * @return array
     */
    public function getTiposListasYDependencias()
    {
        // URL para consultar Tipos de Lista
        $url = "https://portalservicios.jusformosa.gob.ar/listadespachoV3/consultas/C_consulta/MostrarBusquedaPorCriterios";
        // Obtener el HTML de la URL utlizando curl
        $html = $this->curl_get($url);
        // Crear un DOMDocument
        $dom = new DOMDocument();
        // Cargar el HTML
        @$dom->loadHTML($html);
        // Obtener el formulario formulario_BuscarPorFechaDependenciaTipoLista
        $form = $dom->getElementById('formulario_BuscarPorFechaDependenciaTipoLista');
        // Obtener el select id_tipoLista del formulario
        $select = $form->getElementsByTagName('select')->item(0);
        // Obtener las opciones del select
        $options = $select->getElementsByTagName('option');
        // Armar array con los tipos de lista
        $tipos_listas = $this->armarArray($options);
        // Obtener el select id cuDependencia del formulario
        $select = $form->getElementsByTagName('select')->item(1);
        // Obtener las opciones del select
        $options = $select->getElementsByTagName('option');
        // Armar array con las dependencias
        $dependencias = $this->armarArray($options);
        // Retornar array con los tipos de lista y dependencias
        return array(
            'tipos_listas' => $tipos_listas,
            'dependencias' => $dependencias
        );
    }

    /**
     * Obtener Listado de Listas de Despacho por rango de fecha
     * y Dependencia
     * @param string $fecha_ini
     * @param string $fecha_fin
     * @param string $dependencia
     * @param string $tipo_lista
     */
    public function getListasDespachoPorRangoFechaYTipo($fecha_ini, $fecha_fin, $dependencia, $tipo_lista)
    {
        // Array que contendra las listas de despacho
        $listas_despacho = array();
        // Armar URL para consulta de Listas de Despacho
        $url = "https://portalservicios.jusformosa.gob.ar/listadespachoV3/consultas/C_consulta/ConsultaListaDespacho/{$dependencia}/NULL/{$fecha_ini}_{$fecha_fin}/{$tipo_lista}/";
        // Obtener el HTML de la URL mediante un POST con body vacio
        print("Realizar la consulta de listas de despacho: {$dependencia} - {$tipo_lista} - {$fecha_ini} - {$fecha_fin}");
        $html = $this->curl_post($url, array());
        print("Resultado obtenido.");
        // Buscar en el html la etiqueta <main
        $explodeMain = explode('<main', $html);
        // Buscar etiqueta de cierre del main
        $explodeMain = explode('</main>', $explodeMain[1]);
        // Obtener el HTML del main
        $html = $explodeMain[0];
        // Quitar lo restante del tag meta (buscar el pico > de cierre)
        $searchTrashMain = explode('>', $html);
        $searchTrashMain = $searchTrashMain[0];
        // Borrar lo que tenga $searchTrashMain
        $html = str_replace($searchTrashMain . '>', '', $html);
        // Vamos a extraer cada contenedor que tenga la etiqueta <div id="accordion">
        $accordions = explode('<div id="accordion">', $html);
        // Quitar el primer elemento de accordions
        array_shift($accordions);
        // Iterar cada accordion, para extraer los datos de cada Lista de Despacho
        foreach ($accordions as $accordion) {
            // Vamos a buscar la fecha de la lista
            $fecha = $this->getFechaLista($accordion);
            // Extraer el ID de la Lista de Despacho
            $id = $this->getIdListaDespacho($accordion);
            // Obtener la lista de Despacho
            $lista_despacho = $this->getListaDespachoPorId($id, $dependencia);
            print("Iterando lista de despacho: {$id} - {$fecha} - {$dependencia}");
            // Extraer expedientes
            $expedientes = array();
            foreach ($lista_despacho as $expediente) {
                // Extraer los movimientos del expediente
                $movimientos = array();
                // Validar que $expediente['expediente_movimientos'] exista y sea un array
                if (isset($expediente['expediente_movimientos']) && is_array($expediente['expediente_movimientos'])) {
                    // Iterar cada movimiento
                    foreach ($expediente['expediente_movimientos'] as $movimiento) {
                        // Quitar todos los tags html del texto
                        $texto = strip_tags($movimiento['movimiento_texto']);
                        array_push(
                            $movimientos,
                            array(
                                'fecha' => $movimiento['movimiento_fechaEstado'],
                                'estado' => $movimiento['movimiento_estado'],
                                'texto' => $texto,
                                'titulo' => $movimiento['movimiento_impresion'],
                                'despacho' => $movimiento['movimiento_textoDespacho']
                            )
                        );
                    }
                }
                array_push(
                    $expedientes,
                    array(
                        'orden' => $expediente['expediente_orden'],
                        'numero' => $expediente['expediente_numero'],
                        'anio' => $expediente['expediente_anio'],
                        'caratula' => $expediente['expediente_caratula'],
                        'reservado' => $expediente['expediente_reservado'],
                        'movimientos' => $movimientos
                    )
                );
            }
            // Insertar en el array de listas de despacho esta lista
            array_push(
                $listas_despacho,
                array(
                    'fecha' => $fecha,
                    'id' => $id,
                    'dependencia' => $dependencia,
                    'tipo_lista' => $tipo_lista,
                    'expedientes' => $expedientes
                )
            );
        }
        print("Listas de despacho obtenidas: " . count($listas_despacho));
        return $listas_despacho;
    }
}
?>