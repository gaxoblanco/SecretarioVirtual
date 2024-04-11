<?php

class post_plan
{
  private $ACCES_TOKEN = 'TEST-5763954744698204-040908-f7c5b76430483ea6f5f3ef24a640493c-1751465896';

  // importo el ACCES_TOKEN

  // hago el post a la api de mercadopago para crear un plan de subscripción
  public function post_plan($reason)
  {
    // apuntamos a la url: https://api.mercadopago.com/preapproval_plan
    $url = 'https://api.mercadopago.com/preapproval_plan';
    // preparamos el header para enviar un .json con el ACCES_TOKEN de .env
    $header = array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $this->ACCES_TOKEN
    );

    // hacemos el body para enviar los datos del plan de subscripción ejemplo:
    // en este caso vamos a crear un plan de subscripción de 10 pesos argentinos por mes con 12 repeticiones
    // y un trial de 1 mes

    // Para basic, premium y pro necesitaria 3 bodys diferentes o pasarles variables, al hacer = `{... da error
    $body = '{
            "reason": "Secretario Virtual",
            "auto_recurring": {
              "frequency": 1,
              "frequency_type": "months",
              "repetitions": 12,
              "billing_day": 10,
              "billing_day_proportional": true,
              "free_trial": {
                "frequency": 1,
                "frequency_type": "months"
              },
              "transaction_amount": 100,
              "currency_id": "ARS"
            },
            "payer_email": "test_user_1579957027@testuser.com",
            "back_url": "https://www.secretariovirtual.ar/status"
          }';

    // hacemos el POST con curl
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);


    // devolvemos la respuesta de la API
    return $response;
  }
}
