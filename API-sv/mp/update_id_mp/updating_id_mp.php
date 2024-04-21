<?php
/*
Script para mantener la tabla mercado_pago actualizada con los datos de la API de Mercado Pago

1. Solicito todos las filas que tengan el campo id_mp en null
2. Realizo una petición a la API de Mercado Pago para obtener el id_mp
3. Actualizo la tabla mercado_pago con el id_mp obtenido
*/

class update_id_mp
{
    private $conexion;
    private $list_preapproval;
    private $ACCES_TOKEN = 'TEST-5548694823343472-041412-4dd92592ca1e30d38ecfd4053f041c33-1751465896';

    public function __construct($conexion,)
    {
        $this->conexion = $conexion;
    }

    public function startIdMp()
    {
        try {
            // Obtengo todas las filas de la tabla mercado_pago que tengan el campo id_mp en null
            $query = "SELECT preapproval_id FROM mercado_pago WHERE id_mp IS NULL";
            $stmt = $this->conexion->prepare($query);
            $stmt->execute();
            $this->list_preapproval = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Valido que la lista de preapproval_ids no esté vacía
            if (empty($this->list_preapproval)) {
                echo json_encode([
                    'status' => 400,
                    'message' => 'No hay preapproval_ids sin id_mp'
                ]);
                return;
            }
        } catch (\Throwable $th) {
            // Devuelve mensaje de error en JSON
            echo json_encode([
                'status' => 500,
                'message' => 'Error obteniendo los preapproval_ids: ' . $th->getMessage()
            ]);
            return; // Termina la ejecución del método si hay un error
        }

        // imprimo el tamaño de la lista
        echo json_encode([
            'status' => 200,
            'message' => 'Se encontraron ' . count($this->list_preapproval) . ' preapproval_ids sin id_mp'
        ]);

        // itero sobre la lista para pasarle el preapproval_id a la clase searchData
        foreach ($this->list_preapproval as $preapproval_id) {
            // Utilizo searchData para buscar el preapproval_id en la api de mercado pago
            require_once 'search_data.php';
            $searchData = new searchData($this->conexion, $this->ACCES_TOKEN);
            $searchData->searchData(50, $preapproval_id['preapproval_id']);

            // imprimo un mensaje de exito para el preapproval_id
            echo json_encode([
                'status' => 200,
                'message' => 'Se actualizó el id_mp del preapproval_id: ' . $preapproval_id['preapproval_id']
            ]);
        }
    }
}
