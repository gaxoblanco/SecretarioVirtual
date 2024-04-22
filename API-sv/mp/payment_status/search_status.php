<?php

class search_status
{
    private $conexion;
    private $ACCES_TOKEN;

    public function __construct($conexion, $ACCES_TOKEN)
    {
        $this->conexion = $conexion;
        $this->ACCES_TOKEN = $ACCES_TOKEN;
    }

    public function searchStatus($limit = 50)
    {
        echo 'Iniciando actualización de status de pagos' . PHP_EOL;
        // Inicializa el valor de offset en 0
        $offset = 0;

        // Realiza la primera solicitud para obtener el valor total de la paginación
        $url = 'https://api.mercadopago.com/v1/payments/search';
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

            // valido que results no sea un array vacio
            if (empty($response['results'])) {
                echo json_encode([
                    'status' => 400,
                    'message' => 'No hay usuarios suscriptos'
                ]);
                return;
            }
        } catch (\Throwable $th) {
            // Devuelve mensaje de error en JSON
            echo json_encode([
                'status' => 500,
                'message' => 'Error obteniendo el total de suscriptos: ' . $th->getMessage()
            ]);
            return; // Termina la ejecución del método si hay un error
        }

        // calcula el número de iteraciones necesarias
        $iterations = ceil($total / $limit);

        // Itero a través de las iteraciones necesarias
        for ($i = 0; $i < $iterations; $i++) {
            // Actualizo el offset en cada iteración
            $offset = $i * $limit;

            // Realizo la solicitud para recuperar los datos de los suscriptores
            $url = 'https://api.mercadopago.com/v1/payments/search?limit=' . $limit . '&offset=' . $offset;
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

            // Itero por cada peticion a la API buscando el id = $id_mp y le paso el id_mp a updating_status
            echo json_encode($response['results']);
            foreach ($response['results'] as $payment) {
                include_once 'updating_status.php';
                $updatingStatus = new update_status($this->conexion);
                $updatingStatus->updateStatus($id, $status);
            }
        }
    }
}
