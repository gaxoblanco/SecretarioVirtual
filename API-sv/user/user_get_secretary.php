<?php

class user_get_secretary
{
    private $conexion;
    private $userId;

    public function __construct($conexion, $userId)
    {
        $this->conexion = $conexion;
        $this->userId = $userId;
    }

    public function getSecretaries()
    {
        try {
            // Obtener los secretarios del usuario de la tabla secretaries
            $query = $this->conexion->prepare('SELECT * FROM secretaries WHERE id_users = :id_users');
            $query->execute([':id_users' => $this->userId]);
            $secretaries = $query->fetchAll(PDO::FETCH_ASSOC);

            //devolver los secretarios en json
            echo json_encode([
                'status' => 200,
                'message' => 'Secretarios obtenidos correctamente',
                'data' => $secretaries
            ]);
            // print_r($secretaries);
            return $secretaries;
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 500,
                'message' => 'Error obteniendo los secretarios: ' . $e->getMessage()
            ]);
            return [];
        }
    }
}
