<?php

class updateIdMp
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

        try {
            // Actualizo el id_mp en la tabla mercado_pago
            $query = "UPDATE mercado_pago SET id_mp = :id_mp WHERE preapproval_id = :preapproval_id";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':id_mp', $this->id_mp, PDO::PARAM_STR);
            $stmt->bindParam(':preapproval_id', $this->preapproval_id, PDO::PARAM_STR);
            $stmt->execute();

            echo json_encode([
                'status' => 200,
                'message' => $this->preapproval_id
            ]);
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 500,
                'message' => 'Error actualizando el id_mp: ' . $e->getMessage()
            ]);
        }
    }
}
