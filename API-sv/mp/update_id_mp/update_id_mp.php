<?php

class update_id_mp
{
    private $conexion;
    private $id_mp;
    private $preapproval_id;

    public function __construct($conexion, $id_mp)
    {
        $this->conexion = $conexion;
        $this->id_mp = $id_mp;
    }

    public function updateIdMp($preapproval_id)
    {
        $this->preapproval_id = $preapproval_id;

        // valido que el id_mp no esté vacío
        if (empty($this->id_mp)) {
            echo json_encode([
                'status' => 400,
                'message' => 'El id_mp esta vacio - el usuario aun no ha pagado la suscripción'
            ]);
            return;
        }

        try {
            // Actualizo el id_mp en la tabla mercado_pago
            $query = "UPDATE mercado_pago SET id_mp = :id_mp WHERE preapproval_id = :preapproval_id";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':id_mp', $this->id_mp, PDO::PARAM_STR);
            $stmt->bindParam(':preapproval_id', $this->preapproval_id, PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Throwable $th) {
            // Devuelve mensaje de error en JSON
            echo json_encode([
                'status' => 500,
                'message' => 'Error actualizando el id_mp: ' . $th->getMessage()
            ]);
            return; // Termina la ejecución del método si hay un error
        }

        // imprimo un mensaje de exito para el preapproval_id
        echo json_encode([
            'status' => 200,
            'message' => 'Se actualizó el id_mp del preapproval_id: ' . $this->id_mp
        ]);
    }
}
