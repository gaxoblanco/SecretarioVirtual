
<?php
// este archivo se encargar de actualiar la lista secretaries de la base de datos
// obtiene el id_user y el oldSemail como referencia para ubicar el secretario a actualizar

// el newSemail y el Spass pueden o no ser != ''
// si son != '' se actualizan los campos correspondientes con los nuevos valores

require_once './config.php';
class user_update_secretary
{
  private $conexion;
  private $userId;
  private $secreataryId;
  private $oldSemail;
  private $newSemail;
  private $Spass;
  private $firstName;

  public function __construct($conexion, $userId, $secreataryId, $oldSemail, $newSemail, $Spass, $firstName)
  {
    $this->conexion = $conexion;
    $this->userId = $userId;
    $this->secreataryId = $secreataryId;
    $this->oldSemail = $oldSemail;
    $this->newSemail = $newSemail;
    $this->Spass = $Spass;
    $this->firstName = $firstName;
  }

  public function updateSecretary()
  {
    try {
      //consulto si existe un secretario en la tabla secretaries que concinda el campo id_user y el campo secreataryId
      $query = $this->conexion->prepare('SELECT * FROM secretaries WHERE id_users = :id_users AND secreataryId = :secreataryId');
      $query->execute([
        ':id_users' => $this->userId,
        ':secreataryId' => $this->secreataryId
      ]);
      $secretary = $query->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      // Devolver mensaje de error en json
      http_response_code(500);
      echo json_encode('Error al consultar el secretario');
    }

    // si no existe un secretario que concinda el campo id_user y el campo secreataryId devuelvo un mensaje de error
    if (!$secretary) {
      echo json_encode([
        'status' => 404,
        'message' => 'No existe un secretario con ese id'
      ]);
      return;
    }

    //se el campo firstName es diferente de '' actualizo el campo firstName del $secretary
    if ($this->firstName != '') {
      try {
        $query = $this->conexion->prepare('UPDATE secretaries SET firstName = :firstName WHERE id_users = :id_users AND secreataryId = :secreataryId');
        $query->execute([
          ':firstName' => $this->firstName,
          ':id_users' => $this->userId,
          ':secreataryId' => $this->secreataryId
        ]);
      } catch (PDOException $e) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al actualizar el nombre');
      }
    }

    //si el newSemail es diferente de '' actualizo el campo email del $secretary
    if ($this->newSemail != '') {
      try {
        // Verificar si el correo electrónico ya está registrado en algun secreatario
        $query = $this->conexion->prepare('SELECT COUNT(*) FROM secretaries WHERE Semail = :Semail');
        $query->execute([':Semail' => $this->newSemail]);
        $count = $query->fetchColumn();
      } catch (PDOException $e) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al consultar el numero de secretarios para crear uno nuevo');
      }

      if ($count > 0) {
        // Devolver mensaje de error en json
        http_response_code(202);
        echo json_encode('El correo electrónico ya existe');
        return;
      }

      try {
        $query = $this->conexion->prepare('UPDATE secretaries SET Semail = :Semail WHERE id_users = :id_users AND secreataryId = :secreataryId');
        $query->execute([
          ':Semail' => $this->newSemail,
          ':id_users' => $this->userId,
          ':secreataryId' => $this->secreataryId
        ]);
      } catch (PDOException $e) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al actualizar el correo electrónico');
      }
    }

    //si el Spass es diferente de '' genro un hash de la contraseña y actualizo el campo Spass del $secretary
    if ($this->Spass != '') {
      $Spass = password_hash($this->Spass, PASSWORD_DEFAULT);

      try {
        $query = $this->conexion->prepare('UPDATE secretaries SET Spass = :Spass WHERE id_users = :id_users AND secreataryId = :secreataryId');
        $query->execute([
          ':Spass' => $Spass,
          ':id_users' => $this->userId,
          ':secreataryId' => $this->secreataryId
        ]);
      } catch (PDOException $e) {
        // Devolver mensaje de error en json
        http_response_code(500);
        echo json_encode('Error al actualizar la contraseña');
      }
    }
    // devuelvo un mensaje de exito
    http_response_code(200);
    echo json_encode('Secretario actualizado correctamente');
  }
}
