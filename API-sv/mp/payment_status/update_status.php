<?php

// Script para mantener actualizado el status payment de la tabla mercado_pago

// 1. Solicito todas las filas que tengan el campo status != approved
// 2. Realizo una petición a la API de Mercado Pago para obtener el status del payment
// 3. Actualizo la tabla mercado_pago con el status obtenido

class update_status
{
    private $conexion;
    private $list_payment;
    private $ACCES_TOKEN = 'TEST-5548694823343472-041412-4dd92592ca1e30d38ecfd4053f041c33-1751465896';

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function updateStatus($id, $status)
    {
        // valido que el id no sea nulo
        if (empty($id)) {
            echo json_encode([
                'status' => 400,
                'message' => 'El id no puede ser nulo'
            ]);
            return;
        }
        //valido que $status sea una cadena de texto
        if (!is_string($status)) {
            echo json_encode([
                'status' => 400,
                'message' => 'El status debe ser una cadena de texto'
            ]);
            return;
        }

        try {
            // busco en la tabla mercado_pago el id_mp == id y actualizo el status
            $sql = "UPDATE mercado_pago SET status = '$status' WHERE id_mp = '$id'";
            $this->conexion->query($sql);

            // Devuelvo mensaje de éxito en JSON
            echo json_encode([
                'status' => 200,
                'message' => 'Status actualizado correctamente' . $id
            ]);
        } catch (\Throwable $th) {
            // Devuelve mensaje de error en JSON
            echo json_encode([
                'status' => 500,
                'message' => 'Error actualizando el status: ' . $th->getMessage()
            ]);
        }
    }
}
