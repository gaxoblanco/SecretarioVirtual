
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
    $this->userId = $userId;
  }

  public function getDispatches()
  {
    try {
      // Obtener los expedientes del usuario de la tabla user_expedients usando el userId como filtro en la columna id_user
      $query = "SELECT * FROM user_expedients WHERE id_user = :userId";
      $stmt = $this->conexion->prepare($query);
      $stmt->bindParam(':userId', $this->userId, PDO::PARAM_INT);
      $stmt->execute();
      $dispatches = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Verificar si $dispatches está vacío
      if (empty($dispatches)) {
        http_response_code(204);  // Sin contenido
        echo json_encode(['message' => 'No hay expedientes para este usuario']);
        return;
      }

      //valido que sea un json
      $jsonDispatches = json_encode($dispatches);
      if ($jsonDispatches === null) {
        http_response_code(500);  // Error interno del servidor
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
  }
}
