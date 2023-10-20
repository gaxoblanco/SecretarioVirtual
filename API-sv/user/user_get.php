
<?php
// Obtener la informacion del usuario segun el id_user

// data base
require_once './config.php';

class user_get
{
    private $conexion; // Objeto de conexión PDO
    private $id;

    public function __construct($conexion, $id)
    {
        $this->conexion = $conexion;
        $this->id = $id;
    }

    public function getUsers()
    {
        try {
            // Verificar si el usuario con el id proporcionado existe
            $query = $this->conexion->prepare('SELECT COUNT(*) FROM users WHERE id_user = :id_user');
            $query->execute([':id_user' => $this->id]);
            $count = $query->fetchColumn();

            if ($count === 0) {
                //devuelve mensaje de error en json
                echo json_encode([
                    'status' => 400,
                    'message' => 'El usuario no existe'
                ]);
                return;
            }

            // Obtener la información del usuario menos la contraseña
            $query = $this->conexion->prepare('SELECT id_user, firstName, lastName, email FROM users WHERE id_user = :id_user');
            $query->execute([':id_user' => $this->id]);
            $user = $query->fetch(PDO::FETCH_ASSOC);


            echo json_encode($user);
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 500,
                'message' => 'Error obteniendo el usuario: ' . $e->getMessage()
            ]);
        }
    }
}
