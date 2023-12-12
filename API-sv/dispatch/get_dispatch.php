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
    $this->$userId = $userId;
  }

  public function getDispatches()
  {
    try {
      // Obtener los 20primeros expedientes del usuario de la tabla dispatchlist
      $query = $this->conexion->prepare('SELECT * FROM user_expedients WHERE id_user = :id_user ORDER BY id_exp DESC LIMIT 20');
      $query->execute([':id_user' => $this->userId]);
      $dispatches = $query->fetchAll(PDO::FETCH_ASSOC);

      // Verificar si $dispatches está vacío
      if (empty($dispatches)) {
        echo json_encode([
          // mensaje de que la consulta salio bien pero no tiene datos
          http_response_code(200),
          'message' => 'No hay expedientes para este usuario'
        ]);
        return;
      }
      // Devolver los expedientes como respuesta en formato JSON
      echo json_encode($dispatches);
      return $dispatches;
    } catch (PDOException $e) {
      echo 'Error al obtener los expedientes: ' . $e->getMessage();
    }
  }
}
