

<?php
// require_once 'conexion.php';
// Importar el cliente SQL
require_once 'db.php';

class process_news
{
    private $db;

    public function __construct($conexion)
    {
        $this->db = $conexion;
    }

    //obtener los datos del usuario segun el $newMoves['id_expediente']
    public function getUserData($id_expediente)
    {
        $query = $this->db->prepare('SELECT * FROM users WHERE id_user = (SELECT id_user FROM user_expedients WHERE id_user_exp = (SELECT id_user_exp FROM expedientes WHERE id_expediente = :id_expediente))');
        $query->execute([':id_expediente' => $id_expediente]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        return $user;
    }

    // obtener el Semail de los secretarios asociados al userId
    public function getSecretaryEmail($userId)
    {
        $query = $this->db->prepare('SELECT Semail FROM secretaries WHERE userId = :userId');
        $query->execute([':userId' => $userId]);
        $secretary = $query->fetch(PDO::FETCH_ASSOC);
        return $secretary;
    }

    // guardar los datos del usuario en un array junto con el array de secretarios que contiene el Semail y el array de expediente con sus movimientos
    public function get_all_dispatch_user($newMoves)
    {
        $newMovesTo = [];
        foreach ($newMoves as $key => $value) {
            $user = $this->getUserData($value['id_expediente']);
            $secretary = $this->getSecretaryEmail($user['id_user']);
            $newMovesTo[] = [
                'id_user' => $user['id_user'],
                'firstName' => $user['firstName'],
                'lastName' => $user['lastName'],
                'email' => $user['email'],
                'password' => $user['password'],
                'token' => $user['token'],
                'Semail' => $secretary['Semail'],
                'id_expediente' => $value['id_expediente'],
                'fecha_movimiento' => $value['fecha_movimiento'],
                'estado' => $value['estado'],
                'texto' => $value['texto'],
                'titulo' => $value['titulo'],
                'despacho' => $value['despacho'],
                'id_exp' => $value['id_exp']
            ];

            echo $user . $secretary . $value; // para debug
        }
        return $newMovesTo;
    }
}
