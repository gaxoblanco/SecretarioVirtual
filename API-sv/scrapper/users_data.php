<?php
// este archivo se encargar de solicitar la lista de expedientes por cada usuario y devuelve cada lista en un array	


require_once 'db.php';

class users_data
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // consulta en la tabla users por el id_user y crea un array con el obj:

    public function getUsers()
    {
        try {
            // Obtener los usuarios de la tabla users
            $query = $this->conexion->prepare('SELECT * FROM users');
            $query->execute();
            $users = $query->fetchAll(PDO::FETCH_ASSOC);

            // Devolver los usuarios como respuesta en formato JSON
            // echo 'userList' . json_encode($users);
            return $users;
        } catch (PDOException $e) {
            echo 'Error al obtener los usuarios: ' . $e->getMessage();
        }
    }

    // Consultar por cada usuario en la tabla user_expedients y cargar en $user['expedients'] los expedientes asociados a cada usuario
    public function getExpedients()
    {
        $users = $this->getUsers();
        try {
            // Obtener los expedientes del usuario de la tabla dispatchlist
            foreach ($users as &$user) {
                $query = $this->conexion->prepare('SELECT * FROM user_expedients WHERE id_user = :id_user');
                $query->execute([':id_user' => $user['id_user']]);
                $expedients = $query->fetchAll(PDO::FETCH_ASSOC);
                $user['expedients'] = $expedients;

                //itero por cada movimiento que pueda tener el expediente
                $this->haveMovings($user['expedients']);
            }

            // devuelve un nuevo array con los expediente cargados
            return $users;
        } catch (PDOException $e) {
            echo 'Error al obtener los expedientes: ' . $e->getMessage();
        }
    }

    // crea la funcion haveMovings() que recibe expedients y consulta por sus movimientos en la tabla user_exp_move
    public function haveMovings(&$expedients)
    {
        // Traigo el array con los usuarios y sus expedientes
        // $expedients = $this->getExpedients();

        try {
            // Obtener los movimientos del expediente de la tabla user_exp_move asociados al id_exp obtenido en $expedients[expedients] y consultar por cada expediente cargado en el array

            foreach ($expedients as &$expedient) {
                $query = $this->conexion->prepare('SELECT * FROM user_exp_move WHERE id_exp = :id_exp');
                $query->execute([':id_exp' => $expedient['id_exp']]);

                // Asegúrate de que haya resultados antes de intentar obtenerlos
                if ($query->rowCount() > 0) {
                    $movings = $query->fetchAll(PDO::FETCH_ASSOC);
                    $expedient['movimientos'] = $movings;
                } else {
                    // Si no hay movimientos, establece un array vacío
                    $expedient['movimientos'] = [];
                }
            }

            // devuelve un nuevo array con los movimientos cargados
            return $expedients;
        } catch (PDOException $e) {
            echo 'Error al obtener los movimientos: ' . $e->getMessage();
        }
    }
}
