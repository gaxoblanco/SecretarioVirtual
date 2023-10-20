<?php

class user_delete_secretary
{
    private $conexion;
    private $userId;
    private $Semail;

    public function __construct($conexion, $userId, $Semail)
    {
        $this->conexion = $conexion;
        $this->userId = $userId;
        $this->Semail = $Semail;
    }

    public function deleteSecretary()
    {
        try {
            // Verificar si el secretario con el correo electrónico proporcionado existe para el usuario
            $query = $this->conexion->prepare('SELECT COUNT(*) FROM secretaries WHERE id_users = :id_users AND Semail = :Semail');
            $query->execute([':id_users' => $this->userId, ':Semail' => $this->Semail]);
            $count = $query->fetchColumn();

            if ($count === 0) {
                //devuelve mensaje de error en json
                echo json_encode([
                    'status' => 400,
                    'message' => 'El secretario no existe'
                ]);
                return;
            }

            // Eliminar el secretario de la tabla secretaries
            $deleteQuery = $this->conexion->prepare('DELETE FROM secretaries WHERE id_users = :id_users AND Semail = :Semail');
            $deleteQuery->execute([':id_users' => $this->userId, ':Semail' => $this->Semail]);

            //devuelve mensaje de éxito en json
            echo json_encode([
                'status' => 200,
                'message' => 'Secretario eliminado correctamente'
            ]);
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 500,
                'message' => 'Error eliminando el secretario: ' . $e->getMessage()
            ]);
        }
    }
}
