<?php
// Importar clase Listas_Despacho
require_once 'PJF_Listas_Despacho.php';
// Crear instancia de la clase Listas_Despacho
$pjf = new PJF_Listas_Despacho();

//Obtener Tipos de Lista y Dependencias
$tipos_listas_y_dependencias = $pjf->getTiposListasYDependencias();
// Convertir a JSON y guardarlo en un archivo
file_put_contents('tipos_listas_y_dependencias.json', json_encode($tipos_listas_y_dependencias));

// Leer tipos de lista y dependencias desde el archivo JSON
$tipos_listas_y_dependencias = json_decode(file_get_contents('tipos_listas_y_dependencias.json'), true);

// // Tipo de lista
// $tipo_lista = '1'; // Ordinaria
// $dependencia = '7441513'; // Dependencia: Juzgado de 1 Instancia en lo Civil y Comercial N 1

//----------
// Obtener Listas de Despacho por rango de fecha
// $lista_despacho = $pjf->getListasDespachoPorRangoFechaYTipo('2023-06-01', '2023-06-31', $dependencia, $tipo_lista);
// // Guardar Lista de Despacho obtenida
// file_put_contents('lista_despacho.json', json_encode($lista_despacho));

//----------
// Por cada Tipo de Lista, consultar por cada Dependencia
// foreach ($tipos_listas_y_dependencias['tipos_listas'] as $id_tipo_lista => $nombre_tipo_lista) {
//     foreach ($tipos_listas_y_dependencias['dependencias'] as $id_dependencia => $nombre_dependencia) {
//         // Obtener Listas de Despacho
//         $listas_despacho = $pjf->getListasDespacho('2021-01-01', '2023-01-01', $id_dependencia, $id_tipo_lista);
//         echo '<pre>';
//         print_r($listas_despacho);
//         echo '</pre>';
//     }
// }

//----------
// Obtener Lista de Despacho por Expediente y Dependencia
// $dependencia = '7441513'; // Dependencia: Juzgado de 1 Instancia en lo Civil y Comercial N 1
$dependencia = '275555824'; // Dependencia: Juzgado de Instancia en lo Civil, Comercial y del Trabajo
$numero = '276'; // Número de Expediente
$anio = '04'; // Año de Expediente

$expedientes = $pjf->getListaDespachoPorExpediente($dependencia, $numero, $anio);

// Escribimos el resultado en .json
$json = json_encode($expedientes);
file_put_contents("{$dependencia}-{$numero}-{$anio}.json", $json);

//----------
// Obtener Listas de Despacho por Dependencia y Caratula
// $dependencia = '7441513'; // Dependencia: Juzgado de 1 Instancia en lo Civil y Comercial N 1
// $caratula = 'SANCHEZ'; // Caratula

// $listas_despacho = $pjf->getListaDespachoPorCaratula($dependencia, $caratula);
// print_r($listas_despacho);