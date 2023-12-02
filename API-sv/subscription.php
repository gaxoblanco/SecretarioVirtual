<?php
// obtiene el id_user y con el consulta en la tabla users y obtiene el id_subscription
// con el id_subscription consulta en la tabla suscriptions y obtiene toda la informacion de la suscripcion
// con la informacion de la suscripcion se devuelve un objeto json de num_exp y num_secretaries
// si no se encuentra el id_user se devuelve un objeto json con error
// si no se encuentra el id_subscription se devuelve un objeto json con error
// si no se encuentra la suscripcion se devuelve un objeto json con error
// si no se encuentra la informacion de la suscripcion se devuelve un objeto json con error

class subscription
{
    private $id_user;
    private $conexion;

    public function __construct($conexion, $id_user)
    {
        $this->conexion = $conexion;
        $this->id_user = $id_user;
    }

    public function subscriptionUser()
    {
        // Verificar si el id_user existe en la base de datos
        try {
            $query = $this->conexion->prepare('SELECT id_user FROM users WHERE id_user = :id_user');
            $query->execute([':id_user' => $this->id_user]);
            $existingUser = $query->fetch(PDO::FETCH_ASSOC);

            if (!$existingUser) {
                //devuelve mensaje de error en json
                echo json_encode([
                    'status' => 400,
                    'message' => 'El id_user no existe'
                ]);
                return;
            }
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 400,
                'message' => 'Error al obtener el id_user'
            ]);
            return;
        }

        // obtiene el id_subscription del id_user
        try {
            $query = $this->conexion->prepare('SELECT id_subscription FROM users WHERE id_user = :id_user');
            $query->execute([':id_user' => $this->id_user]);
            $id_subscription = $query->fetch(PDO::FETCH_ASSOC);

            if (!$id_subscription) {
                //devuelve mensaje de error en json
                echo json_encode([
                    'status' => 400,
                    'message' => 'El id_user no existe'
                ]);
                return;
            }
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 400,
                'message' => 'Error al obtener el id_subscription'
            ]);
            return;
        }

        // Verificar si el id_subscription existe en la base de datos
        try {
            $query = $this->conexion->prepare('SELECT id_subscription FROM subscription WHERE id_subscription = :id_subscription');
            $query->execute([':id_subscription' => $id_subscription['id_subscription']]);
            $existingSuscription = $query->fetch(PDO::FETCH_ASSOC);

            if (!$existingSuscription) {
                //devuelve mensaje de error en json
                echo json_encode([
                    'status' => 400,
                    'message' => 'El id_subscription no existe'
                ]);
                return;
            }
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 400,
                'message' => 'Error al obtener el id_subscription'
            ]);
            return;
        }

        // obtiene la informacion de la suscripcion
        try {
            $query = $this->conexion->prepare('SELECT * FROM subscription WHERE id_subscription = :id_subscription');
            $query->execute([':id_subscription' => $id_subscription['id_subscription']]);
            $suscription = $query->fetch(PDO::FETCH_ASSOC);

            //devolver un mensaje de éxito en json
            // echo json_encode([
            //     // 'status' => 200,
            //     // 'message' => 'Suscripcion obtenida correctamente',
            //     'suscription' => $suscription // deuvleve todo el contenido en esa fila
            // ]);
            return $suscription;
        } catch (PDOException $e) {
            //devuelve mensaje de error en json
            echo json_encode([
                'status' => 400,
                'message' => 'Error al obtener la suscripcion'
            ]);
            return;
        }
    }
}
