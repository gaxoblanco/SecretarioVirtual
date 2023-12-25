<?php
// procesamos el formulario de restablecer contraseña, que tiene token, email y password
require_once 'token_expiration.php';
require_once 'token_expiration_secretary.php';

class save_new_pass
{
  private $conexion;
  private $email;
  private $token;
  private $password;

  public function __construct($conexion, $email, $token, $password)
  {
    $this->conexion = $conexion;
    $this->email = $email;
    $this->token = $token;
    $this->password = $password;
  }

  public function restart_password()
  {

    // valido que los campos no esten vacios
    if (empty($this->email) || empty($this->token) || empty($this->password)) {
      http_response_code(400);
      echo json_encode(['message' => 'Todos los campos son requeridos']);
      return;
    }

    // Generar el hash de la contraseña
    $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    try {
      // user table
      // consulto si el correo existe en la tabla users
      try {
        // Verificar que el correo electrónico exista en la base de datos
        $sql = "SELECT id_user FROM users WHERE email = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        //guardo el valor de id_user en user
        $userObj = $stmt->fetch(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
        echo json_encode(['message' => 'Error en el user-email' . $this->email . $this->token . $this->password]);
      }

      if ($userObj && isset($userObj['id_user'])) {

        $user = $userObj['id_user'];

        $token_state = token_expiration_state($this->conexion, $user);

        if ($token_state) {
          //consulto si el token es valido
          try {
            //consulto en la tabla users si el token es valido
            $sql = "SELECT token FROM users WHERE id_user = :id_user";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_user', $user);
            $stmt->execute();
            $token = $stmt->fetch(PDO::FETCH_ASSOC);
          } catch (PDOException $e) {
            echo json_encode(['message' => 'Error en el token']);
          }

          //comparo el token de la tabla con el token del formulario
          if ($token['token'] != $this->token) {
            http_response_code(400);
            echo json_encode(['message' => 'El token no es valido - user']);
            return;
          }

          //$user existe y tiene el id_user
          if ($token_state) {
            try {
              // actualizo el campo password del $user
              $sql = "UPDATE users SET password = :password WHERE id_user = :id_user";
              $stmt = $this->conexion->prepare($sql);
              $stmt->bindParam(':password', $this->password);
              $stmt->bindParam(':id_user', $user);
              $stmt->execute();

              // retorno mensaje de exito
              $stmt->rowCount();
              http_response_code(200);
              echo json_encode(['message' => 'password actualizada']);
            } catch (PDOException $e) {
              echo json_encode(['message' => 'Error en el token']);
            }
          }
        }
      } else {
        // secretaries table

        // consulto en la talba secretaries si el correo existe email_secretary
        try {
          // consulto en la talba secretaries si el correo existe
          $sql = "SELECT secreataryId FROM secretaries WHERE Semail = :Semail";
          $stmt = $this->conexion->prepare($sql);
          $stmt->bindParam(':Semail', $this->email);
          $stmt->execute();
          $secretaryObj = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          echo json_encode(['message' => 'Error en el secretary-email' . $e->getMessage()]);
        }


        if ($secretaryObj && isset($secretaryObj['secreataryId'])) {
          $secretary = $secretaryObj['secreataryId'];
          $token_state = token_expiration_state_secretary($this->conexion, $secretary);

          //consulto si el token es valido
          try {
            // consulto en la tabla secretaries si el token es valido
            $sql = "SELECT token FROM secretaries WHERE secreataryId = :secreataryId";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':secreataryId', $secretary);
            $stmt->execute();
            $token = $stmt->fetch(PDO::FETCH_ASSOC);

            //comparo el token de la tabla con el token del formulario
            if ($token['token'] != $this->token) {
              http_response_code(400);
              echo json_encode(['message' => 'El token no es valido - secretary']);
              return;
            }
          } catch (PDOException $e) {
            echo json_encode(['message' => 'Error en el token']);
          }

          // si el Semail existe actualizo el campo password
          if ($token_state) {
            try {
              // actualizo el campo password del $secretary
              $sql = "UPDATE secretaries SET Spass = :Spass WHERE secreataryId = :secreataryId";
              $stmt = $this->conexion->prepare($sql);
              $stmt->bindParam(':Spass', $this->password);
              $stmt->bindParam(':secreataryId', $secretary);
              $stmt->execute();

              // retorno mensaje de exito
              $stmt->rowCount();
              http_response_code(200);
              echo json_encode(['message' => 'password actualizada']);
            } catch (PDOException $e) {
              echo json_encode(['message' => 'Error en el token de secretaries']);
            }
          }
        } else {
          http_response_code(400);
          echo json_encode(['message' => 'El correo no existe en la base de datos']);
        }
      }
    } catch (PDOException $e) {
      echo json_encode(['message' => 'Error en el save_new']);
    }
  }
}
