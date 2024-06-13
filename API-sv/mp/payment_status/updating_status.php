<?php

// Script para mantener actualizado el status payment de la tabla mercado_pago

// 1. Solicito todas las filas que tengan el campo status != approved
// 2. Realizo una petición a la API de Mercado Pago para obtener el status del payment
// 3. Actualizo la tabla mercado_pago con el status obtenido

class updating_status
{
    private $conexion;
    private $list_payment;
    private $ACCES_TOKEN = 'TEST-5548694823343472-041412-4dd92592ca1e30d38ecfd4053f041c33-1751465896';

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function startStatus()
    {
        //1 - Hago la consulta a la API de mercado pago para obtener la primer iteracion
        echo 'Iniciando actualización de status de pagos' . PHP_EOL;
        include_once './mp/payment_status/search_status.php';
        $searchStatus = new search_status($this->conexion, $this->ACCES_TOKEN);
        $searchStatus->searchStatus(50);
        echo 'Finalizando actualización de status de pagos' . PHP_EOL;
    }
}
