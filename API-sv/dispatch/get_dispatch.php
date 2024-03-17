
<?php
//    Este archivo se encarga de obtener los expedientes de la base de datos y devolverlos como respuesta en formato JSON.
//    Se utiliza en el archivo dispatch\index.php
class get_dispatch
{
  private $userId;
  private $conexion;

  public function __construct($conexion, $userId)
  {
    $this->conexion = $conexion;
    $this->userId = 26;
  }

  public function getDispatches()
  {
    try {
      // Obtener los expedientes del usuario de la tabla dispatchlist
      $query = $this->conexion->prepare('SELECT * FROM user_expedients WHERE id_user = :id_user ORDER BY anio_exp DESC');
      $query->execute([':id_user' => $this->userId]);
      $dispatches = $query->fetchAll(PDO::FETCH_ASSOC);

      // Verificar si $dispatches está vacío
      if (empty($dispatches)) {
        http_response_code(204);  // Sin contenido
        echo json_encode(['message' => 'No hay expedientes para este usuario']);
        return;
      }

      // Devolver los expedientes como respuesta en formato JSON
      http_response_code(200);
      echo json_encode($dispatches);
      return;
    } catch (PDOException $e) {
      http_response_code(500);  // Error interno del servidor
      echo json_encode(['error' => 'Error al obtener los expedientes: ' . $e->getMessage()]);
    }
    //devuelvo por consola el array de expedientes
    echo json_encode($dispatches);
  }
}
