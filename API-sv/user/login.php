<?php

class Login
{
  private $email;
  private $password;
  private $conexion;

  public function __construct($conexion, $email, $password)
  {
    $this->conexion = $conexion;
    $this->email = $email;
    $this->password = $password;
  }

  public function loginUser()
  {
    // Verificar las credenciales del usuario
    $stmt = $this->conexion->prepare('SELECT * FROM users WHERE email = :email');
    $stmt->bindParam(':email', $this->email);
    $stmt->execute();

    // Obtener el usuario de la base de datos
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
      // El email no coincide con ningún usuario
      echo json_encode(['message' => 'Invalid email']);
      return null;
    }
    //guardar el id_user en una variable
    $id_user = $user['id_user'];

    // Verificar la contraseña proporcionada con la contraseña del usuario
    if (!password_verify($this->password, $user['password'])) {
      // Las contraseñas no coinciden
      echo json_encode(['message' => 'Invalid password']);
      return null;
    }

    // Generar un nuevo token
    $token = bin2hex(random_bytes(32));

    // Actualizar el token en la base de datos
    $updateStmt = $this->conexion->prepare('UPDATE users SET token = :token WHERE email = :email');
    $updateStmt->bindParam(':token', $token);
    $updateStmt->bindParam(':email', $this->email);
    $updateStmt->execute();

    // Retornar el token y userId al cliente en el body
    echo json_encode([
      'token' => $token,
      'id' => $id_user,
    ]);
  }
}
