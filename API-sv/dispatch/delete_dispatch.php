<?php

class delete_dispatch
{
  private $userId;
  private $dispatchId;
  private $conexion;

  public function __construct($conexion, $userId, $dispatchId)
  {
    $this->conexion = $conexion;
    $this->userId = $userId;
    $this->dispatchId = $dispatchId;
  }

  public function deleteDispatchFromList()
  {
    try {
      // Verificar si el usuario existe en la base de datos
      $query = $this->conexion->prepare('SELECT * FROM users WHERE id_user = :id_user');
      $query->execute([':id_user' => $this->userId]);
      $userExists = $query->fetch(PDO::FETCH_ASSOC);

      if (!$userExists) {
        echo "Error: El usuario no existe.";
        return;
      }

      // Verificar si el expediente existe para el usuario dado
      $query = $this->conexion->prepare('SELECT * FROM user_expedients WHERE id_user = :id_user AND CONCAT(id_exp) = :id_exp');
      $query->execute([':id_user' => $this->userId, ':id_exp' => $this->dispatchId]);
      $dispatchExists = $query->fetch(PDO::FETCH_ASSOC);

      if (!$dispatchExists) {
        echo "Error: No se encontró el expediente.";
        return;
      }

      // Consulta en la tabla user_exp_move si existe algun movimiento para el expediente
      $query = $this->conexion->prepare('SELECT * FROM user_exp_move WHERE id_exp = :id_exp');
      $query->execute([':id_exp' => $this->dispatchId]);
      $dispatchHasMoves = $query->fetch(PDO::FETCH_ASSOC);

      // Si el expediente tiene movimientos, los elimina
      if ($dispatchHasMoves) {
        $query = $this->conexion->prepare('DELETE FROM user_exp_move WHERE id_exp = :id_exp');
        $query->execute([':id_exp' => $this->dispatchId]);
      }

      // Eliminar el expediente de la tabla user_expedients
      $query = $this->conexion->prepare('DELETE FROM user_expedients WHERE id_exp = :id_exp AND CONCAT(id_exp) = :id_exp');
      $query->execute([':id_user' => $this->userId, ':id_exp' => $this->dispatchId]);
      $response = [
        'message' => 'Expediente eliminado correctamente.'
      ];
      echo json_encode($response);
    } catch (PDOException $e) {
      $response = [
        'error' => 'Error de conexión: ' . $e->getMessage()
      ];
      echo json_encode($response);
    }
  }
}
