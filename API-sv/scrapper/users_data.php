<?php
// este archivo se encargar de solicitar la lista de expedientes por cada usuario y devuelve cada lista en un array

// 1 - Get the users with pagination
// 2 - Get expedients from the users, take the expedients with pagination
// 3 - Get movings from the expedients



require_once 'db.php';

class users_data
{
  private $conexion;

  public function __construct($conexion)
  {
    $this->conexion = $conexion;
  }

  // consulta en la tabla users por el id_user y crea un array con el obj:
  // 1 - Get the users
  public function getUsers($offset, $limit)
  {
    try {
      // Obtener los usuarios de la tabla users con paginación
      $query = $this->conexion->prepare('SELECT * FROM users LIMIT :offset, :limit');
      $query->bindParam(':offset', $offset, PDO::PARAM_INT);
      $query->bindParam(':limit', $limit, PDO::PARAM_INT);
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
  // 2 - Get expedients from the users
  public function userExpedients($offset, $limit)
  {
    $users = $this->getUsers($offset, $limit);
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
  // 3 - Get movings from the expedients
  public function haveMovings(&$expedients)
  {
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
