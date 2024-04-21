<?php

/*
Si el usuario por algun motivo no pudo completar el pago,
se le da la posibilidad de reintentar el pago
*/

class get_init_point
{
    private $conexion;
    private $user_id;

    public function __construct($conexion, $user_id)
    {
        $this->conexion = $conexion;
        $this->user_id = $user_id;
    }

    // Hago una consulta al a tabla mercado_pago para obtener el init_point segun el user_id
    public function getInitPoint()
    {
        try {
            // obtengo de la tabla mercado_pago el init_point del elemento que contenga el mismo user_id
            $query = "SELECT init_point FROM mercado_pago WHERE user_id = :user_id";
            $stmt = $this->conexion->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id, PDO::PARAM_STR);
            $stmt->execute();
            // guardo el init_point en la variable init_point
            $init_point = $stmt->fetch(PDO::FETCH_ASSOC)['init_point'];

            // valido que init_point no sea null
            if ($init_point == null) {
                echo json_encode([
                    'status' => 400,
                    'message' => 'El init_point no existe'
                ]);
                return;
            }
            echo json_encode([
                'status' => 200,
                'init_point' => $init_point
            ]);
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 500,
                'message' => 'Error obteniendo el init_point: ' . $e->getMessage()
            ]);
            return;
        }
    }
}
