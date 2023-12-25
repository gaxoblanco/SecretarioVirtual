<?php
// Con el correo electronico actualizo el token del usuario a un token que caduca en 24hs

class user_password_restart
{
  private $conexion;
  private $email;
  private $token;
  private $token_expiration;

  public function __construct($conexion, $email)
  {
    $this->conexion = $conexion;
    $this->email = $email;
    $this->token = bin2hex(random_bytes(16));
    $this->token_expiration = date('Y-m-d H:i:s', strtotime('+1 day'));
  }

  public function passwordRestart()
  {
    // genero un has para el token
    $token_hash = bin2hex(random_bytes(32));

    try {
      try {
        // Verificar que el correo electrónico exista en la base de datos
        $sql = "SELECT id_user FROM users WHERE email = :email";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
      } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['message' => 'Error en el user-email']);
      }

      // Verificar que el correo electrónico exista en la base de datos
      // require_once './user/email_user.php';
      // $email_user = new email_user($this->conexion, $this->email);
      // $user = $email_user->email_user();

      // echo json_encode($user);
      //$user existe y tiene el id_user
      if ($user) {
        try {
          // actualizo en la tabla users el token y la fecha de expiracion
          $sql = "UPDATE users SET token = :token, token_expiration = :token_expiration WHERE id_user = :id_user";
          $stmt = $this->conexion->prepare($sql);
          $stmt->bindParam(':token', $token_hash);
          $stmt->bindParam(':token_expiration', $this->token_expiration);
          $stmt->bindParam(':id_user', $user['id_user']);

          // valido que $stmt->execute() se ejecuto correctamente
          if (!$stmt->execute()) {
            http_response_code(400);
            echo json_encode(['message' => 'Error en el token']);
            return;
          }
          $stmt->execute();

          // tomar el nombre del id_user -- falta hacer la consulta
          $name = 'usuario';

          // envio el correo electronico con el token
          require_once 'email_restart.php';
          $email = new email_restart($this->conexion, $this->email, $token_hash, $name);
          $email->write_restart();
        } catch (PDOException $e) {
          echo json_encode('Error en el token');
        }
      } else {
        // Inicializa $secretary fuera del bloque try
        $secretary = null;
        // consulto en la talba secretaries si el correo existe email_secretary
        try {
          // consulto en la talba secretaries si el correo existe
          $sql = "SELECT secreataryId FROM secretaries WHERE Semail = :Semail";
          $stmt = $this->conexion->prepare($sql);
          $stmt->bindParam(':Semail', $this->email);
          $stmt->execute();
          $secretary = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
          echo json_encode(['message' => 'Error en el secretary-email' . $e->getMessage()]);
        }

        if ($secretary) {
          try {
            // si el Semail existe actualizo el token y la fecha de expiracion
            $sql = "UPDATE secretaries SET token = :token, token_expiration = :token_expiration WHERE secreataryId = :secreataryId";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':token', $token_hash);
            $stmt->bindParam(':token_expiration', $this->token_expiration);
            $stmt->bindParam(':secreataryId', $secretary['secreataryId']);
            $stmt->execute();

            // envio el correo electronico con el token
            // echo json_encode('http://localhost:4200/reset-password?token=' . $token_hash . '&email=' . $this->email);

            // nomnbre del secretario
            $secretaryName = "usuario";
            // envio el correo electronico con el token
            require_once './email_restart.php';
            $email = new email_restart($this->conexion, $this->email, $token_hash, $secretaryName);
            $email->write_restart();

            // http_response_code(200);
            // echo json_encode('Se envio el correo electronico');
          } catch (PDOException $e) {
            echo json_encode('Error en el token de secretaries');
          }
        } else {
          http_response_code(202);
          echo json_encode("email no existe");
          return;
        }
      }
    } catch (PDOException $e) {
      echo json_encode(['message' => 'Error el correo no existe en la base de datos']);
    }
  }
}
