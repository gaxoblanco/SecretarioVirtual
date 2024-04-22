<?php

/* El puntero https://api.mercadopago.com/preapproval/{id} no funciona con el preapproval_id, por lo que se debe buscar el id_mp en la api de mercado pago.
 ------------------------------------------------------------------------
El search hace peticiones a https://api.mercadopago.com/preapproval/search
Realizo la peticion hasta obtener el id_mp que estoy buscando.
*/

/*
    1. Inicializa el paginado paginado usando la API de Mercado Pago
    2. Actualizo la subcripción en la base de datos con el id_mp
*/

class search_data
{
  private $conexion;
  private $ACCES_TOKEN;

  public function __construct($conexion, $ACCES_TOKEN)
  {
    $this->conexion = $conexion;
    $this->ACCES_TOKEN = $ACCES_TOKEN;
  }

  public function searchData($limit, $preapproval_id)
  {
    // Inicializa el valor de offset en 0
    $offset = 0;

    // Realiza la primera solicitud para obtener el valor total de la paginación
    $url = 'https://api.mercadopago.com/preapproval/search?limit=' . $limit . '&offset=' . $offset;
    $header = array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $this->ACCES_TOKEN
    );

    try {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($curl);
      curl_close($curl);

      $response = json_decode($response, true);
      // echo json_encode($response);
      // Obtiene el valor total de la paginación
      $total = $response['paging']['total'];
    } catch (\Throwable $th) {
      // Devuelve mensaje de error en JSON
      echo json_encode([
        'status' => 500,
        'message' => 'Error obteniendo el total de suscriptos: ' . $th->getMessage()
      ]);
      return; // Termina la ejecución del método si hay un error
    }

    // Calcula el número de iteraciones necesarias
    $iterations = ceil($total / $limit);

    // Iterate through the necessary iterations
    for ($i = 0; $i < $iterations; $i++) {
      // Update the offset in each iteration
      $offset = $i * $limit;

      // Make the request to retrieve subscribers data
      $url = 'https://api.mercadopago.com/preapproval/search?limit=' . $limit . '&offset=' . $offset;
      $header = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $this->ACCES_TOKEN
      );

      try {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($response, true);
      } catch (\Throwable $th) {
        // Return error message in JSON
        echo json_encode([
          'status' => 500,
          'message' => 'Error getting data in for loop: ' . $th->getMessage()
        ]);
        return; // Terminate method execution if there's an error
      }

      // echo json_encode($preapproval_id);
      // itero sobre los resultados de la respuesta
      foreach ($response['results'] as $result) {
        if ($result['preapproval_plan_id'] == $preapproval_id) {
          // echo json_encode($result);

          // llamo a la funcion para actualizar el id_mp en la base de datos
          require_once 'update_id_mp.php';
          $updateIdMp = new update_id_mp($this->conexion, $result['id']);
          $updateIdMp->updateIdMp($preapproval_id);
        }
      }
    }
  }
}
