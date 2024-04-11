<?php

// clase para consultar el estado de una suscripción obteniendo el id de la suscripción
class status
{
    private $ACCES_TOKEN = 'TEST-5763954744698204-040908-f7c5b76430483ea6f5f3ef24a640493c-1751465896';
    // creo la funcion status_by_id para hacer get a https://api.mercadopago.com/preapproval/{id} esperando un json


    public function preapproval($preapproval_id)
    {
        // apuntamos a la url: https://api.mercadopago.com/preapproval/{id}
        $url = 'https://api.mercadopago.com/preapproval/' . $preapproval_id;
        // preparamos el header para enviar un .json con el ACCES_TOKEN de .env
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->ACCES_TOKEN
        );

        // hacemos el GET con curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // devolvemos el json
        return $response;
    }
}
